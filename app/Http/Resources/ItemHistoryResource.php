<?php

namespace App\Http\Resources;

use App\Helpers\Helper;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        list($before, $after) = Helper::parseHistoryJson($this->before, $this->after);

        return
            [
                'created_at'=>$this->created_at->format('d.m.Y H:i:s'),
                'user_name'=> \Spo::user(\Spo::getUserInfoById($this->user_id))->getFio(),
                'operation_title'=>$this->operation_title,
                'before'=> $before,
                'after'=> $after
            ];
    }
}
