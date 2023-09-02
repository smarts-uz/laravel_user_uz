<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\TaskResponse;
use App\Models\User;
use App\Services\Task\ResponseService;
use JsonException;
use Tests\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ResponseServiceTest extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws JsonException
     */
    public function test_store()
    {
        $data = [
            'description' => 'test uchun yaratildi',
            'price' => 120000,
            'not_free' => 1
        ];
        $task = Task::find(3033);
        $auth_user = User::find(1662);
        (new ResponseService)->store($data, $task, $auth_user);
        $this->assertTrue(true);
    }

    public function test_selectPerformer()
    {
        $response = TaskResponse::query()->where('description','test uchun yaratildi')->first();
        (new ResponseService)->selectPerformer($response);
        $this->assertTrue(true);
    }
}
