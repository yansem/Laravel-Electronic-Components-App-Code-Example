<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManufacturerRequest;
use App\Http\Resources\ItemHistoryResource;
use App\Http\Resources\ManufacturersCollection;
use App\Models\History;
use App\Models\LogCodes;
use App\Models\ManufacturerReference;
use App\Models\Operation;
use App\Services\JoinService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ManufacturersReferenceResourceController extends Controller
{
    public function index(Request $request)
    {
        $orderColumn = request('order_column');
        $orderDirection = request('order_direction');

        $items = ManufacturerReference::withFilters($request->all())
            ->when($orderColumn, function ($query) use ($orderColumn, $orderDirection) {
                return $query->orderBy($orderColumn, $orderDirection);
            }, function ($query) {
                return $query->orderBy('title', 'asc');
            })
            ->when($request->page, function ($query) {
                return $query->paginate();
            }, function ($query) {
                return $query->get();
            });

        return new ManufacturersCollection($items);
    }

    public function store(ManufacturerRequest $request)
    {
        Gate::authorize('can-edit', $this->userPermission);

        $reference = ManufacturerReference::create($request->validated());

        History::create([
            'log_code_id' => LogCodes::MANUFACTURERS_ID,
            'user_id' => $this->userId,
            'operation_id' => Operation::ADD_ID,
            'historyable_type' => ManufacturerReference::class,
            'historyable_id' => $reference->id,
            'after' => $reference
        ]);

        return $reference;
    }

    public function update(ManufacturerRequest $request, $id)
    {
        Gate::authorize('can-edit', $this->userPermission);

        $reference = ManufacturerReference::findOrFail($id);
        $referenceBeforeForHistory = json_encode($reference);
        $reference->update($request->validated());
        $referenceAfterForHistory = json_encode($reference);
        if ($referenceBeforeForHistory != $referenceAfterForHistory) {
            History::create([
                'log_code_id' => LogCodes::MANUFACTURERS_ID,
                'user_id' => $this->userId,
                'operation_id' => Operation::UPDATE_ID,
                'historyable_type' => ManufacturerReference::class,
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

        $reference = ManufacturerReference::findOrFail($id);
        if ($reference->secureDelete('elements')) {
            History::create([
                'log_code_id' => LogCodes::MANUFACTURERS_ID,
                'user_id' => $this->userId,
                'operation_id' => Operation::HIDE_ID,
                'historyable_type' => ManufacturerReference::class,
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
            $reference = ManufacturerReference::findOrFail($id);
            $refTitle = $reference->title;
            if ($reference->secureDelete('elements')) {
                History::create([
                    'log_code_id' => LogCodes::MANUFACTURERS_ID,
                    'user_id' => $this->userId,
                    'operation_id' => Operation::HIDE_ID,
                    'historyable_type' => ManufacturerReference::class,
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

        $reference = ManufacturerReference::withTrashed()->findOrFail($id);
        $reference->restore();

        History::create([
            'log_code_id' => LogCodes::MANUFACTURERS_ID,
            'user_id' => $this->userId,
            'operation_id' => Operation::RESTORE_ID,
            'historyable_type' => ManufacturerReference::class,
            'historyable_id' => $reference->id
        ]);

        return $reference;
    }

    public function multipleRestore(Request $request)
    {
        Gate::authorize('can-edit', $this->userPermission);

        foreach ($request->idsMultiSelect as $id) {
            $reference = ManufacturerReference::withTrashed()->findOrFail($id);
            $reference->restore();

            History::create([
                'log_code_id' => LogCodes::MANUFACTURERS_ID,
                'user_id' => $this->userId,
                'operation_id' => Operation::RESTORE_ID,
                'historyable_type' => ManufacturerReference::class,
                'historyable_id' => $reference->id
            ]);
        }

        return response()->json(null);
    }

    public function join(Request $request, $id)
    {
        Gate::authorize('can-edit', $this->userPermission);

        $oldReference = ManufacturerReference::findOrFail($id);
        $newReference = ManufacturerReference::findOrFail($request->newId);

        $joinService = new JoinService($oldReference, $newReference, $this->userId);
        $joinService->join(
            [
                ['logCodeId' => LogCodes::ELEMENTS_ID, 'relation' => 'elements']
            ], LogCodes::MANUFACTURERS_ID);

        return response()->json(null);
    }

    public function multipleJoin(Request $request)
    {
        Gate::authorize('can-edit', $this->userPermission);

        $newReference = ManufacturerReference::findOrFail($request->newId);
        foreach ($request->idsMultiSelect as $id) {
            $oldReference = ManufacturerReference::findOrFail($id);
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
        $reference = ManufacturerReference::withTrashed()->findOrFail($id);

        return ItemHistoryResource::collection($reference->histories()->get());
    }
}
