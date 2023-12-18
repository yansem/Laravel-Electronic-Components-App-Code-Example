<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ElementsReferencesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'components' => new ComponentReferenceCollection($this['components']),
            'manufacturers' => new ManufacturersCollection($this['manufacturers']),
            'tempRanges' => new TempRangeCollection($this['tempRanges']),
            'partStatuses' => new PartStatusesCollection($this['partStatuses']),
            'libraryRefs' => new LibraryRefReferencesCollection($this['libraryRefs']),
            'footprints' => new FootprintsReferencesCollection($this['footprints'])
        ];
    }
}
