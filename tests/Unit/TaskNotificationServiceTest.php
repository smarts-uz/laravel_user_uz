<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Services\TaskNotificationService;
use JsonException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Tests\TestCase;

class TaskNotificationServiceTest extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws JsonException
     */
    public function test_sendNotificationForCancelledTask()
   {
       $task = Task::find(3033);
       TaskNotificationService::sendNotificationForCancelledTask($task);
       $this->assertTrue(true);
   }
}
