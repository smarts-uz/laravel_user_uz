<?php
declare(strict_types=1);

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\{BudgetRequest, CreateContactRequest,
    CreateNameRequest, NoteRequest, TaskDateRequest, UserPhoneRequest, UserRequest};
use App\Models\{CustomField, CustomFieldsValue, Task, User};
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use App\Services\{Task\CreateService, Task\CustomFieldService, Task\UpdateTaskService, VerificationService};
use Exception;
use Illuminate\Contracts\{Foundation\Application, View\Factory, View\View};
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\{Auth, Session};
use JsonException;
use RealRashid\SweetAlert\Facades\Alert;

class CreateController extends Controller
{
    protected CreateService $service;
    protected CustomFieldService $custom_field_service;
    public UpdateTaskService $updatetask;
    public function __construct()
    {
        $this->updatetask = new UpdateTaskService();
        $this->service = new CreateService();
        $this->custom_field_service = new CustomFieldService();
    }

    /**
     *
     * Function  name
     * @param Request $request
     * @return  Application|Factory|View
     */
    public function name(Request $request): View|Factory|Application
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
    public function custom_get(int $task_id): View|Factory|RedirectResponse|Application
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
    public function remote_get(int $task_id): View|Factory|Application
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
    public function address(int $task_id): View|Factory|Application
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
        $coordinates = $this->service->addAdditionalAddress($task_id, $requestAll);
        $task->update([
            'coordinates' => $coordinates,
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
    public function date(int $task_id): View|Factory|Application
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
     * @param int $task_id
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
    public function budget(int $task_id): View|Factory|Application
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
    public function budget_store(int $task_id, BudgetRequest $request): RedirectResponse
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
     * @throws JsonException
     */
    public function images_store(Request $request, Task $task): void
    {
        if($task->photos == null){
            $imgData = [];
        }else{
            $imgData = json_decode($task->photos, false, 512, JSON_THROW_ON_ERROR);
        }
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
    public function note(int $task_id): View|Factory|Application
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

       Task::find($task_id)->update($data);

        return redirect()->route("task.create.contact", $task_id);
    }


    /**
     *
     * Function  contact
     * @param int $task_id
     * @return  Application|Factory|View
     */
    public function contact(int $task_id): View|Factory|Application
    {
        $result = $this->custom_field_service->getCustomFieldsByRoute($task_id, CustomField::ROUTE_CONTACTS);
        $task = $result['task'];
        $custom_fields = $result['custom_fields'];

        if (Auth::check()){
            return view('create.contacts2', compact('task', 'custom_fields'));
        }

        return view('create.contacts', compact('task', 'custom_fields'));

    }


    /**
     *
     * Function  contact_store
     * @param int $task_id
     * @param CreateContactRequest $request
     * @return  RedirectResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function contact_store(int $task_id, CreateContactRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $data = $request->validated();
        return $this->service->contact_store($user, $data, $task_id);
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
        $data = $request->validated();
        $password = $request->get('password');
        return $this->service->contact_register($task_id, $data, $password);
    }

    /**
     *
     * Function  contact_login
     * @param int $task_id
     * @param UserPhoneRequest $request
     * @return  RedirectResponse
     * @throws Exception
     */
    public function contact_login(int $task_id, UserPhoneRequest $request): RedirectResponse
    {
        $request->validated();
        /** @var User $user */
        $user = User::where('phone_number', $request->get('phone_number'))->first();
        VerificationService::send_verification('phone', $user, $user->phone_number);
        return redirect()->route('task.create.verify', ['task' => $task_id, 'user' => $user->id])->with(['not-show', 'true']);
    }

    /**
     *
     * Function  verify
     * @param $task_id
     * @param $user
     * @return  Application|Factory|View
     */
    public function verify($task_id, $user): View|Factory|Application
    {
        $task = Task::find($task_id);
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
        $this->updatetask->taskGuard($task);
        $task->photos = null;
        $task->save();
        Alert::success(__('Изменено успешно'));
        return back();
    }
}
