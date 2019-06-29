<?php

namespace Tests\Features;

use Tests\TestCase;
use Metko\Galera\GlrMessage;
use Metko\Galera\Facades\Galera;
use Metko\Galera\GlrConversation;
use Metko\Galera\Exceptions\MessageDoesntExist;
use Metko\Galera\Exceptions\MessageInvalidType;
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
        $conversation = Galera::create(1, 2);
        $this->user->write('test message', $this->conversation->id);
        $message1 = GlrMessage::all()->last();
        $this->user2->write('response message', $conversation->id, $message1);
    }

    /** @test */
    public function reffering_a_message_with_an_invalid_type_throw_exception()
    {
        $this->expectException(MessageInvalidType::class);
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
        $this->user->write('test message', $this->conversation);
        $this->assertCount(0, $this->conversation->messages->count());
    }
}
