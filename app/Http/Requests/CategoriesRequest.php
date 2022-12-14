<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoriesRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'category_id' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'category_id.required' => __('Требуется заполнение!')
        ];
    }

    public function getValidatorInstance()
    {
        $this->cleanCategory();
        return parent::getValidatorInstance();
    }

    protected function cleanCategory()
    {
        if($this->request->has('category_id')){
            $this->merge([
                'category_id' => str_replace(['[',']'], '', $this->request->get('category_id'))
            ]);
        }
    }

}
