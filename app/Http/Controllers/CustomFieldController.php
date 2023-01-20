<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
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
        $duration =  substr($request->Options_rowOrder, -1) ;
        $options_uz = [];
        $options_ru = [];
        
        for ($i = 1; $i <= $duration; $i++) {
            $uz = 'Options_uz_' . $i;
            $ru = 'Options_ru_' . $i;
            $options_uz['options'][] = $request->$uz;
            $options_ru['options'][] = $request->$ru;
        }

        $customfield->options = $options_uz;
        $customfield->options_ru = $options_ru;
        $customfield->save();
    }
}
