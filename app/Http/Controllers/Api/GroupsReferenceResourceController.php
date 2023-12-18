<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupsRequest;
use App\Http\Resources\GroupsCollection;
use App\Http\Resources\ItemHistoryResource;
use App\Models\GroupReference;
use App\Models\History;
use App\Models\LogCodes;
use App\Models\Operation;
use App\Services\GroupReferenceService;
use App\Services\JoinService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class GroupsReferenceResourceController extends Controller
{
    public function index(Request $request)
    {
        $orderColumn = request('order_column');
        $orderDirection = request('order_direction');

        $items = GroupReference::selectWithRelations()
            ->withFilters($request->all())
            ->when($orderColumn, function ($query) use ($orderColumn, $orderDirection) {
                return $query->orderBy($orderColumn, $orderDirection);
            }, function ($query) {
                return $query->orderBy('component_title', 'asc')
                    ->orderBy('title', 'asc');
            })
            ->when($request->page, function ($query) {
                return $query->paginate();
            }, function ($query) {
                return $query->get();
            });

        return new GroupsCollection($items);
    }

    public function store(GroupsRequest $request)
    {
        Gate::authorize('can-edit', $this->userPermission);

        $reference = GroupReference::create($request->validated());
        $referenceForHistory = GroupReference::selectWithRelations([$reference->id])->get()->first()->toJson();

        History::create([
            'log_code_id' => LogCodes::GROUPS_ID,
            'user_id' => $this->userId,
            'operation_id' => Operation::ADD_ID,
            'historyable_type' => GroupReference::class,
            'historyable_id' => $reference->id,
            'after' => $referenceForHistory
        ]);

        return $reference;
    }

    public function update(GroupsRequest $request, $id)
    {
        Gate::authorize('can-edit', $this->userPermission);

        $reference = GroupReference::findOrFail($id);
        $referenceBeforeForHistory = GroupReference::selectWithRelations([$id])->get()->first()->toJson();
        $reference->update($request->validated());
        $referenceAfterForHistory = GroupReference::selectWithRelations([$id])->get()->first()->toJson();
        if ($referenceBeforeForHistory !== $referenceAfterForHistory) {
            History::create([
                'log_code_id' => LogCodes::GROUPS_ID,
                'user_id' => $this->userId,
                'operation_id' => Operation::UPDATE_ID,
                'historyable_type' => GroupReference::class,
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

        $reference = GroupReference::findOrFail($id);
        if ($reference->secureDelete('elements', 'categories')) {
            History::create([
                'log_code_id' => LogCodes::GROUPS_ID,
                'user_id' => $this->userId,
                'operation_id' => Operation::HIDE_ID,
                'historyable_type' => GroupReference::class,
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
            $reference = GroupReference::findOrFail($id);
            $refTitle = $reference->title;
            if ($reference->secureDelete('elements', 'categories')) {
                History::create([
                    'log_code_id' => LogCodes::GROUPS_ID,
                    'user_id' => $this->userId,
                    'operation_id' => Operation::HIDE_ID,
                    'historyable_type' => GroupReference::class,
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

        $reference = GroupReference::withTrashed()->findOrFail($id);
        $reference->restore();
        History::create([
            'log_code_id' => LogCodes::GROUPS_ID,
            'user_id' => $this->userId,
            'operation_id' => Operation::RESTORE_ID,
            'historyable_type' => GroupReference::class,
            'historyable_id' => $reference->id
        ]);

        return $reference;
    }

    public function multipleRestore(Request $request)
    {
        Gate::authorize('can-edit', $this->userPermission);

        foreach ($request->idsMultiSelect as $id) {
            $reference = GroupReference::withTrashed()->findOrFail($id);
            $reference->restore();
            History::create([
                'log_code_id' => LogCodes::GROUPS_ID,
                'user_id' => $this->userId,
                'operation_id' => Operation::RESTORE_ID,
                'historyable_type' => GroupReference::class,
                'historyable_id' => $reference->id
            ]);
        }

        return response()->json(null);
    }

    public function join(GroupReferenceService $groupReferenceService, Request $request, $id)
    {
        Gate::authorize('can-edit', $this->userPermission);

        $oldReference = GroupReference::findOrFail($id);
        $newReference = GroupReference::findOrFail($request->newId);

        $resultCheckComponent = $groupReferenceService->checkSameComponent($oldReference, $newReference);
        if (isset($resultCheckComponent['message'])) {
            return response()->json($resultCheckComponent, 500);
        }

        $joinService = new JoinService($oldReference, $newReference, $this->userId);
        $joinService->join(
            [
                ['logCodeId' => LogCodes::ELEMENTS_ID, 'relation' => 'elements'],
                ['logCodeId' => LogCodes::CATEGORIES_ID, 'relation' => 'categories']
            ], LogCodes::GROUPS_ID);

        return response()->json(null);
    }

    public function multipleJoin(GroupReferenceService $groupReferenceService, Request $request)
    {
        Gate::authorize('can-edit', $this->userPermission);

        $newReference = GroupReference::findOrFail($request->newId);
        foreach ($request->idsMultiSelect as $id) {
            $oldReference = GroupReference::findOrFail($id);

            $resultCheckComponent = $groupReferenceService->checkSameComponent($oldReference, $newReference);
            if (isset($resultCheckComponent['message'])) {
                return response()->json($resultCheckComponent, 500);
            }

            $joinService = new JoinService($oldReference, $newReference, $this->userId);
            $joinService->join(
                [
                    ['logCodeId' => LogCodes::ELEMENTS_ID, 'relation' => 'elements'],
                    ['logCodeId' => LogCodes::CATEGORIES_ID, 'relation' => 'categories']
                ], LogCodes::GROUPS_ID);
        }
        return response()->json(null);
    }

    public function getHistory($id)
    {
        $reference = GroupReference::withTrashed()->findOrFail($id);

        return ItemHistoryResource::collection($reference->histories()->get());
    }
}
