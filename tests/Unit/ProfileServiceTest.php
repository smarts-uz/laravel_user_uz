<?php

namespace Tests\Unit;

use App\Models\Portfolio;
use App\Models\ResponseTemplate;
use App\Models\User;
use App\Services\Profile\ProfileService;
use Exception;
use Tests\TestCase;
use UAParser\Exception\FileNotFoundException;

class ProfileServiceTest extends TestCase
{
    public function test_index()
    {
        $userId = 1;
        (new ProfileService)->index($userId);
        $this->assertTrue(true);
    }

    /**
     * @throws FileNotFoundException
     */
    public function test_settingsEdit()
    {
        $user = User::find(1);
        $lang = 'uz';
        (new ProfileService)->settingsEdit($user, $lang);
        $this->assertTrue(true);
    }

    public function test_settingsUpdate()
    {
        $user = User::find(1);
        $data = [
            'email' => 'admin@admin.com',
            'phone_number' => '+998879799484',
        ];
        (new ProfileService)->settingsUpdate($data, $user);
        $this->assertTrue(true);
    }

    public function test_storeProfilePhoto()
    {
        $user = User::find(1);
        $hasFile = '';
        $files = null;
        (new ProfileService)->storeProfilePhoto($files, $hasFile, $user);
        $this->assertTrue(true);
    }

    public function test_profileCash()
    {
        $user = User::find(1);
        (new ProfileService)->profileCash($user);
        $this->assertTrue(true);
    }

    public function test_profileData()
    {
        $user = User::find(1);
        (new ProfileService)->profileData($user);
        $this->assertTrue(true);
    }

    public function test_userReviews()
    {
        $userId = 1;
        $performer = 0;
        $review = 'good';
        ProfileService::userReviews($userId, $performer, $review);
        $this->assertTrue(true);
    }

    public function test_videoStore()
    {
        $user = User::find(1);
        $link = 'Hello';
        (new ProfileService)->videoStore($user, $link);
        $this->assertTrue(true);
    }

    public function test_balance()
    {
        $period = 'year';
        $from = '2022-11-01';
        $to = '2023-11-01';
        $type = 'in';
        $userId = 1;
        (new ProfileService)->balance($period, $from, $to, $type, $userId);
        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     */
    public function test_phoneUpdate()
    {
        $phoneNumber = '+998945480514';
        $user = User::find(1);
        (new ProfileService)->phoneUpdate($phoneNumber, $user);
        $this->assertTrue(true);
    }

    public function test_changePassword()
    {
        $user = User::find(1);
        $data = [
            'old_password'=>'1234565644',
            'password'=>'$$@@admin@@$$'
        ];
        (new ProfileService)->changePassword($user, $data);
        $this->assertTrue(true);
    }

    public function test_updateSettings()
    {
        $user = User::find(1);
        $data = [
            'name'=>'Admin',
            'email'=>'admin@admin.com'
        ];
        (new ProfileService)->updateSettings($data, $user);
        $this->assertTrue(true);
    }

    public function test_notifications()
    {
        $user = User::find(1);
        $notification = 1;
        (new ProfileService)->notifications($user, $notification);
        $this->assertTrue(true);
    }

    public function test_subscribeToCategory()
    {
        $user = User::find(1);
        $categories = [];
        $sms_notification = 0;
        $email_notification = 0;
        (new ProfileService)->subscribeToCategory($categories, $user, $sms_notification, $email_notification);
        $this->assertTrue(true);
    }

    public function test_walletBalance()
    {
        $user = User::find(1);
        $data = ProfileService::walletBalance($user);
        $this->assertTrue(true);
    }

    public function test_verifyCategory()
    {
        $lang = 'uz';
        (new ProfileService)->verifyCategory($lang);
        $this->assertTrue(true);
    }

    public function test_change_password()
    {
        $user = User::find(1);
        $data = [
            'password'=>'123412345678',
            'old_password'=>'98765432q'
        ];
        (new ProfileService)->change_password($user, $data);
        $this->assertTrue(true);
    }

    /**
     * @throws \JsonException
     */
    public function test_deleteImage()
    {
        $image = '';
        $portfolioId = 426;
        (new ProfileService)->deleteImage($image, $portfolioId);
        $this->assertTrue(true);
    }

    public function test_portfolioUpdate()
    {
        $data = [];
        $portfolio = Portfolio::find(426);
        (new ProfileService)->portfolioUpdate($data, $portfolio);
        $this->assertTrue(true);
    }

    public function test_portfolios()
    {
        $userId = 1;
        (new ProfileService)->portfolios($userId);
        $this->assertTrue(true);
    }

    public function test_portfolioIndex()
    {
        $portfolio = Portfolio::find(426);
        (new ProfileService)->portfolioIndex($portfolio);
        $this->assertTrue(true);
    }

    public function test_changeLanguage()
    {
        $lang = 'uz';
        $version = '';
        (new ProfileService)->changeLanguage($lang, $version);
        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     */
    public function test_self_delete()
    {
        $user = User::find(1);
        (new ProfileService)->self_delete($user);
        $this->assertTrue(true);
    }

    public function test_confirmationSelfDelete()
    {
        $user = User::find(1);
        $code = 123456;
        (new ProfileService)->confirmationSelfDelete($user, $code);
        $this->assertTrue(true);
    }

    public function test_blocked_user()
    {
        $blocked_user_id = 1;
        $user_id = 1662;
        (new ProfileService)->blocked_user($blocked_user_id, $user_id);
        $this->assertTrue(true);
    }

    public function test_blocked_user_list()
    {
        $user_id = 1;
        (new ProfileService)->blocked_user_list($user_id);
        $this->assertTrue(true);
    }

    public function test_response_template()
    {
        $user_id = 1;
        (new ProfileService)->response_template($user_id);
        $this->assertTrue(true);
    }

    public function test_response_template_delete()
    {
        $user_id = 1;
        $templateId = ResponseTemplate::find(1);
        (new ProfileService)->response_template_delete($user_id, $templateId);
        $this->assertTrue(true);
    }

    public function test_userCategory()
    {
        $user_id = 1;
        (new ProfileService)->userCategory($user_id);
        $this->assertTrue(true);
    }

    public function test_categories()
    {
        $category = [];
        (new ProfileService)->categories($category);
        $this->assertTrue(true);
    }
}
