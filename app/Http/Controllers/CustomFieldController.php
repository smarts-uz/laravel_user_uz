<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CustomField;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use TCG\Voyager\Http\Controllers\VoyagerBreadController;

class CustomFieldController extends VoyagerBaseController
{
    /**
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     */
    final public function store(Request $request)
    {
        $request = $this->save($request);
        return parent::store($request);
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
        $request = $this->save($request);
        return parent::update($request, $id);
    }

    /**
     *
     * Function  save
     * @param $customfield
     * @param $request
     */
    public function save($request)
    {
        $duration =  substr($request->options_rowOrder, -1);

        $options_uz = [];
        $options_ru = [];
        for ($i = 1; $i <= $duration; $i++) {
            $uz = 'options_uz_' . $i;
            $ru = 'options_ru_' . $i;
            $options_uz['options'][$i] = $request->$uz;
            $options_ru['options'][$i] = $request->$ru;
        }
        $request->merge([
            'options' => $options_uz,
            'options_ru' => $options_ru,
        ]);
        return  $request;
    }
}
