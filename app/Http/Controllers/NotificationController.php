<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Services\NotificationService;
use App\Services\Response;
use Illuminate\Http\Request;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use function Symfony\Component\Translation\t;

class NotificationController extends VoyagerBaseController
{
    use Response;

    public function getNotifications()
    {
        return $this->success(
            NotificationResource::collection(NotificationService::getNotifications(auth()->user()))
        );
    }
    public function read_notification(Notification $notification)
    {
        $notification->update(['is_read' => 1]);
        return $notification->is_read;
    }

    public function show_notification(Notification $notification)
    {
        $notification->update(['is_read' => 1]);
        return redirect('/detailed-tasks/' . $notification->task_id);
    }

    public function setToken(Request $request)
    {
        $request->validate(['token' => 'required']);
        auth()->user()->update(['firebase_token' => $request->token]);
        return $this->success();
    }

    public function store(Request $request)
    {
        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->addRows)->validate();
        $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());

        event(new BreadDataAdded($dataType, $data));

        if (!$request->has('_tagging')) {
            if (auth()->user()->can('browse', $data)) {
                $redirect = redirect()->route("voyager.{$dataType->slug}.index");
            } else {
                $redirect = redirect()->back();
            }

            NotificationService::sendNotification($data, $slug);

            return $redirect->with([
                'message'    => __('voyager::generic.successfully_added_new')." {$dataType->getTranslatedAttribute('display_name_singular')}",
                'alert-type' => 'success',
            ]);
        } else {
            return response()->json(['success' => true, 'data' => $data]);
        }
    }
}
