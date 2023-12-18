<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\ItemHistoryResource;
use App\Models\CategoryReference;
use App\Models\History;
use App\Models\LogCodes;
use App\Models\Operation;
use App\Services\CategoryReferenceService;
use App\Services\JoinService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class CategoriesReferenceResourceController extends Controller
{
    public function index(Request $request)
    {
        $orderColumn = request('order_column');
        $orderDirection = request('order_direction');

        $items = CategoryReference::selectWithRelations()
            ->withFilters($request->all())
            ->when($orderColumn, function ($query) use ($orderColumn, $orderDirection) {
                return $query->orderBy($orderColumn, $orderDirection);
            }, function ($query) {
                return $query->orderBy('component_title', 'asc')
                    ->orderBy('group_title', 'asc')
                    ->orderBy('title', 'asc');
            })
            ->when($request->page, function ($query) {
                return $query->paginate();
            }, function ($query) {
                return $query->get();
            });

        return new CategoryCollection($items);
    }

    public function store(CategoryRequest $request)
    {
        Gate::authorize('can-edit', $this->userPermission);

        $reference = CategoryReference::create($request->validated());
        $referenceForHistory = CategoryReference::selectWithRelations([$reference->id])->get()->first()->toJson();

        History::create([
            'log_code_id' => LogCodes::CATEGORIES_ID,
            'user_id' => $this->userId,
            'operation_id' => Operation::ADD_ID,
            'historyable_type' => CategoryReference::class,
            'historyable_id' => $reference->id,
            'after' => $referenceForHistory
        ]);

        return $reference;
    }

    public function update(CategoryRequest $request, $id)
    {
        Gate::authorize('can-edit', $this->userPermission);

        $reference = CategoryReference::findOrFail($id);
        $referenceBeforeForHistory = CategoryReference::selectWithRelations([$id])->get()->first()->toJson();
        $reference->update($request->validated());
        $referenceAfterForHistory = CategoryReference::selectWithRelations([$id])->get()->first()->toJson();
        if ($referenceBeforeForHistory !== $referenceAfterForHistory) {
            History::create([
                'log_code_id' => LogCodes::CATEGORIES_ID,
                'user_id' => $this->userId,
                'operation_id' => Operation::UPDATE_ID,
                'historyable_type' => CategoryReference::class,
                'historyable_id' => $reference->id,
                'before' => $referenceBeforeForHistory,
                'after' => $referenceAfterForHistory
            ]);
        }

        return $reference;
    }

    public function destroy($id)
    {
        Gate::authorize('can-edit', $this->userPermission);

        $reference = CategoryReference::findOrFail($id);
        if ($reference->secureDelete('elements')) {
            History::create([
                'log_code_id' => LogCodes::CATEGORIES_ID,
                'user_id' => $this->userId,
                'operation_id' => Operation::HIDE_ID,
                'historyable_type' => CategoryReference::class,
                'historyable_id' => $reference->id
            ]);
            return response()->json(null);
        } else {
            return response()->json(['message' => 'Ошибка: У записи есть связи'], 500);
        }
    }

    public function multipleDestroy(Request $request)
    {
        Gate::authorize('can-edit', $this->userPermission);

        $refsWithRelations = [];
        DB::beginTransaction();
        foreach ($request->idsMultiSelect as $id) {
            $reference = CategoryReference::findOrFail($id);
            $refTitle = $reference->title;
            if ($reference->secureDelete('elements')) {
                History::create([
                    'log_code_id' => LogCodes::CATEGORIES_ID,
                    'user_id' => $this->userId,
                    'operation_id' => Operation::HIDE_ID,
                    'historyable_type' => CategoryReference::class,
                    'historyable_id' => $reference->id
                ]);
            } else {
                $refsWithRelations[] =  "\"$refTitle\"";
            }
        }
        if (empty($refsWithRelations)) {
            DB::commit();
            return response()->json(null);
        } else {
            DB::rollBack();
            $refsWithRelations = implode('<br>', $refsWithRelations);
            return response()->json(['message' => "Ошибка. <br> У следующих записей есть связи: <br> $refsWithRelations"], 500);
        }
    }

    public function restore($id)
    {
        Gate::authorize('can-edit', $this->userPermission);

        $reference = CategoryReference::withTrashed()->findOrFail($id);
        $reference->restore();

        History::create([
            'log_code_id' => LogCodes::CATEGORIES_ID,
            'user_id' => $this->userId,
            'operation_id' => Operation::RESTORE_ID,
            'historyable_type' => CategoryReference::class,
            'historyable_id' => $reference->id
        ]);

        return $reference;
    }

    public function multipleRestore(Request $request)
    {
        Gate::authorize('can-edit', $this->userPermission);

        foreach ($request->idsMultiSelect as $id) {
            $reference = CategoryReference::withTrashed()->findOrFail($id);
            $reference->restore();

            History::create([
                'log_code_id' => LogCodes::CATEGORIES_ID,
                'user_id' => $this->userId,
                'operation_id' => Operation::RESTORE_ID,
                'historyable_type' => CategoryReference::class,
                'historyable_id' => $reference->id
            ]);
        }
        return response()->json(null);
    }

    public function join(CategoryReferenceService $categoryReferenceService, Request $request, $id)
    {
        Gate::authorize('can-edit', $this->userPermission);

        $oldReference = CategoryReference::findOrFail($id);
        $newReference = CategoryReference::findOrFail($request->newId);

        $resultCheckGroup = $categoryReferenceService->checkSameGroup($oldReference, $newReference);
        if (isset($resultCheckGroup['message'])) {
            return response()->json($resultCheckGroup, 500);
        }

        $joinService = new JoinService($oldReference, $newReference, $this->userId);
        $joinService->join(
            [
                ['logCodeId' => LogCodes::ELEMENTS_ID, 'relation' => 'elements']
            ], LogCodes::CATEGORIES_ID);

        return response()->json(null);
    }

    public function multipleJoin(CategoryReferenceService $categoryReferenceService, Request $request)
    {
        Gate::authorize('can-edit', $this->userPermission);

        $newReference = CategoryReference::findOrFail($request->newId);
        foreach ($request->idsMultiSelect as $id) {
            $oldReference = CategoryReference::findOrFail($id);

            $resultCheckGroup = $categoryReferenceService->checkSameGroup($oldReference, $newReference);
            if (isset($resultCheckGroup['message'])) {
                return response()->json($resultCheckGroup, 500);
            }

            $joinService = new JoinService($oldReference, $newReference, $this->userId);
            $joinService->join(
                [
                    ['logCodeId' => LogCodes::ELEMENTS_ID, 'relation' => 'elements']
                ], LogCodes::CATEGORIES_ID);
        }
        return response()->json(null);
    }

    public function getHistory($id)
    {
        $reference = CategoryReference::withTrashed()->findOrFail($id);

        return ItemHistoryResource::collection($reference->histories()->get());
    }
}
