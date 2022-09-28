<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ProfileVideoRequest extends FormRequest
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
            'link' => 'required|url'
        ];
    }

    public function messages()
    {
        return [
            'link.required' => trans('trans.Enter link.'),
            'link.url' => trans('trans.Link should be type of url.'),
        ];
    }
}
