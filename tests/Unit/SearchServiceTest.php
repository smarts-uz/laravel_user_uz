<?php

namespace Tests\Unit;

use App\Models\Compliance;
use App\Models\User;
use App\Services\Task\SearchService;
use Tests\TestCase;

class SearchServiceTest extends TestCase
{
    public function test_search_new()
    {
        $lang = 'uz';
        (new SearchService)->search_new($lang);
        $this->assertTrue(true);
    }

    public function test_compliance_saveS()
    {
        $data = [
            'user_id'=>1,
            'task_id'=>3033,
            'compliance_type_id'=>1,
            'text'=>'unit test uchun'
        ];
        (new SearchService)->compliance_saveS($data);
        Compliance::query()->where('text','unit test uchun')->delete();
        $this->assertTrue(true);
    }

    public function test_task_service()
    {
        $auth_response = '';
        $userId = 1;
        $task_id = 3033;
        $filter = 'date';
        (new SearchService)->task_service($auth_response, $userId, $task_id, $filter);
        $this->assertTrue(true);
    }

    public function test_cancelTask()
    {
        $userId = 1;
        $taskId = 3033;
        (new SearchService)->cancelTask($taskId, $userId);
        $this->assertTrue(true);
    }

    public function test_delete_task()
    {
        $userId = 1662;
        $taskId = 3033;
        (new SearchService)->delete_task($taskId, $userId);
        $this->assertTrue(true);
    }

    public function test_task_cancel()
    {
        $user = User::find(1);
        $taskId = 3033;
        (new SearchService)->task_cancel($taskId, $user);
        $this->assertTrue(true);
    }

    public function test_favorite_task_create()
    {
        $task_id = 3033;
        $userId = 1;
        (new SearchService)->favorite_task_create($task_id, $userId);
        $this->assertTrue(true);
    }

    public function test_favorite_task_delete()
    {
        $task_id = 3033;
        $userId = 1;
        (new SearchService)->favorite_task_delete($task_id, $userId);
        $this->assertTrue(true);
    }

    public function test_favorite_task_all()
    {
        $userId = 1;
        (new SearchService)->favorite_task_all($userId);
        $this->assertTrue(true);
    }


}
