<?php

namespace Tests\Features;

use Tests\TestCase;
use Metko\Galera\Facades\Galera;
use Metko\Galera\GlrConversation;
use Metko\Galera\Exceptions\ConversationIsClosed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Metko\Galera\Exceptions\ConversationInvalidType;
use Metko\Galera\Exceptions\ConversationDoesntExists;

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
        $conversation = Galera::addParticipants([1, 2])->create();
        $this->user->write('test message', $conversation->id);
        $this->assertCount(1, $conversation->messages);
    }

    /** @test */
    public function a_user_cant_write_in_a_conversation_that_is_not_participants_throw_exeption()
    {
        $conversation = Galera::addParticipants([1, 2])->create();
        $this->user3->write('Hey', $conversation->id);
        $this->assertCount(0, $conversation->messages);
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
        $conversation = factory(GlrConversation::class)->create();
        $conversation->close();
        $message = 'test message';
        $this->user->write($message, $conversation);
        $this->assertCount(0, $conversation->messages->count());
    }
}