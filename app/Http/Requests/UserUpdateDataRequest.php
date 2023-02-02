<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateDataRequest extends FormRequest
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
        $validation = [
            'email' => 'required|email|unique:users',
            'age' => 'nullable|int',
            'phone_number' => 'required|numeric|unique:users|min:13',
            'born_date'=>'required',
            'gender'=>'nullable',
            'location' => 'nullable',
        ];
        if (auth()->user()->email == $this->request->get('email')) {
            $validation['email'] = "required|email";
        }
        if (auth()->user()->phone_number == $this->request->get('phone_number')) {
            $validation['phone_number'] = "required|min:13";
        }

        return $validation;
    }


    public function messages()
    {
        return [
            'email.email' => __('Введите адрес электронной почты в правильном формате!'),
            'email.required' => __('Требуется заполнение!'),
            'email.unique' => __('Пользователь с такой почтой уже существует!'),
            'phone_number.numeric' => __('Поле должно быть числом'),
            'phone_number.min' => __('Неверный формат номера телефона!'),
            'phone_number.required' => __('Требуется заполнение!'),
            'phone_number.unique' => __('Этот номер есть в системе!'),
            'born_date.required' => __('Требуется заполнение!'),
            'gender.nullable' => __('Проверяемое поле может быть нулевым.'),
            'age.nullable' => __('Проверяемое поле может быть нулевым.'),
            'age.int' => __('Проверяемое поле должно быть целым числом.'),
            'location.nullable' => __('Проверяемое поле может быть нулевым.')
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
                'phone_number' => str_replace(['-','_','(',')'], '', $this->request->get('phone_number'))
            ]);
        }
    }
}
