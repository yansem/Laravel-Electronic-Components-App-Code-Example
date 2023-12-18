<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LogsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return
            [
                'uid'=>\Spo::user(\Spo::getFullInfoByUserId($this->uid))->getFio(),
                'text'=>$this->text,
                'id'=>$this->id,
                'created_at'=>$this->created_at->format('d.m.Y H:i:s'),
                'code_title'=>$this->code->title,
            ];
    }
}
