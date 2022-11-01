<?php

namespace Modules\SupportChat\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'name' => 'required',
            'phone_number' =>  'numeric|min:13'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('Требуется заполнение!'),
            'phone_number.numeric' => __('Поле должно быть числом'),
            'phone_number.min' => __('Неверный формат номера телефона!'),
        ];
    }
    public function getValidatorInstance()
    {
        $this->cleanPhoneNumber();
        return parent::getValidatorInstance();
    }

    protected function cleanPhoneNumber()
    {
        if($this->request->has('phone_number')){
            $this->merge([
                'phone_number' => str_replace(['-','(',')'], '', $this->request->get('phone_number'))
            ]);
        }
    }
}
