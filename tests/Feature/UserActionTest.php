<?php

namespace Tests\Unit;

use Tests\TestCase;
use Metko\Galera\GlrMessage;
use Metko\Galera\Exceptions\MessageDoesntExist;
use Metko\Galera\Exceptions\MessageDoesntBelongsToUser;

class UserActionTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function a_user_can_send_message_in_a_conversations_who_is_participant()
    {
        $this->user->write('test message', $this->conversation->id);
        $this->assertCount(1, $this->conversation->messages);
    }

    /** @test */
    public function a_user_can_send_message_while_reffering_another()
    {
        $this->user->write('test message', $this->conversation->id);
        $message1 = GlrMessage::all()->last();
        $this->user2->write('response message', $this->conversation->id, $message1->id);
        $message2 = GlrMessage::all()->last();
        $this->assertTrue($message2->isResponse());
    }

    /** @test */
    public function a_user_can_read_a_message()
    {
        $this->user->write('test message', $this->conversation->id);
        $message = $this->conversation->messages->first();
        $this->user2->readMessage($message->id);
        $this->assertTrue($message->isRead());
    }

    /** @test */
    public function read_message_that_doesnt_exists_throw_an_exeption()
    {
        $this->expectException(MessageDoesntExist::class);
        $this->user2->readMessage(22);
    }

    /** @test */
    public function read_message_that_doesnt_belongs_to_the_user_throw_an_exeption()
    {
        $this->expectException(MessageDoesntBelongsToUser::class);
        $this->user->write('test message', $this->conversation->id);
        $message = $this->conversation->messages->first();
        $this->user3->readMessage($message);
    }
}
