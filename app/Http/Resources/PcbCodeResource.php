<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PcbCodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->versions->pcbCode = $this->code;
        return [
            'id' => $this->id,
            'code' => $this->code,
            'url_svn' => $this->url_svn,
            'url_svn_title' => Str::after($this->url_svn, 'https://svn.orlan.in/svn/main/'),
            'description' => $this->description,
            'url_wiki' => $this->url_wiki,
            'versions' => VersionResource::collection($this->versions)->setPcbCode($this->code),
            'deleted_at' => $this->deleted_at
        ];
    }
}
