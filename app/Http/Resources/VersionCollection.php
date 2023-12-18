<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class VersionCollection extends ResourceCollection
{
    protected $pcbCode;

    public function setPcbCode($value){
        $this->pcbCode = $value;
        return $this;
    }
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request){
        return $this->collection->map(function(VersionResource $resource) use($request){
            return $resource->setPcbCode($this->pcbCode)->toArray($request);
        })->all();
    }
}
