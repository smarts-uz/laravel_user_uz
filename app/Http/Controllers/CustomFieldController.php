<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CustomField;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use TCG\Voyager\Http\Controllers\VoyagerBreadController;

class CustomFieldController extends VoyagerBreadController
{
    /**
     * @param Request $request
     * @return void
     */
    final public function store(Request $request): void
    {
        parent::store($request);
    }
    final public function edit($id)
    {
        parent::edit($id);
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    final public function update(Request $request, $id): RedirectResponse
    {
        $customfield = CustomField::find($id);

        $customfield->name = $request->neme;
        $customfield->title = $request->title;
        $customfield->type = $request->type;
        $customfield->required = $request->required;
        $customfield->data_type = $request->data_type;
        $customfield->regex = $request->regex;
        $customfield->min = $request->min;
        $customfield->max = $request->max;

        //AppendGrid Options
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
        //AppendGrid Options end

        $customfield->values = $request->values;
        $customfield->category_id = $request->category_id;
        $customfield->route = $request->route;
        $customfield->order = $request->order;
        $customfield->placeholder = $request->placeholder;
        $customfield->label = $request->label;

        $customfield->save();
        return redirect('/admin/custom-fields');
    }
}
