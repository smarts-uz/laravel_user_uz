<?php

namespace Tests\Unit;

use App\Models\Compliance;
use App\Models\Task;
use App\Models\TaskResponse;
use App\Models\User;
use App\Services\Task\TaskService;
use JsonException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Tests\TestCase;

class TaskServiceTest extends TestCase
{
    public function test_taskIncrement()
    {
        $user_id = 1;
        $taskId = 3033;
        (new TaskService)->taskIncrement($user_id, $taskId);
        $this->assertTrue(true);
    }

    public function test_taskIndex()
    {
        $taskId = 3033;
        (new TaskService)->taskIndex($taskId);
        $this->assertTrue(true);
    }

    public function test_userInTask()
    {
        $user = User::find(1);
        (new TaskService)->userInTask($user);
        $this->assertTrue(true);
    }

    public function test_performerResponse()
    {
        $performer_response = TaskResponse::query()
            ->where('task_id', 3340)
            ->first();
        (new TaskService)->performerResponse($performer_response);
        $this->assertTrue(true);
    }

    public function test_taskAddress()
    {
        $task = Task::find(3033);
        (new TaskService)->taskAddress($task->addresses);
        $this->assertTrue(true);
    }

    public function test_same_tasks()
    {
        $taskId = 3033;
        (new TaskService)->same_tasks($taskId);
        $this->assertTrue(true);
    }

    public function test_responses()
    {
        $filter = 'rating';
        $taskId = 3033;
        $userId = 1;
        (new TaskService)->responses($filter, $taskId, $userId);
        $this->assertTrue(true);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws JsonException
     */
    public function test_response_store()
    {
        $data = [];
        $taskId = 3033;
        $user = User::find(1694);
        (new TaskService)->response_store($taskId, $user, $data);
        $this->assertTrue(true);
    }

    public function test_taskStatusUpdate()
    {
        $taskId = 3033;
        $authId = 1;
        (new TaskService)->taskStatusUpdate($taskId, $authId);
        $this->assertTrue(true);
    }

    public function test_my_tasks_count()
    {
        $is_performer = 1;
        $user = User::find(1694);
        (new TaskService)->my_tasks_count($user, $is_performer);
        $this->assertTrue(true);
    }

    public function test_my_tasks_all()
    {
        $is_performer = 1;
        $status = 1;
        $userId = 1;
        (new TaskService)->my_tasks_all($userId, $is_performer, $status);
        $this->assertTrue(true);
    }

    public function test_taskComplain()
    {
        $data = [
            'compliance_type_id' => '3',
            'text' => 'required'
        ];
        $user = User::find(1);
        $taskId = 3033;
        (new TaskService)->taskComplain($data, $user, $taskId);
        Compliance::query()
            ->where('task_id',$taskId)
            ->where('user_id',1)->delete();
        $this->assertTrue(true);
    }

    public function test_complainTypes()
    {
        (new TaskService)->complainTypes();
        $this->assertTrue(true);
    }

    public function test_performer_tasks()
    {
        $user_id = 1;
        $status = 1;
        (new TaskService)->performer_tasks($user_id, $status);
        $this->assertTrue(true);
    }

    public function test_all_tasks()
    {
        $user_id = 1;
        (new TaskService)->all_tasks($user_id);
        $this->assertTrue(true);
    }
}
