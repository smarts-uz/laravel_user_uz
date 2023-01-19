<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use TCG\Voyager\Http\Controllers\VoyagerBreadController;

class CustomFieldController extends VoyagerBreadController
{
    /**
     * @param Request $request
     * @param string $table
     * @return void
     */
    final public function store(Request $request)
    {
        parent::store($request);
    }
    final public function edit($id)
    {
        parent::edit($id);
    }
    /**
     * @param string $table
     * @return void
     */
    final public function update(Request $request, $id)
    {
        $customfield = CustomField::find($id);

        $uz = isset($customfield->options) ? $customfield->options["options"] : null;
        $ru = isset($customfield->options_ru) ? $customfield->options_ru["options"] : null;
        foreach(explode(',',$request->language_rowOrder) as $value)
        {
            $b = 'language_uz_'.$value;
            $uz[] = $request->$b;
        }
        foreach(explode(',',$request->language_rowOrder) as $value)
        {
            $b = 'language_ru_'.$value;
            $ru[] = $request->$b;
        }
        $uz = ['options' => collect($this->startfrom1($uz))];
        $customfield->options = $uz;
        $ru = ['options' => collect($this->startfrom1($ru))];
        $customfield->options_ru = $ru;
        $customfield->save();
        dd($request);
        parent::update($request,$id);
    }
    final public function startfrom1($variable)
    {
        array_unshift($variable,"");
        unset($variable[0]);
        return $variable;
    }
}
