<?php
declare(strict_types=1);

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\BudgetRequest;
use App\Http\Requests\CreateContactRequest;
use App\Http\Requests\CreateNameRequest;
use App\Http\Requests\NoteRequest;
use App\Http\Requests\TaskDateRequest;
use App\Http\Requests\UserPhoneRequest;
use App\Http\Requests\UserRequest;
use App\Models\CustomField;
use App\Models\CustomFieldsValue;
use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use App\Models\WalletBalance;
use App\Services\NotificationService;
use App\Services\Task\CreateService;
use App\Services\Task\CustomFieldService;
use App\Services\Task\UpdateTaskService;
use App\Services\VerificationService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
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


    /**
     *
     * Function  name
     * @param Request $request
     * @return  Application|Factory|View
     */
    public function name(Request $request)
    {
        $lang = Session::get('lang');
        $category_id = $request->get('category_id');
        $item = $this->service->name($category_id, $lang);
        return view("create.name", [
            'current_category'=>$item->current_category,
            'categories'=>$item->categories,
            'child_categories'=>$item->child_categories,
        ]);
    }

    /**
     *
     * Function  name_store
     * @param CreateNameRequest $request
     * @return  RedirectResponse
     */
    public function name_store(CreateNameRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $task_id = $this->service->storeName($data['name'], $data['category_id']);
        $this->service->attachCustomFieldsByRoute($task_id, CustomField::ROUTE_NAME, $request->all());
        return redirect()->route("task.create.custom.get", $task_id);
    }


    /**
     *
     * Function  custom_get
     * @param int $task_id
     * @return  Application|Factory|View|RedirectResponse
     */
    public function custom_get(int $task_id)
    {
        $result = $this->custom_field_service->getCustomFieldsByRoute($task_id, CustomField::ROUTE_CUSTOM);
        $task = $result['task'];
        $custom_fields = $result['custom_fields'];
        if (!$task->category->customFieldsInCustom->count()) {
            if ($task->category->parent->remote) {
                return redirect()->route("task.create.remote", $task->id);
            }
            return redirect()->route('task.create.address', $task->id);
        }
        return view('create.custom', compact('task', 'custom_fields'));
    }

    /**
     *
     * Function  custom_store
     * @param Request $request
     * @param int $task_id
     * @return  RedirectResponse
     */
    public function custom_store(Request $request, int $task_id): RedirectResponse
    {
        $task = $this->service->attachCustomFieldsByRoute($task_id, CustomField::ROUTE_CUSTOM, $request->all());

        if ($task->category->parent->remote) {
            return redirect()->route("task.create.remote", $task->id);
        }
        return redirect()->route('task.create.address', $task->id);
    }

    /**
     *
     * Function  remote_get
     * @param int $task_id
     * @return  Application|Factory|View
     */
    public function remote_get(int $task_id)
    {
        $task = Task::with('category.custom_fields')->find($task_id);
        return view('create.remote', compact('task'));
    }

    /**
     *
     * Function  remote_store
     * @param Request $request
     * @param Task $task
     * @return  RedirectResponse
     */
    public function remote_store(Request $request, Task $task): RedirectResponse
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

        return redirect()->back();
    }

    /**
     *
     * Function  address
     * @param int $task_id
     * @return  Application|Factory|View
     */
    public function address(int $task_id)
    {
        $result = $this->custom_field_service->getCustomFieldsByRoute($task_id, CustomField::ROUTE_ADDRESS);
        $task = $result['task'];
        $custom_fields = $result['custom_fields'];
        return view('create.location', compact('task', 'custom_fields'));
    }

    /**
     *
     * Function  address_store
     * @param Request $request
     * @param int $task_id
     * @return  RedirectResponse
     */
    public function address_store(Request $request, int $task_id): RedirectResponse
    {
        $task = Task::select('id')->find($task_id);
        $requestAll = $request->all();
        $cordinates = $this->service->addAdditionalAddress($task_id, $requestAll);
        $task->update([
            'coordinates' => $cordinates,
            'go_back'=> $request->get('go_back')
        ]);

        $this->service->attachCustomFieldsByRoute($task_id, CustomField::ROUTE_ADDRESS, $requestAll);
        return redirect()->route("task.create.date", $task_id);

    }

    /**
     *
     * Function  date
     * @param int $task_id
     * @return  Application|Factory|View
     */
    public function date(int $task_id)
    {
        $result = $this->custom_field_service->getCustomFieldsByRoute($task_id, CustomField::ROUTE_DATE);
        $task = $result['task'];
        $custom_fields = $result['custom_fields'];
        return view('create.date', compact('task', 'custom_fields'));

    }

    /**
     *
     * Function  date_store
     * @param TaskDateRequest $request
     * @param $task_id
     * @return  RedirectResponse
     */
    public function date_store(TaskDateRequest $request, int $task_id): RedirectResponse
    {
        $data = $request->validated();
        $task = $this->service->attachCustomFieldsByRoute($task_id, CustomField::ROUTE_DATE, $request->all());
        $task->update($data);

        return redirect()->route('task.create.budget', $task_id);
    }

    /**
     *
     * Function  budget
     * @param int $task_id
     * @return  Application|Factory|View
     */
    public function budget(int $task_id)
    {
        $result = $this->custom_field_service->getCustomFieldsByRoute($task_id, CustomField::ROUTE_BUDGET);
        $category = $result['category'];
        $custom_fields = $result['custom_fields'];
        $task = $result['task'];
        return view('create.budget', compact('task', 'category', 'custom_fields'));
    }

    /**
     *
     * Function  budget_store
     * @param int $task_id
     * @param BudgetRequest $request
     * @return  RedirectResponse
     */
    public function budget_store(int $task_id, BudgetRequest $request): \Illuminate\Http\RedirectResponse
    {
        $task = $this->service->attachCustomFieldsByRoute($task_id, CustomField::ROUTE_BUDGET, $request->all());

        $task->budget = $request->get('amount2');
        $task->save();

        return redirect()->route('task.create.note', $task->id);
    }

    /**
     *
     * Function  images_store
     * @param Request $request
     * @param Task $task
     */
    public function images_store(Request $request, Task $task): void
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

    /**
     *
     * Function  note
     * @param int $task_id
     * @return  Application|Factory|View
     */
    public function note(int $task_id)
    {
        $result = $this->custom_field_service->getCustomFieldsByRoute($task_id, CustomField::ROUTE_NOTE);
        $task = $result['task'];
        $custom_fields = $result['custom_fields'];

        return view('create.notes', compact('task', 'custom_fields'));
    }

    /**
     *
     * Function  note_store
     * @param int $task_id
     * @param NoteRequest $request
     * @return  RedirectResponse
     */
    public function note_store(int $task_id, NoteRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request['docs'] === "on") {
            $data['docs'] = 1;
        } else {
            $data['docs'] = 0;
        }

        $task = Task::select('id')->find($task_id)->update($data);

        return redirect()->route("task.create.contact", $task_id);
    }


    /**
     *
     * Function  contact
     * @param int $task_id
     * @return  Application|Factory|View
     */
    public function contact(int $task_id)
    {
        $result = $this->custom_field_service->getCustomFieldsByRoute($task_id, CustomField::ROUTE_CONTACTS);
        $task = $result['task'];
        $custom_fields = $result['custom_fields'];

        return view('create.contacts', compact('task', 'custom_fields'));
    }


    /**
     *
     * Function  contact_store
     * @param int $task_id
     * @param CreateContactRequest $request
     * @return  RedirectResponse
     * @throws Exception
     */
    public function contact_store(int $task_id, CreateContactRequest $request): RedirectResponse
    {
        $task = Task::find($task_id);
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
        $performer_id = Session::get('performer_id_for_task');
        if ($performer_id) {
            $this->service->perform_notification($task, $user ,$performer_id);
        } else {
            NotificationService::sendTaskNotification($task, $user->id);
        }
        $task->save();
        return redirect()->route('searchTask.task', $task->id);
    }

    /**
     *
     * Function  contact_register
     * @param int $task_id
     * @param UserRequest $request
     * @return  RedirectResponse
     * @throws Exception
     */
    public function contact_register(int $task_id, UserRequest $request): RedirectResponse
    {
        $task = Task::select('phone')->first($task_id);
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

    /**
     *
     * Function  contact_login
     * @param $task_id
     * @param UserPhoneRequest $request
     * @return  RedirectResponse
     * @throws Exception
     */
    public function contact_login($task_id, UserPhoneRequest $request): RedirectResponse
    {
        $request->validated();
        /** @var User $user */
        $user = User::select('id', 'phone_number', 'email')->where('phone_number', $request->get('phone_number'))->first();
        VerificationService::send_verification('phone', $user, $user->phone_number);
        return redirect()->route('task.create.verify', ['task' => $task_id, 'user' => $user->id])->with(['not-show', 'true']);

    }

    /**
     *
     * Function  verify
     * @param $task_id
     * @param User $user
     * @return  Application|Factory|View
     */
    public function verify($task_id, User $user)
    {
        $task = Task::select('name')->first($task_id);
        return view('create.verify', compact('task', 'user'));
    }

    /**
     *
     * Function  deleteTask
     * @param Task $task
     */
    public function deleteTask(Task $task): void
    {
        $task->delete();
        CustomFieldsValue::query()->where('task_id', $task)->delete();
    }

    /**
     *
     * Function  deleteAllImages
     * @param Task $task
     * @return  RedirectResponse
     */
    public function deleteAllImages(Task $task): RedirectResponse
    {
        (new UpdateTaskService)->taskGuard($task);
        $task->photos = null;
        $task->save();
        Alert::success(__('Изменено успешно'));
        return back();
    }
}
