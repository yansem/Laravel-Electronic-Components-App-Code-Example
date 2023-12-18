<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VersionRequest;
use App\Http\Resources\VersionResource;
use App\Models\PcbCode;
use App\Models\Version;
use Illuminate\Support\Facades\Gate;

class VersionController extends Controller
{
    public function store(VersionRequest $request)
    {
        
        Gate::authorize('can-edit', $this->userPermission);
        
        $data = $request->validated();

        if (PcbCode::find($request->pcb_code_id) === null) {
            return response()->json(['message' => 'Код платы не найден. Попробуйте обновить страницу'], 500);
        }

        $version = Version::create($data);

        return new VersionResource($version);
    }

    public function update(VersionRequest $request, $id)
    {
        
        Gate::authorize('can-edit', $this->userPermission);
        
        $version = Version::findOrFail($id);
        $data = $request->validated();
        $version->update($data);

        return new VersionResource($version);
    }

    public function destroy($id)
    {
        
        Gate::authorize('can-edit', $this->userPermission);
        
        $version = Version::findOrFail($id);
        $version->delete();

        return new VersionResource($version);
    }

    public function restore($id)
    {
        
        Gate::authorize('can-edit', $this->userPermission);
        
        $version = Version::withTrashed()->findOrFail($id);

        if ($version->pcb_code === null) {
            return response()->json(['message' => 'Код платы не найден. Попробуйте обновить страницу'], 500);
        }
        $version->restore();

        return new VersionResource($version);
    }
}
