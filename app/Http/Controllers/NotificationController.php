<?php

namespace App\Http\Controllers;

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
    public function index(Request $request)
    {
        return $this->success(NotificationService::getNotifications(auth()->user()));
    }
    public function read_notification(Notification $notification)
    {
//        $notification->is_read = 1;
        $notification->update(['is_read' => 1]);
        return $notification->is_read;
    }

    public function show_notification(Notification $notification)
    {
//        $notification->is_read = 1;
        $notification->update(['is_read' => 1]);
        return redirect('/detailed-tasks/' . $notification->task_id);
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
