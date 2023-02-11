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
            'phone' => 'required|numeric|min:13',
            'oplata' => 'required',
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
            'name.required' => __('Требуется заполнение!'),
            'phone.required' => __('Требуется заполнение!'),
            'description.required' => __('Требуется заполнение!'),
            'start_date.required' => __('Требуется заполнение!'),
            'date_type.required' => __('Требуется заполнение!'),
            'budget.required' => __('Требуется заполнение!'),
            'category_id.required' => __('Требуется заполнение!'),
            'oplata.required' => __('login.name.required'),
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
