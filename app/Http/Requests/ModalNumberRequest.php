<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ModalNumberRequest extends FormRequest
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
            'phone_number' => 'required|unique:users|min:13'
        ];
    }
    public function messages()
    {
        return  [
            'phone_number.required' => __('Требуется заполнение!'),
            'phone_number.unique' => __('Этот номер есть в системе!'),
            'phone_number.min' => __('Неверный формат номера телефона!'),
        ];
    }
    public function getValidatorInstance(): Validator
    {
        $this->cleanPhoneNumber();
        return parent::getValidatorInstance();
    }

    protected function cleanPhoneNumber(): void
    {
        if($this->request->has('phone_number')){
            $this->merge([
                'phone_number' => str_replace(['-','_','(',')'], '', $this->request->get('phone_number'))
            ]);
        }
    }
}
