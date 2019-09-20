<?php

namespace Ipsum\Media\app\Http\Requests;


use Ipsum\Admin\app\Http\Requests\FormRequest;

class StoreMedia extends FormRequest
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
            "titre" => "required|max:255",
            "url" => "nullable|url|max:255",
        ];
    }

}
