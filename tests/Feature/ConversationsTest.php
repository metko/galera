<?php

namespace Tests\Features;

use Tests\TestCase;
use Metko\Galera\GlrMessage;
use Metko\Galera\Facades\Galera;
use Metko\Galera\GlrConversation;
use Metko\Galera\Exceptions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConversationsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function retreive_a_conversation()
    {
        $conversation = Galera::conversation(1);
        $this->assertTrue($this->conversation->is($conversation));
        $conversation = Galera::conversation($this->conversation->id);
        $this->assertTrue($this->conversation->is($conversation));
    }

    /** @test */
    public function create_with_less_of_2_participants_will_throw_an_exception()
    {
        $this->expectException(Exceptions\InsufisantParticipant::class);
        $conversation = Galera::make();
    }

    /** @test */
    public function a_conversation_can_add_a_user()
    {
        $conversation = Galera::participants($this->user, 3)->make();
        $conversation->fresh()->add(2);
        $this->assertCount(3, $conversation->fresh()->participants);
    }

    /** @test */
    public function a_conversation_can_add_multiple_users_at_the_same_time()
    {
        $conversation = Galera::participants(1, 2)->make();
        $conversation->addMany([$this->user3, '4']);
        $this->assertCount(4, $conversation->fresh()->participants);
    }

    /** @test */
    public function a_conversation_can_remove_a_participant()
    {
        $conversation = Galera::participants([1, 2, 3])->make();
        $conversation->fresh()->remove($this->user2);
        $this->assertCount(2, $conversation->fresh()->participants);
    }

    /** @test */
    public function a_conversation_can_send_a_message_in_a_conversation()
    {
        Galera::from(1)->to(2)->in($this->conversation)->send('Hello');

        $this->assertCount(1, $this->conversation->fresh()->messages);
    }

    /** @test */
    public function a_conversation_can_send_a_message_and_create_a_new_one()
    {
        $conversation = Galera::from(2)->to(3)->send('Hello');
        $this->assertCount(1, $conversation->fresh()->messages);
        $this->assertCount(2, GlrConversation::all());
    }

    /** @test */
    public function a_conversation_can_be_deleted()
    {
        Galera::conversation(1)->delete();
        $this->assertCount(0, GlrConversation::all());
    }

    /** @test */
    public function a_conversation_can_clear_all_his_message()
    {
        $this->user->write('Message', $this->conversation->id);
        $this->user2->write('Message', $this->conversation->id);
        $this->user->write('Message', $this->conversation->id);
        $this->conversation->fresh()->clear();
        $this->assertCount(0, $this->conversation->fresh()->messages);
    }

    /** @test */
    public function a_conversation_can_delete_a_message()
    {
        $this->user->write('Message', $this->conversation->id);
        $message = GlrMessage::all()->last();
        Galera::message($message->id)->delete();
        $this->assertCount(0, $this->conversation->fresh()->messages);
    }

    /** @test */
    public function a_conversation_can_mark_all_the_message_as_readed()
    {
        $this->conversation->add(3);
        $this->user->write('test message', $this->conversation->id);
        $this->user2->write('test message 2', $this->conversation->id);
        $message = $this->conversation->messages->last();
        $this->user->write('test message 3', $this->conversation->id, $message->id);
        $this->user2->write('test message 4', $this->conversation->id);
        $this->assertSame(8, $this->conversation->unreadCount());
        $this->conversation->readAll();
        $this->assertSame(0, $this->conversation->unreadCount());
    }

    /** @test */
    public function remove_participants_of_conversation_of_2_will_throw_exception()
    {
        $this->expectException(Exceptions\CantRemoveUser::class);
        $conversation = Galera::participants(1, 2)->make();
        $conversation->remove($this->user2);
    }

    /** @test */
    public function remove_participant_who_doesnt_exist_throw_a_execption()
    {
        $this->conversation->add(3);
        $this->expectException(Exceptions\UserDoesntExist::class);
        $this->conversation->fresh()->remove(10);
    }

    /** @test */
    public function remove_participants_that_is_not_intance_of_user_will_throw_exception()
    {
        $this->conversation->add(3);
        $this->expectException(Exceptions\InvalidUserInstance::class);
        $this->conversation->fresh()->remove(new GlrConversation());
    }

    /** @test */
    public function add_user_that_doesnt_exist_thrown_exeption()
    {
        $this->expectException(Exceptions\UserDoesntExist::class);
        $this->conversation->add(5);
    }

    /** @test */
    public function add_user_that_is_already_in_the_conversation_throw_exeption()
    {
        $this->expectException(Exceptions\UserAlreadyInConversation::class);
        $this->conversation->fresh()->add($this->user2);
    }

    /** @test */
    public function add_user_that_is_not_user_class_throw_exeption()
    {
        $this->expectException(Exceptions\InvalidUserInstance::class);
        $this->conversation->add(new GlrConversation());
    }

    /** @test */
    public function create_conversation_with_invalid_user_type_will_throw_exeption()
    {
        $this->expectException(Exceptions\InvalidUserInstance::class);
        Galera::participants(new GlrConversation(), 2)->make();
    }

    /** @test */
    public function create_conversation_with_inexistant_user_type_will_throw_exeption()
    {
        $this->expectException(Exceptions\UserDoesntExist::class);
        Galera::participants(5, 1)->make();
    }

    /** @test */
    public function can_retreive_conversations_ordered_by_updated_at_and_new_messages()
    {
        $conversation1 = Galera::participants(1, 3)->make();
        $conversation2 = Galera::participants(2, 3)->make();
        $conversation3 = Galera::participants(3, 4)->make();
        sleep(1);
        $this->user3->write('test', $conversation3->id);
        sleep(1);
        $this->user4->write('test another', $conversation3->id);
        $conversations = Galera::getLastConversations();
        $this->assertTrue($conversation3->is($conversations[0]));
    }

    /** @test */
    public function can_retreive_a_conversation()
    {
        $conversation1 = Galera::participants(1, 3)->make();
        $conversation2 = Galera::participants(2, 3)->make();
        $conversation3 = Galera::participants(3, 4)->make();

        sleep(1);
        $this->user3->write('test', $conversation3->id);
        sleep(1);
        $this->user4->write('test another', $conversation3->id);
        $message = Galera::conversation($conversation3->id, true);
        //dd($message);
        $conversations = $this->user3->getLastConversations(true, 1);

        $this->assertTrue($conversation3->is($conversations[0]));
        $this->assertTrue($conversation3->is($conversations[0]));
    }
}
