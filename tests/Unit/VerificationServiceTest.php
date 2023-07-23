<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use App\Services\VerificationService;
use Exception;
use Tests\TestCase;

class VerificationServiceTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function test_send_verification(){
        $needle = 'email';
        $user = User::find(1662);
        VerificationService::send_verification($needle, $user);
        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     */
    public function test_send_verification_email(){
        $needle = 'email';
        $user = User::find(1662);
        VerificationService::send_verification_email($needle, $user);
        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     */
    public function test_send_verification_for_task_phone(){
        $task = Task::find(3033);
        $phone_number = '+998945480514';
        VerificationService::send_verification_for_task_phone($task, $phone_number);
        $this->assertTrue(true);
    }

}
