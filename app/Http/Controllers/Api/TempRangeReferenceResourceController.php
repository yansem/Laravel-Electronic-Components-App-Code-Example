<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TempRangeRequest;
use App\Http\Resources\ItemHistoryResource;
use App\Http\Resources\TempRangeCollection;
use App\Models\History;
use App\Models\LogCodes;
use App\Models\Operation;
use App\Models\TempRangeReference;
use App\Services\JoinService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TempRangeReferenceResourceController extends Controller
{
    public function index(Request $request)
    {
        $orderColumn = request('order_column');
        $orderDirection = request('order_direction');

        $items = TempRangeReference::withFilters($request->all())
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

        return new TempRangeCollection($items);
    }

    public function store(TempRangeRequest $request)
    {
        Gate::authorize('can-edit', $this->userPermission);

        $data = $request->validated();
        $data['description'] = 'от ' . $data['min'] . ' до ' . $data['max'];
        $reference = TempRangeReference::create($data);

        History::create([
            'log_code_id' => LogCodes::TEMP_RANGES_ID,
            'user_id' => $this->userId,
            'operation_id' => Operation::ADD_ID,
            'historyable_type' => TempRangeReference::class,
            'historyable_id' => $reference->id,
            'after' => $reference
        ]);

        return $reference;
    }

    public function update(TempRangeRequest $request, $id)
    {
        Gate::authorize('can-edit', $this->userPermission);

        $reference = TempRangeReference::findOrFail($id);
        $data = $request->validated();
        $data['description'] = 'от ' . $data['min'] . ' до ' . $data['max'];
        $referenceBeforeForHistory = json_encode($reference);
        $reference->update($data);
        $referenceAfterForHistory = json_encode($reference);
        if ($referenceBeforeForHistory != $referenceAfterForHistory) {
            History::create([
                'log_code_id' => LogCodes::TEMP_RANGES_ID,
                'user_id' => $this->userId,
                'operation_id' => Operation::UPDATE_ID,
                'historyable_type' => TempRangeReference::class,
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

        $reference = TempRangeReference::findOrFail($id);
        if ($reference->secureDelete('elements')) {
            History::create([
                'log_code_id' => LogCodes::TEMP_RANGES_ID,
                'user_id' => $this->userId,
                'operation_id' => Operation::HIDE_ID,
                'historyable_type' => TempRangeReference::class,
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
            $reference = TempRangeReference::findOrFail($id);
            $refTitle = $reference->description;
            if ($reference->secureDelete('elements')) {
                History::create([
                    'log_code_id' => LogCodes::TEMP_RANGES_ID,
                    'user_id' => $this->userId,
                    'operation_id' => Operation::HIDE_ID,
                    'historyable_type' => TempRangeReference::class,
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

        $reference = TempRangeReference::withTrashed()->findOrFail($id);
        $reference->restore();
        History::create([
            'log_code_id' => LogCodes::TEMP_RANGES_ID,
            'user_id' => $this->userId,
            'operation_id' => Operation::RESTORE_ID,
            'historyable_type' => TempRangeReference::class,
            'historyable_id' => $reference->id
        ]);
        return $reference;
    }

    public function multipleRestore(Request $request)
    {
        Gate::authorize('can-edit', $this->userPermission);

        foreach ($request->idsMultiSelect as $id) {
            $reference = TempRangeReference::withTrashed()->findOrFail($id);
            $reference->restore();
            History::create([
                'log_code_id' => LogCodes::TEMP_RANGES_ID,
                'user_id' => $this->userId,
                'operation_id' => Operation::RESTORE_ID,
                'historyable_type' => TempRangeReference::class,
                'historyable_id' => $reference->id
            ]);
        }
        return response()->json(null);
    }

    public function join(Request $request, $id)
    {
        Gate::authorize('can-edit', $this->userPermission);

        $oldReference = TempRangeReference::findOrFail($id);
        $newReference = TempRangeReference::findOrFail($request->newId);

        $joinService = new JoinService($oldReference, $newReference, $this->userId);
        $joinService->join(
            [
                ['logCodeId' => LogCodes::ELEMENTS_ID, 'relation' => 'elements']
            ], LogCodes::TEMP_RANGES_ID);

        return response()->json(null);
    }

    public function multipleJoin(Request $request)
    {
        Gate::authorize('can-edit', $this->userPermission);

        $newReference = TempRangeReference::findOrFail($request->newId);
        foreach ($request->idsMultiSelect as $id) {
            $oldReference = TempRangeReference::findOrFail($id);

            $joinService = new JoinService($oldReference, $newReference, $this->userId);
            $joinService->join(
                [
                    ['logCodeId' => LogCodes::ELEMENTS_ID, 'relation' => 'elements']
                ], LogCodes::TEMP_RANGES_ID);
        }
        return response()->json(null);
    }

    public function getHistory($id)
    {
        $reference = TempRangeReference::withTrashed()->findOrFail($id);

        return ItemHistoryResource::collection($reference->histories()->get());
    }
}
