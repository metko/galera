<?php

namespace Tests\Features;

use Tests\TestCase;
use Metko\Galera\GlrMessage;
use Metko\Galera\Facades\Galera;
use Metko\Galera\GlrConversation;
use Illuminate\Support\Facades\Event;
use Metko\Galera\Events\MessageWasSent;
use Metko\Galera\Exceptions\MessageDoesntExist;
use Metko\Galera\Exceptions\ConversationIsClosed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Metko\Galera\Exceptions\ConversationInvalidType;
use Metko\Galera\Exceptions\ConversationDoesntExists;
use Metko\Galera\Exceptions\UnauthorizedConversation;
use Metko\Galera\Exceptions\MessageDoesntBelongsToConversation;

class MessagesTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function sending_a_message_fire_an_event()
    {
        Event::fake();
        $this->user->write('test message', $this->conversation->id);
        $message = GlrMessage::all()->last();
        Event::assertDispatched(MessageWasSent::class, function ($event) use ($message) {
            return $event->message->id === $message->id;
        });
    }

    /** @test */
    public function a_new_message_is_by_default_not_read()
    {
        $this->user->write('test message', $this->conversation->id);
        $this->assertFalse($this->conversation->messages->first()->isRead());
    }

    /** @test */
    public function a_new_message_is_by_default_not_read_by_all_user_except_expeditor()
    {
        $this->conversation->add($this->user3);
        $this->user->write('test message', $this->conversation->id);
        $message = $this->conversation->messages->first();
        $this->assertFalse($message->isRead());
        $this->assertCount(2, $message->status);
        $this->assertFalse($message->status->contains('to_user_id', $this->user->id));
    }

    /** @test */
    public function a_message_can_be_mark_as_read()
    {
        $this->user->write('test message', $this->conversation->id);
        $message = $this->conversation->messages->first();
        $message->markAsRead();
        $this->assertTrue($message->isRead());
    }

    /** @test */
    public function reffering_a_message_that_doest_exist_throw_exception()
    {
        $this->expectException(MessageDoesntExist::class);
        $this->user->write('test message', $this->conversation->id);
        $message1 = GlrMessage::all()->last();
        $this->user2->write('response message', $this->conversation->id, 4);
    }

    /** @test */
    public function reffering_a_message_that_doesnt_belongs_to_the_conversation_throw_exception()
    {
        $this->expectException(MessageDoesntBelongsToConversation::class);
        $conversation = Galera::participants(1, 2)->make();
        $this->user->write('test message', $this->conversation->id);
        $message1 = GlrMessage::all()->last();
        $this->user2->write('response message', $conversation->id, $message1);
    }

    /** @test */
    public function reffering_a_message_with_an_invalid_type_throw_exception()
    {
        $this->expectException(MessageDoesntExist::class);
        $this->user->write('test message', $this->conversation->id);
        $this->user2->write('response message', $this->conversation->id, new GlrConversation());
    }

    /** @test */
    public function sending_message_in_a_conversation_that_is_not_participants_throw_exeption()
    {
        $this->expectException(UnauthorizedConversation::class);
        $this->user3->write('Hey', $this->conversation->id);
        $this->assertCount(0, $this->conversation->fresh()->messages);
    }

    /** @test */
    public function sending_message_in_a_conversation_who_doesnt_exixts_trow_an_exeptions()
    {
        $this->expectException(ConversationDoesntExists::class);
        $message = 'test message';
        $this->user->write($message, 23);
    }

    /** @test */
    public function sending_message_in_a_conversation_with_invalid_type_trow_an_exeptions()
    {
        $this->expectException(ConversationInvalidType::class);
        $message = 'test message';
        $this->user->write($message, ['array']);
    }

    /** @test */
    public function send_a_message_in_a_close_conversation_throw_ecxeption()
    {
        $this->expectException(ConversationIsClosed::class);
        $this->conversation->close();
        $this->user->write('test message', $this->conversation->id);
        $this->assertCount(0, $this->conversation->messages->count());
    }

    /** @test */
    public function can_retreive_message_of_a_conversation_and_paginate()
    {
        for ($i = 0; $i < 30; ++$i) {
            $this->user->write('test message '.$i, $this->conversation);
        }
        $messages = Galera::ofConversation($this->conversation)->paginate(15);
        $this->assertCount(15, $messages);
    }
}
