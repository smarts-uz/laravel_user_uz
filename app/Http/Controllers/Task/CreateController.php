<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LoginController;
use App\Http\Requests\TaskDateRequest;
use App\Http\Requests\UserPhoneRequest;
use App\Http\Requests\UserRequest;
use App\Models\Category;
use App\Models\CustomField;
use App\Models\CustomFieldsValue;
use App\Models\Task;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\Task\CreateService;
use App\Services\Task\CustomFieldService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PlayMobile\SMS\SmsService;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\Notification;
use App\Models\Address;

class CreateController extends Controller
{
    protected $service;
    protected $custom_field_service;

    public function __construct()
    {
        $this->service = new CreateService();
        $this->custom_field_service = new CustomFieldService();
    }


    public function name(Request $request)
    {

        $current_category = Category::findOrFail($request->category_id);

        return view("create.name", compact('current_category'));


    }

    public function name_store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required'
        ]);
        $task = Task::create($data);
        $this->service->attachCustomFieldsByRoute($task, CustomField::ROUTE_NAME, $request);

        return redirect()->route("task.create.custom.get", $task->id);
    }


    public function custom_get(Task $task)
    {

        $custom_fields = $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_CUSTOM);
        if (!$task->category->customFieldsInCustom->count()) {
            if ($task->category->parent->remote){
                return redirect()->route("task.create.remote", $task->id);
            }
            return redirect()->route('task.create.address', $task->id);
        }

        return view('create.custom', compact('task','custom_fields'));

    }

    public function custom_store(Request $request, Task $task)
    {
        $this->service->attachCustomFieldsByRoute($task, CustomField::ROUTE_CUSTOM, $request);

        if ($task->category->parent->remote){
            return redirect()->route("task.create.remote", $task->id);
        }

        return redirect()->route('task.create.address', $task->id);
    }

    public function remote_get(Task $task){
        return view('create.remote', compact('task'));
    }

    public function remote_store(Request $request,Task $task)
    {
        $data = $request->validate(['radio' => 'required']);

        if ($data['radio'] === 'address')
        {
            return redirect()->route("task.create.address", $task->id);
        }

        if ($data['radio'] === 'remote')
        {
            $task->remote = 1;
            $task->save();
            return redirect()->route("task.create.date", $task->id);
        }

        return back();
    }

    public function address(Task $task)
    {
        $custom_fields = $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_ADDRESS);
        return view('create.location', compact('task','custom_fields'));

    }

    public function address_store(Request $request, Task $task)
    {

        $task->update(['coordinates' => $this->service->addAdditionalAddress($task, $request)]);
        $this->service->attachCustomFieldsByRoute($task, CustomField::ROUTE_ADDRESS, $request);
        return redirect()->route("task.create.date", $task->id);

    }

    public function date(Task $task)
    {
        $custom_fields = $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_DATE);
        return view('create.date', compact('task','custom_fields'));

    }

    public function date_store(TaskDateRequest $request, Task $task)
    {
        $data = $request->validated();
        $task->update($data);
        $this->service->attachCustomFieldsByRoute($task, CustomField::ROUTE_DATE, $request);

        return redirect()->route('task.create.budget', $task->id);
    }

    public function budget(Task $task)
    {
        $category = Category::findOrFail($task->category_id);
        $custom_fields = $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_BUDGET);

        return view('create.budget', compact('task', 'category','custom_fields'));
    }

    public function budget_store(Task $task, Request $request)
    {
        $task->budget = preg_replace('/[^0-9.]+/', '', $request->amount1);
        $task->save();
        $this->service->attachCustomFieldsByRoute($task, CustomField::ROUTE_BUDGET, $request);


        return redirect()->route('task.create.note', $task->id);

    }

    public function note(Task $task)
    {
        $custom_fields = $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_NOTE);

        return view('create.notes', compact('task','custom_fields'));
    }

    public function images_store(Request $request)
    {
        $imgData = session()->has('images') ? json_decode(session('images')):[];
        foreach ($request->file('images') as $uploadedImage) {
            $filename = time() . '_' .$uploadedImage->getClientOriginalName();
            $uploadedImage->move(public_path("storage/uploads/"), $filename);
            $imgData[] = $filename;
        }
        session()->put('images', json_encode($imgData));
    }
    public function note_store(Task $task, Request $request)
    {
        $data = $request->validate([
            'description' => 'required|string',
            'oplata' => 'required',
        ]);
        if ($request['docs'] === "on") {
            $data['docs'] = 1;
        } else {
            $data['docs'] = 0;
        }
        $data['photos'] = session()->has('images') ? session('images') : '[]';

        session()->forget('images');
        $task->update($data);
        return redirect()->route("task.create.contact", $task->id);
    }


    public function contact(Task $task)
    {
        $custom_fields = $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_CONTACTS);

        return view('create.contacts', compact('task','custom_fields'));
    }


    public function contact_store(Task $task, Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'phone_number' => 'required|integer|min:13|unique:users,phone_number,' . $user->id
        ]);
        /*if (!$user->is_phone_number_verified || $user->phone_number != $data['phone_number']) {*/
        if (!$user->is_phone_number_verified && $user->phone_number == $data['phone_number']) {
            $data['is_phone_number_verified'] = 0;
            $user->update($data);
            LoginController::send_verification('phone', $user);
            return redirect()->route('task.create.verify', ['task' => $task->id, 'user' => $user->id]);
        }

        if ($user->is_phone_number_verified && $user->phone_number != $data['phone_number']) {
            LoginController::send_verification_for_task_phone($task, $data['phone_number']);
            return redirect()->route('task.create.verify.phone', ['task' => $task->id, 'user' => $user->id, 'data' => $data['phone_number']]);
        }

        $task->status = 1;
        $task->user_id = $user->id;
        $task->phone = $user->phone_number;

        $performer_id = session()->get('performer_id_for_task');
        if ($performer_id) {
            $performer = User::query()->find($performer_id);
            $text_url = route("searchTask.task", $task->id);
            $text = "Заказчик предложил вам новую задания $text_url. Имя заказчика: " . $user->name;
            (new SmsService())->send($performer->phone_number, $text);
            Notification::query()->create([
                'user_id' => $task->user_id,
                'performer_id' => $performer_id,
                'task_id' => $task->id,
                'name_task' => $task->name,
                'description' => '123',
                'type' => 4,
            ]);

            NotificationService::sendNotificationRequest([$performer_id], [
                'url' => 'detailed-tasks' . '/' . $task->id, 'name' => $task->name, 'time' => 'recently'
            ]);

            session()->forget('performer_id_for_task');
        } else {
            NotificationService::sendTaskNotification($task, $user->id);
        }

        $task->save();
        return redirect()->route('searchTask.task', $task->id);
    }

    public function contact_register(Task $task, UserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make('login123');
        $user = User::create($data);
        LoginController::send_verification('phone', $user);

        return redirect()->route('task.create.verify', ['task' => $task->id, 'user' => $user->id]);

    }

    public function contact_login(Task $task, UserPhoneRequest $request)
    {
        $request->validated();
        $user = User::query()->where('phone_number', $request->phone_number)->first();
        LoginController::send_verification('phone', $user);
        return redirect()->route('task.create.verify', ['task' => $task->id, 'user' => $user->id])->with(['not-show', 'true']);

    }

    public function verify(Task $task, User $user)
    {
        return view('create.verify', compact('task', 'user'));
    }

    public function verify2(Task $task, User $user, $data)
    {
        return view('create.verify2', compact('task', 'user', 'data'));
    }

    public function deletetask(Task $task)
    {
        $task->delete();
        CustomFieldsValue::where('task_id', $task)->delete();
    }

    public function deleteAllImages(Task $task)
    {
        taskGuard($task);
        $task->photos = null;
        $task->save();
        Alert::success('success');
        return back();
    }

}
