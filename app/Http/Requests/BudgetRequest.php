<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BudgetRequest extends FormRequest
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
            'amount2' => 'gt:1'
        ];
    }
    public function messages()
    {
        return [
            'amount2.gt' => __('Выберите одну из сумм в опции')
        ];
    }
    public function getValidatorInstance()
    {
        $this->cleanBudget();
        return parent::getValidatorInstance();
    }

    protected function cleanBudget()
    {
        if($this->request->has('amount2')){
            $this->merge([
                'amount2' => preg_replace('/[^0-9.]+/', '', $this->request->get('amount2'))
            ]);
        }
    }
}
