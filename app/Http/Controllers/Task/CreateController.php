<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\BudgetRequest;
use App\Http\Requests\CreateContactRequest;
use App\Http\Requests\TaskDateRequest;
use App\Http\Requests\UserPhoneRequest;
use App\Http\Requests\UserRequest;
use App\Models\Category;
use App\Models\CustomField;
use App\Models\CustomFieldsValue;
use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use App\Models\WalletBalance;
use App\Services\Task\CreateService;
use App\Services\Task\CustomFieldService;
use App\Services\VerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class CreateController extends Controller
{
    protected CreateService $service;
    protected CustomFieldService $custom_field_service;


    public function __construct()
    {
        $this->service = new CreateService();
        $this->custom_field_service = new CustomFieldService();
    }


    public function name(Request $request)
    {
        $category_id = $request->get('category_id');
        $service = new CreateService();
        $item = $service->name($category_id);
        return view("create.name", [
            'current_category'=>$item->current_category,
            'categories'=>$item->categories,
            'child_categories'=>$item->child_categories,
        ]);
    }

    public function name_store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required'
        ],
        [
            'name.required'=>__('Требуется заполнение!'),
            'name.string'=>__('Требуется заполнение!'),
            'name.max'=>__('Требуется заполнение!'),
            'category_id.required'=>__('Требуется заполнение!')
        ]);
        /** @var Task $task */
        $task = Task::query()->create($data);
        $this->service->attachCustomFieldsByRoute($task, CustomField::ROUTE_NAME, $request);

        return redirect()->route("task.create.custom.get", $task->id);
    }


    public function custom_get(Task $task)
    {

        $custom_fields = $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_CUSTOM);
        if (!$task->category->customFieldsInCustom->count()) {
            if ($task->category->parent->remote) {
                return redirect()->route("task.create.remote", $task->id);
            }
            return redirect()->route('task.create.address', $task->id);
        }

        return view('create.custom', compact('task', 'custom_fields'));

    }

    public function custom_store(Request $request, Task $task)
    {
        $this->service->attachCustomFieldsByRoute($task, CustomField::ROUTE_CUSTOM, $request);

        if ($task->category->parent->remote) {
            return redirect()->route("task.create.remote", $task->id);
        }

        return redirect()->route('task.create.address', $task->id);
    }

    public function remote_get(Task $task)
    {
        return view('create.remote', compact('task'));
    }

    public function remote_store(Request $request, Task $task)
    {
        $data = $request->validate(['radio' => 'required']);

        if ($data['radio'] === 'address') {
            return redirect()->route("task.create.address", $task->id);
        }

        if ($data['radio'] === 'remote') {
            $task->remote = 1;
            $task->save();
            return redirect()->route("task.create.date", $task->id);
        }

        return back();
    }

    public function address(Task $task)
    {
        $custom_fields = $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_ADDRESS);
        return view('create.location', compact('task', 'custom_fields'));
    }

    public function address_store(Request $request, Task $task)
    {

        $requestAll = $request->all();

        $task->update([
            'coordinates' => $this->service->addAdditionalAddress($task, $requestAll),
            'go_back'=> $request->get('go_back')
        ]);

        $this->service->attachCustomFieldsByRoute($task, CustomField::ROUTE_ADDRESS, $request);
        return redirect()->route("task.create.date", $task->id);

    }

    public function date(Task $task)
    {
        $custom_fields = $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_DATE);
        return view('create.date', compact('task', 'custom_fields'));

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
        $category = Category::query()->findOrFail($task->category_id);
        $custom_fields = $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_BUDGET);

        return view('create.budget', compact('task', 'category', 'custom_fields'));
    }

    public function budget_store(Task $task, BudgetRequest $request)
    {

        $task->budget = $request->get('amount2');
        $task->save();
        $this->service->attachCustomFieldsByRoute($task, CustomField::ROUTE_BUDGET, $request);


        return redirect()->route('task.create.note', $task->id);

    }

    public function images_store(Request $request, Task $task)
    {
        $imgData = json_decode($task->photos) ?? [];
        foreach ($request->file('images') as $uploadedImage) {
            $filename = time() . '_' . $uploadedImage->getClientOriginalName();
            $uploadedImage->move(public_path() . '/storage/uploads/', $filename);
            $imgData[] = $filename;
        }
        $task->photos = $imgData;
        $task->save();
    }

    public function note(Task $task)
    {
        $custom_fields = $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_NOTE);

        return view('create.notes', compact('task', 'custom_fields'));
    }

    public function note_store(Task $task, Request $request)
    {
        $data = $request->validate([
            'description' => 'required|string',
            'oplata' => 'required',
        ],
            [
                'description.required' =>  __('Требуется заполнение!'),
                'description.string' =>  __('login.name.string'),
            ]
        );
        if ($request['docs'] === "on") {
            $data['docs'] = 1;
        } else {
            $data['docs'] = 0;
        }
        $task->update($data);
        return redirect()->route("task.create.contact", $task->id);
    }


    public function contact(Task $task)
    {
        $custom_fields = $this->custom_field_service->getCustomFieldsByRoute($task, CustomField::ROUTE_CONTACTS);

        return view('create.contacts', compact('task', 'custom_fields'));
    }


    public function contact_store(Task $task, CreateContactRequest $request)
    {
        /** @var User $user */
        $user = auth()->user();
        $data = $request->validated();
        if (!($user->is_phone_number_verified && $user->phone_number === $data['phone_number'])) {
            VerificationService::send_verification('phone', $user, $data['phone_number']);
            $task->phone = $data['phone_number'];
            if ($user->phone_number === null) {
                $user->phone_number = $task->phone;
                $user->save();
            }
            $task->save();
            return redirect()->route('task.create.verify', ['task' => $task->id, 'user' => $user->id]);
        }

        $task->status = Task::STATUS_OPEN;
        $task->user_id = $user->id;
        $task->phone = $data['phone_number'];

        $create_service = new CreateService();
        $create_service->perform_notification($task, $user);

        $task->save();
        return redirect()->route('searchTask.task', $task->id);
    }

    public function contact_register(Task $task, UserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($request->get('password'));
        unset($data['password_confirmation']);
        $task->phone = $data['phone_number'];
        $task->save();
        /** @var User $user */
        $user = User::query()->create($data);
        $user->phone_number = $data['phone_number'] . '_' . $user->id;
        $user->save();
        $wallBal = new WalletBalance();
        $wallBal->balance = setting('admin.bonus');
        $wallBal->user_id = $user->id;
        $wallBal->save();
        if(setting('admin.bonus')>0){
            Notification::query()->create([
                'user_id' => $user->id,
                'description' => 'wallet',
                'type' => Notification::WALLET_BALANCE,
            ]);
        }
        VerificationService::send_verification('phone', $user, $user->phone_number);
        return redirect()->route('task.create.verify', ['task' => $task->id, 'user' => $user->id]);
    }

    public function contact_login(Task $task, UserPhoneRequest $request)
    {
        $request->validated();
        /** @var User $user */
        $user = User::query()->where('phone_number', $request->get('phone_number'))->first();
        VerificationService::send_verification('phone', $user, $user->phone_number);
        return redirect()->route('task.create.verify', ['task' => $task->id, 'user' => $user->id])->with(['not-show', 'true']);

    }

    public function verify(Task $task, User $user)
    {
        return view('create.verify', compact('task', 'user'));
    }

    public function deleteTask(Task $task)
    {
        $task->delete();
        CustomFieldsValue::query()->where('task_id', $task)->delete();
    }

    public function deleteAllImages(Task $task)
    {
        taskGuard($task);
        $task->photos = null;
        $task->save();
        Alert::success(__('Изменено успешно'));
        return back();
    }
}
