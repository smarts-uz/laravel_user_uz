<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use App\Services\Task\UpdateTaskService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Tests\TestCase;

class UpdateTaskServiceTest extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function test_updateName()
    {
        $taskId = 3033;
        $data = [
            'name' => 'test name',
            'category_id' => 31
        ];
        (new UpdateTaskService)->updateName($taskId, $data);
        $this->assertTrue(true);
    }

    public function test_get_custom()
    {
        $taskId = 3033;
        (new UpdateTaskService)->get_custom($taskId);
        $this->assertTrue(true);
    }

    public function test_get_remote()
    {
        $taskId = 3033;
        (new UpdateTaskService)->get_remote($taskId);
        $this->assertTrue(true);
    }

    public function test_updateRemote()
    {
        $taskId = 3033;
        $data = [
          'radio'=> 'address',
        ];
        (new UpdateTaskService)->updateRemote($taskId, $data);
        $this->assertTrue(true);
    }

    public function test_get_address()
    {
        $task = Task::find(3033);
        (new UpdateTaskService)->get_address($task);
        $this->assertTrue(true);
    }

    public function test_get_date()
    {
        $taskId = 3033;
        (new UpdateTaskService)->get_date($taskId);
        $this->assertTrue(true);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function test_updateDate()
    {
        $taskId = 3033;
        $data = [
            'date_type' => 1
        ];
        (new UpdateTaskService)->updateDate($taskId, $data);
        $this->assertTrue(true);
    }

    public function test_get_budget()
    {
        $taskId = 3033;
        (new UpdateTaskService)->get_budget($taskId);
        $this->assertTrue(true);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function test_updateBudget()
    {
        $taskId = 3033;
        $data = [
            'amount' => 12000,
            'budget_type' => 1,
        ];
        (new UpdateTaskService)->updateBudget($taskId, $data);
        $this->assertTrue(true);
    }

    public function test_get_note()
    {
        $taskId = 3033;
        (new UpdateTaskService)->get_note($taskId);
        $this->assertTrue(true);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function test_updateNote()
    {
        $taskId = 3033;
        $data = [
            'description' => 'test',
            'docs' => 0
        ];
        (new UpdateTaskService)->updateNote($taskId, $data);
        $this->assertTrue(true);
    }

    public function test_get_contact()
    {
        $taskId = 3033;
        (new UpdateTaskService)->get_contact($taskId);
        $this->assertTrue(true);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function test_updateContact()
    {
        $taskId = 3033;
        $data = [
            'phone_number' => '+998936803663_1233',
        ];
        $user = User::find(1);
        (new UpdateTaskService)->updateContact($taskId, $data, $user);
        $this->assertTrue(true);
    }

    public function test_get_verify()
    {
        $taskId = 3033;
        $user = User::find(1);
        (new UpdateTaskService)->get_verify($taskId, $user);
        $this->assertTrue(true);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function test_verification()
    {
        $taskId = 3033;
        $data = [
            'phone_number'=>'+998936803663',
            'sms_otp'=>123456
        ];
        (new UpdateTaskService)->verification($taskId, $data);
        $this->assertTrue(true);
    }

    public function test_taskGuardApi()
    {
        $task = Task::find(3033);
        $userId = 1;
        (new UpdateTaskService)->taskGuardApi($task, $userId);
        $this->assertTrue(true);
    }

    public function test_completed()
    {
        $taskId = 3033;
        $userId = 1;
        (new UpdateTaskService)->completed($taskId, $userId);
        $this->assertTrue(true);
    }

    public function test_not_completed()
    {
        $taskId = 3033;
        $data = 'asasassa';
        $userId = 1;
        (new UpdateTaskService)->not_completed($taskId, $data, $userId);
        $this->assertTrue(true);
    }

    public function test_not_completed_web()
    {
        $taskId = 3033;
        $data = 'asasassa';
        (new UpdateTaskService)->not_completed_web($taskId, $data);
        $this->assertTrue(true);
    }

}
