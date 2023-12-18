<?php

namespace App\Http\Resources;

use App\Helpers\Helper;
use Illuminate\Http\Resources\Json\JsonResource;

class HistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        list($before, $after) = Helper::parseHistoryJson($this->before, $this->after);

        return
            [
                'created_at' => $this->created_at->format('d.m.Y H:i:s'),
                'user_name' => $this->user_id
                    ? \Spo::user(\Spo::getUserInfoById($this->user_id))->getFio()
                    : __('scheduled synchronization'),
                'log_code_title' => $this->log_code_title,
                'historyable_title' => $this->historyable->title ?? $this->historyable->id,
                'operation_title' => $this->operation_title,
                'before' => $before,
                'after' => $after
            ];
    }
}
