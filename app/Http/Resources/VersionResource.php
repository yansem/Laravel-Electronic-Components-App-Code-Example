<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VersionResource extends JsonResource
{
    protected $pcbCode;

    public function setPcbCode($value){
        $this->pcbCode = $value;
        return $this;
    }
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'version' => $this->version,
            'url_svn' => $this->url_svn,
            'url_stock' => config('app.stock_server') . '/ware.php#search=' . $this->pcbCode . '.' . $this->version . '&type=search',
            'url_stock_title' => $this->when($this->pcbCode, fn() => $this->pcbCode . '.' . $this->version),
            'description' => $this->description,
            'deleted_at' => $this->deleted_at,
            'element_id' => $this->when($this->element, function () {
                return $this->element->id;
            }),
            'element_stock_title' => $this->when($this->element, function () {
                return $this->element->stock_title;
            }),
        ];
    }

    public static function collection($resource){
        return new VersionCollection($resource);
    }
}
