<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskIndexResource;
use App\Models\Task;
use App\Models\TaskResponse;
use App\Models\WalletBalance;
use Illuminate\Http\Request;

class ResponseAPIController extends Controller
{


    public function store(Request $request, Task $task)
    {

        $data = $request->validate([
            'description' => 'required|string',
            'price' => 'required|int',
            'notificate' => 'required',
            'pay' => 'required'
        ]);
        $data['notification_on'] = $request->notificate ? 1 : 0;
        $data['task_id'] = $task->id;
        $data['user_id'] = $task->user_id;
        $data['creator_id'] = auth()->user()->id;
        if ($request->pay === 0) {
            $data['not_free'] = 0;
        } else {
            $data['not_free'] = 1;
        }

        $ballance = WalletBalance::where('user_id', auth()->user()->id)->first();
        if ($ballance) {
            if ($ballance->balance < setting('admin.min_amount')) {
                response()->json(["success" => false, 'message'=>'Hisobingizda yetarli mablag\' mavjud emas!']);
            }else if($task->responses()->where('creator_id', auth()->user()->id)->first()){
                return response()->json(['success' => false, "Siz allaqachon so'rov yuborgansiz"]);
            } else {
                $ballance->balance = $ballance->balance - $request->pay;
                $ballance->save();
                TaskResponse::create($data);
                return new TaskIndexResource($task);
            }
        } else {

            response()->json(["success" => false, 'message'=>'Hisobingizda yetarli mablag\' mavjud emas!']);
        }
        return response()->json($task); //back();

    }


    /**
     * @OA\Post(
     *     path="/api/select-performer/{response}",
     *     tags={"Responses"},
     *     summary="Select performer",
     *     @OA\Parameter (
     *          in="path",
     *          name="response",
     *          required=true,
     *          @OA\Schema (
     *              type="string"
     *          )
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="Successful operation"
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     ),
     *     security={
     *         {"token": {}}
     *     },
     * )
     */
    public function selectPerformer(TaskResponse $response)
    {
        taskGuard($response->task);
        if ($response->task->status >= 3) {
            return response()->json(["success" => false, 'message' => 'Ish hali bajarilmoqda']);
        }
        $data = [
            'performer_id' => $response->user_id,
            'status' => Task::STATUS_IN_PROGRESS
        ];
        $response->task->update($data);
        return response()->json(["success" => true]);
    }


}
