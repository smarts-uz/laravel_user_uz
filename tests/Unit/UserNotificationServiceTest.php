<?php

namespace Tests\Unit;

use App\Models\Notification;
use App\Models\Task;
use App\Services\UserNotificationService;
use JsonException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Tests\TestCase;

class UserNotificationServiceTest extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws JsonException
     */
    public function test_sendNotificationToPerformer()
    {
        $task = Task::find(3033);
        UserNotificationService::sendNotificationToPerformer($task);
        Notification::query()->where('task_id',3033)->where('type',12)->delete();
        $this->assertTrue(true);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws JsonException
     */
    public function test_sendNotificationToUser()
    {
        $task = Task::find(3033);
        UserNotificationService::sendNotificationToUser($task);
        Notification::query()->where('task_id',3033)->where('type',12)->delete();
        $this->assertTrue(true);
    }
}
