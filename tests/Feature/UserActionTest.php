<?php

namespace Tests\Unit;

use Tests\TestCase;
use Metko\Galera\GlrMessage;

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
    public function a_user_can_read_all_message_in_conversation()
    {
        $this->user->write('test message', $this->conversation->id);
        $this->user->write('test message 2', $this->conversation->id);
        $this->user2->readAll($this->conversation->id);
        $this->assertFalse($this->user2->hasUnreadMessage());
    }

    /** @test */
    public function another_user_cant_read_message_that_doesnt_belongs_to_him()
    {
        $this->user->write('test message', $this->conversation->id);
        $this->user2->write('test message', $this->conversation->id);
        $this->user3->readAll($this->conversation->id);
        $this->assertTrue($this->user->hasUnreadMessage());
        $this->assertTrue($this->user2->hasUnreadMessage());
        $this->assertFalse($this->user3->hasUnreadMessage());
    }
}
