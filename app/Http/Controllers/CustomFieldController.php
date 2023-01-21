<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CustomField;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use TCG\Voyager\Http\Controllers\VoyagerBreadController;

class CustomFieldController extends VoyagerBreadController
{
    /**
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     */
    final public function store(Request $request)
    {
        $customfield = new CustomField;
        $this->save($customfield, $request);
        return redirect('/admin/custom-fields');
    }
    final public function edit($id)
    {
        parent::edit($id);
    }

    /**
     *
     * Function  update
     * @param Request $request
     * @param $id
     * @return Application|Redirector|RedirectResponse
     */
    final public function update(Request $request, $id)
    {
        $customfield = CustomField::find($id);
        $this->save($customfield, $request);
        return redirect('/admin/custom-fields');
    }

    /**
     *
     * Function  save
     * @param $customfield
     * @param $request
     */
    public function save($customfield, $request): void
    {
        $customfield->name = $request->name;
        $customfield->title = $request->title;
        $customfield->type = $request->type;
        $customfield->required = $request->required = 'ON' ? true : false;
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
        $customfield->description = $request->description;
        $customfield->placeholder = $request->placeholder;
        $customfield->label = $request->label;

        $customfield->save();
    }
}
