<?php

namespace Tests\Unit;

use App\Models\ChMessage;
use App\Services\Chat\ContactService;
use JsonException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Tests\TestCase;

class ContactServiceTest extends TestCase
{
    public function test_contactsList()
    {
        $userId = 1;
        ContactService::contactsList($userId);
        $this->assertTrue(true);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function test_telegramNotification()
    {
        $locale = 'uz';
        $request_id = 663;
        $message = 'Hello';
        $AuthId = 1;
        (new ContactService)->telegramNotification($locale, $request_id, $message, $AuthId);
        $this->assertTrue(true);
    }

    /**
     * @throws JsonException
     */
    public function test_sendFromTelegram()
    {
        $user_id = 663;
        $message = 'Hello test';
        (new ContactService)->sendFromTelegram($user_id, $message);
        $this->assertTrue(true);
    }

    public function test_messageData()
    {
        $messages = ChMessage::query()->where('from_id',1)->get();
        (new ContactService)->messageData($messages);
        $this->assertTrue(true);
    }
}
