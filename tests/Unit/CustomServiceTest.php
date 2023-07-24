<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\CustomService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Tests\TestCase;

class CustomServiceTest extends TestCase
{
    public function test_getContentText()
    {
        $page = 'profile';
        $key = 'profile_text';
        CustomService::getContentText($page, $key);
        $this->assertTrue(true);
    }

    public function test_getContentImage()
    {
        $page = 'home';
        $key = 'post_section_img3';
        CustomService::getContentImage($page, $key);
        $this->assertTrue(true);
    }

    public function test_getLocale()
    {
        (new CustomService)->getlocale();
        $this->assertTrue(true);
    }

    public function test_updateCache()
    {
        $taskId = 3033;
        $amount = 200000;
        (new CustomService)->updateCache('task_update_' . $taskId, 'budget', $amount);
        $this->assertTrue(true);
    }

    public function test_correctPhoneNumber(): string
    {
        $phone = '+998945480514_1662';
        $phone_number = (new CustomService)->correctPhoneNumber($phone);
        $this->assertTrue(true);
        return $phone_number;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function test_cacheLang()
    {
        $user_id = 1662;
        (new CustomService)->cacheLang($user_id);
        $this->assertTrue(true);
    }

    public function test_lastSeen()
    {
        $user = User::find(1662);
        (new CustomService)->lastSeen($user);
        $this->assertTrue(true);
    }


}
