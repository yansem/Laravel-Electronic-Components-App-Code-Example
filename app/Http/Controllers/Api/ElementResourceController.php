<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ElementRequest;
use App\Http\Resources\ElementCollection;
use App\Http\Resources\ElementsReferencesResource;
use App\Http\Resources\ItemHistoryResource;
use App\Models\ComponentReference;
use App\Models\Element;
use App\Models\FootprintReference;
use App\Models\History;
use App\Models\LibraryRefReference;
use App\Models\LogCodes;
use App\Models\ManufacturerReference;
use App\Models\Operation;
use App\Models\PartStatusReference;
use App\Models\TempRangeReference;
use App\Services\ElementService;
use App\Services\Program\StockService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class ElementResourceController extends Controller
{

    public function index(Request $request)
    {
        $orderColumn = request('order_column');
        $orderDirection = request('order_direction');

        $items = Element::selectWithRelations()
            ->withFilters($request->all())
            ->when($orderColumn, function ($query) use ($orderColumn, $orderDirection) {
                return $query->orderBy($orderColumn, $orderDirection);
            }, function ($query) {
                return $query->orderBy('component_title', 'asc')
                    ->orderBy('group_title', 'asc')
                    ->orderBy('category_title', 'asc');
            })
            ->paginate();

        return new ElementCollection($items);
    }

    public function store(ElementRequest $request, ElementService $elementService, StockService $ss)
    {
        Gate::authorize('can-edit', $this->userPermission);

        $stockParams = $elementService->getBarcodeStockParams($request, $ss);
        if (isset($stockParams['error'])) return response()->json([
            'errors' =>
                ['stock_barcode' => [$stockParams['error']]],
            'message' => 'The given data was invalid.'
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
        $reference = Element::create(array_merge($request->validated(), $stockParams));
        $referenceForHistory = Element::selectWithRelations([$reference->id])->get()->first()->toJson();

        History::create([
            'log_code_id' => LogCodes::ELEMENTS_ID,
            'user_id' => $this->userId,
            'operation_id' => Operation::ADD_ID,
            'historyable_type' => Element::class,
            'historyable_id' => $reference->id,
            'after' => $referenceForHistory
        ]);

        return $reference;
    }

    public function update(ElementRequest $request, ElementService $elementService,StockService $ss, $id)
    {
        Gate::authorize('can-edit', $this->userPermission);

        $reference = Element::findOrFail($id);
        $referenceBeforeForHistory = Element::selectWithRelations([$id])->get()->first()->toJson();
        $stockParams = $elementService->getBarcodeStockParams($request, $ss);
        if (isset($stockParams['error'])) return response()->json([
            'errors' =>
                ['stock_barcode' => [$stockParams['error']]],
            'message' => 'The given data was invalid.'
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
        $reference->update(array_merge($request->validated(), $stockParams));
        $referenceAfterForHistory = Element::selectWithRelations([$id])->get()->first()->toJson();
        if ($referenceBeforeForHistory !== $referenceAfterForHistory) {
            History::create([
                'log_code_id' => LogCodes::ELEMENTS_ID,
                'user_id' => $this->userId,
                'operation_id' => Operation::UPDATE_ID,
                'historyable_type' => Element::class,
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

        $reference = Element::findOrFail($id);
        $reference->delete();
        History::create([
            'log_code_id' => LogCodes::ELEMENTS_ID,
            'user_id' => $this->userId,
            'operation_id' => Operation::HIDE_ID,
            'historyable_type' => Element::class,
            'historyable_id' => $reference->id
        ]);

        return response()->json(null);
    }

    public function multipleDestroy(Request $request)
    {
        Gate::authorize('can-edit', $this->userPermission);

        foreach ($request->idsMultiSelect as $id) {
            $reference = Element::findOrFail($id);
            $reference->delete();
            History::create([
                'log_code_id' => LogCodes::ELEMENTS_ID,
                'user_id' => $this->userId,
                'operation_id' => Operation::HIDE_ID,
                'historyable_type' => Element::class,
                'historyable_id' => $reference->id
            ]);
        }
        return response()->json(null);
    }

    public function restore($id)
    {
        Gate::authorize('can-edit', $this->userPermission);

        $reference = Element::withTrashed()->findOrFail($id);
        $reference->restore();
        History::create([
            'log_code_id' => LogCodes::ELEMENTS_ID,
            'user_id' => $this->userId,
            'operation_id' => Operation::RESTORE_ID,
            'historyable_type' => Element::class,
            'historyable_id' => $reference->id
        ]);

        return response()->json(null);
    }

    public function multipleRestore(Request $request)
    {
        Gate::authorize('can-edit', $this->userPermission);

        foreach ($request->idsMultiSelect as $id) {
            $reference = Element::withTrashed()->findOrFail($id);
            $reference->restore();
            History::create([
                'log_code_id' => LogCodes::ELEMENTS_ID,
                'user_id' => $this->userId,
                'operation_id' => Operation::RESTORE_ID,
                'historyable_type' => Element::class,
                'historyable_id' => $reference->id
            ]);
        }

        return response()->json(null);
    }

    public function getHistory($id)
    {
        $reference = Element::withTrashed()->findOrFail($id);

        return ItemHistoryResource::collection($reference->histories()->get());
    }

    public function getReferences()
    {
        $references['components'] = ComponentReference::orderBy('title', 'asc')->get();
        $references['manufacturers'] = ManufacturerReference::orderBy('title', 'asc')->get();
        $references['tempRanges'] = TempRangeReference::orderBy('title', 'asc')->get();
        $references['partStatuses'] = PartStatusReference::orderBy('title', 'asc')->get();
        $references['libraryRefs'] = LibraryRefReference::orderBy('title', 'asc')->get();
        $references['footprints'] = FootprintReference::orderBy('title', 'asc')->get();

        return new ElementsReferencesResource($references);
    }

    public function updateStockData(StockService $stockService, ElementService $elementService)
    {
        Gate::authorize('can-edit', $this->userPermission);
        
        $responseArray = $elementService->updateStockData($stockService, $this->userId);
        return response()->json($responseArray);
    }
}
