<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;

class CustomFieldController extends VoyagerBaseController
{
    /**
     * @param Request $request
     * @return RedirectResponse
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
     * @return RedirectResponse
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
        $duration = $request->options_rowOrder ? substr($request->options_rowOrder, -1) : 0;

        $options = [];
        for ($i = 1; $i <= $duration; $i++) {
            $uz = 'options_uz_' . $i;
            $ru = 'options_ru_' . $i;
            $options['options'][$i] = $request->$uz;
            $options['options_ru'][$i] = $request->$ru;
        }
        $request->merge(['options' => $options]);
        return  $request;
    }
}
