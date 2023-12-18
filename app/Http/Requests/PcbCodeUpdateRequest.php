<?php

namespace App\Http\Requests;

use App\Rules\WithoutCyrillic;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PcbCodeUpdateRequest extends PcbCodeStoreRequest
{
    public function rules()
    {
        $rules = parent::rules();

        $rules['url_svn'] = ['required', 'max:255', 'url'];

        return $rules;
    }

    public function attributes()
    {
        $attributes = parent::attributes();

        $attributes['url_svn'] = 'SVN';

        return $attributes;
    }
}
