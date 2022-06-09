<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PortfolioRequest extends FormRequest
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
            'comment' => 'required|string',
            'description' => 'required|string',
            'images.*' => 'nullable|mimes:csv,txt,xlx,xls,pdf,jpg,png,svg,jpeg,bmp'
        ];
    }

    public function messages()
    {
        return [
            'comment.required' => trans('trans.Enter comment.'),
            'comment.string' => trans('trans.Comment should be in text format.'),
            'description.required' => trans('trans.Enter description.'),
            'description.string' => trans('trans.Description should be in text format.'),
        ];
    }
}
