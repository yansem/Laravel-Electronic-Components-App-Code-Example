<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PcbCodeStoreRequest;
use App\Http\Requests\PcbCodeUpdateRequest;
use App\Http\Resources\PcbCodeElementsResource;
use App\Http\Resources\PcbCodeResource;
use App\Models\Element;
use App\Models\PcbCode;
use App\Models\Version;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PcbCodeController extends Controller
{
    public function index()
    {
        $pcbCodes = PcbCode::withTrashed()
            ->with('versions.element')
            ->orderBy('deleted_at')
            ->orderBy('code', 'desc')
            ->get();
        return (PcbCodeResource::collection($pcbCodes));
    }

    public function store(PcbCodeStoreRequest $request)
    {
        Gate::authorize('can-edit', $this->userPermission);

        $data = $request->validated();
        $data['url_svn'] = PcbCode::URL_SVN . $data['code'];
        $data['url_wiki'] = PcbCode::URL_WIKI . mb_strtolower($data['code']);
        $pcbCode = PcbCode::create($data);
        Version::create(
            [
                'pcb_code_id' => $pcbCode->id,
                'version' => 1
            ]
        );
        $pcbCode->load('versions.element');

        return new PcbCodeResource($pcbCode);
    }

    public function update(PcbCodeUpdateRequest $request, $id)
    {
        Gate::authorize('can-edit', $this->userPermission);

        $pcbCode = PcbCode::findOrFail($id);
        $data = $request->validated();
        $data['url_wiki'] = PcbCode::URL_WIKI . mb_strtolower($data['code']);
        $pcbCode->update($data);
        $pcbCode->load('versions.element');

        return new PcbCodeResource($pcbCode);
    }

    public function destroy($id)
    {
        Gate::authorize('can-edit', $this->userPermission);

        $pcbCode = PcbCode::findOrFail($id);
        $pcbCode->delete();
        $pcbCode->versions()->delete();
        $pcbCode->load('versions.element');

        return new PcbCodeResource($pcbCode);
    }

    public function restore($id)
    {
        Gate::authorize('can-edit', $this->userPermission);

        $pcbCode = PcbCode::withTrashed()->findOrFail($id);
        $pcbCode->restore();
        $pcbCode->load('versions.element');

        return new PcbCodeResource($pcbCode);
    }

    public function elements(Request $request)
    {
        $elements = Element::where('stock_title', 'LIKE', '%' . $request->url_stock_title . '%')->orderBy('stock_title')->get();

        return PcbCodeElementsResource::collection($elements);
    }
}
