<?php

namespace App\Http\Requests;

use App\Rules\StockBarcode;
use Illuminate\Foundation\Http\FormRequest;

class ElementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'component_ref_id' => 'required|exists:App\Models\ComponentReference,id,deleted_at,NULL',
            'group_ref_id' => 'nullable|exists:App\Models\GroupReference,id,deleted_at,NULL',
            'category_ref_id' => 'nullable|exists:App\Models\CategoryReference,id,deleted_at,NULL',
            'manufacturer_id' => 'nullable|exists:App\Models\ManufacturerReference,id,deleted_at,NULL',
            'part_number' => 'nullable|max:255',
            'part_status_id' => 'required|exists:App\Models\PartStatusReference,id,deleted_at,NULL',
            'component_name' => 'nullable|max:255',
            'library_ref_id' => 'required|exists:App\Models\LibraryRefReference,id,deleted_at,NULL',
            'footprint_ref1_id' => 'required|exists:App\Models\FootprintReference,id,deleted_at,NULL',
            'footprint_ref2_id' => 'nullable|exists:App\Models\FootprintReference,id,deleted_at,NULL',
            'footprint_ref3_id' => 'nullable|exists:App\Models\FootprintReference,id,deleted_at,NULL',
            'temp_range_id' => 'nullable|exists:App\Models\TempRangeReference,id,deleted_at,NULL',
            'comment' => 'nullable|max:255',
            'description' => 'nullable|max:255',
            'help_url' => 'nullable|max:255|regex:/^https?:\/\/*/',
            'part_count' => 'required|integer|max:4294967295|gt:0',
            'stock_barcode' => ['nullable', 'integer', 'gt:0']
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'component_ref_id' => __('Component'),
            'group_ref_id' => __('Group'),
            'category_ref_id' => __('Category'),
            'manufacturer_id' => __('Manufacturer'),
            'part_status_id' => __('Part status'),
            'component_name' => __('Component name'),
            'library_ref_id' => __('Library ref'),
            'footprint_ref1_id' => __('Footprint 1'),
            'footprint_ref2_id' => __('Footprint 2'),
            'footprint_ref3_id' => __('Footprint 3'),
            'temp_range' => __('Temp range'),
            'comment' => __('Comment'),
            'description' => __('Description'),
            'help_url' => __('Help URL'),
            'part_count' => __('Part count'),
            'part_number' => __('Part number'),
            'stock_barcode' => __('Stock barcode')
        ];
    }

    public function messages()
    {
        return [
            'help_url.regex' => __('The link must start with http:// or https://')
        ];
    }
}
