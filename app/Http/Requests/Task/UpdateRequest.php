<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
        $rule = [
            'name' => 'required',
            'date_type' => 'required',
            'budget' => 'required',
            'description' => 'required',
            'category_id' => 'required|numeric',
            'photos' => 'nullable|array',
            'phone' => 'required|numeric|min:13'
        ];
        $rule = $this->dateRule($rule);
        return $rule;
    }

    public function dateRule($rule)
    {
        switch($this->get('date_type')) {
            case 1:
                $rule['start_date'] = 'required|date';
                $rule['date_type'] = 'required';
                break;
            case 2:
                $rule['end_date'] = 'required|date';
                $rule['date_type'] = 'required';
                break;
            case 3:
                $rule['start_date'] = 'required|date';
                $rule['end_date'] = 'required|date';
                $rule['date_type'] = 'required';
                break;

        }
        return $rule;

    }
    public function messages()
    {
        return [
            'name.required' => 'Напишите имю',
            'phone.required' => 'Пополните полю',
            'description.required' => 'Пополните полю',
            'start_date.required' => 'Пополните полю',
            'date_type.required' => 'Пополните полю',
            'budget.required' => 'Пополните полю',
            'category_id.required' => 'Пополните полю',
        ];
    }
    public function getValidatorInstance()
    {
        $this->cleanPhoneNumber();
        return parent::getValidatorInstance();
    }

    protected function cleanPhoneNumber()
    {
        if($this->request->has('phone')){
            $this->merge([
                'phone' => str_replace(['-','(',')'], '', $this->request->get('phone'))
            ]);
        }
    }
}
