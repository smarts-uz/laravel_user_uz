<?php

namespace Tests\Unit;

use App\Models\Notification;
use App\Models\User;
use App\Services\PerformersService;
use JsonException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Tests\TestCase;

class PerformersServiceTest extends TestCase
{
    public function test_service()
    {
        $authId = 1662;
        $search = '';
        $lang = 'uz';
        (new PerformersService)->service($authId,$search,$lang);
        $this->assertTrue(true);
    }

    public function test_performer()
    {
        $user = User::find(1);
        (new PerformersService)->performer($user);
        $this->assertTrue(true);
    }

    public function test_perf_ajax()
    {
        $authId = 1;
        $search = '';
        $cf_id = 30;
        (new PerformersService)->perf_ajax($authId, $search, $cf_id);
        $this->assertTrue(true);
    }

    public function test_performer_filter()
    {
        $authId = 1;
        $data = [
            'search' => 'Admin',
            'online' => true
        ];
        (new PerformersService)->performer_filter($data, $authId);
        $this->assertTrue(true);
    }

    public function test_performerData()
    {
        $performer = User::find(1);
        (new PerformersService)->performerData($performer);
        $this->assertTrue(true);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws JsonException
     */
    public function test_task_give_app()
    {
        $task_id = 3033;
        $performer_id = 1;
        (new PerformersService)->task_give_app($task_id, $performer_id);
        Notification::query()
            ->where('task_id',3033)
            ->where('performer_id',1)
            ->where('type',4)
            ->delete();
        $this->assertTrue(true);
    }

    public function test_setView()
    {
        $user = User::find(1);
        (new PerformersService)->setView($user);
        $this->assertTrue(true);
    }

    public function test_performers_image()
    {
        $authId = 1;
        $categoryId = 31;
        (new PerformersService)->performers_image($categoryId, $authId);
        $this->assertTrue(true);
    }

    public function test_becomePerformerEmailPhone()
    {
        $user = User::find(1);
        $data = [
            'email' => 'admin@admin.com',
            'phone_number' => '+998879799484'
        ];
        (new PerformersService)->becomePerformerEmailPhone($user, $data);
        $this->assertTrue(true);
    }

    public function test_becomePerformerData()
    {
        $user = User::find(1);
        $data = [
            'born_date' => '1997-12-23',
            'name' => 'Admin',
            'location' => 'Tashkent',
        ];
        (new PerformersService)->becomePerformerData($user, $data);
        $this->assertTrue(true);
    }
}
