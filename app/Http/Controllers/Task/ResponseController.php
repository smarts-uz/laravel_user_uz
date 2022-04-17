<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskResponse;
use App\Models\WalletBalance;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use PlayMobile\SMS\SmsService;
use RealRashid\SweetAlert\Facades\Alert;

class ResponseController extends Controller
{


    public function store(Request $request, Task $task)
    {
        if ($task->user_id == auth()->user()->id)
            abort(403);
        $data = $request->validate([
            'description' => 'required|string',
            'price' => 'required|int',
            'notificate' => 'nullable',
            'pay' => 'required',
        ]);
        $data['notificate'] = $request->notificate ? 1 : 0;
        $data['task_id'] = $task->id;
        $data['user_id'] = $task->user_id;
        $data['performer_id'] = auth()->user()->id;
        if ($request->pay == 0) {
            $data['not_free'] = 0;
        } else {
            $data['not_free'] = 1;
        }

        $ballance = WalletBalance::where('user_id', auth()->user()->id)->first();
        if ($ballance) {
            if ($ballance->balance < 4000) {
                Alert::error('Hisobingizda yetarli mablag\' mavjud emas!');
            }else if($task->responses()->where('performer_id', auth()->user()->id)->first()){
                Alert::error("Balance", 'Allaqachon mavjud!');

            } else {
                Alert::success("Success", 'asdweqweqw');
                $ballance->balance = $ballance->balance - $request->pay;
                $ballance->save();
//                dd($data);
                $task_response = TaskResponse::create($data);
//                dd($task_response);

                NotificationService::sendTaskSelectedNotification($task);
            }
        } else {

            Alert::error("Balance", 'Hisobingizda yetarli mablag\' mavjud emas!');

        }
        return back();

    }


    public function selectPerformer(TaskResponse $response)
    {
        if ($response->task->status >= 3 && $response->task->resonses_count) {
            abort(403);
        }
        $data = [
            'performer_id' => $response->performer_id,
            'status' => Task::STATUS_IN_PROGRESS
        ];
        $response->task->update($data);
        $performer = $response->performer;
        if ($response->user->phone_numer)
        {
            $name = $response->user->name;
            $phone = $response->user->phone_number;
            $text = "Vi ispolnitel v zadanii user.uz/detailed-tasks/$response->task_id. Kontakt zakazchika: $name. $phone";
            (new SmsService())->send($response->user->phone_numer, $text);

        }
        $data = [
            'performer_name' => $performer->name,
            'performer_phone' => $performer->phone_number,
            'performer_description' => $performer->description,
            'performer_avatar' => asset('storage/'.$performer->avatar),
        ];
        return back()->with(['success' => true,'data' => $data]);
    }

}
