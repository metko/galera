<?php

namespace Tests\Features;

use Tests\TestCase;
use Metko\Galera\Facades\Galera;
use Metko\Galera\GlrConversation;
use Metko\Galera\Exceptions\CantRemoveUser;
use Metko\Galera\Exceptions\UserDoesntExist;
use Metko\Galera\Exceptions\InvalidUserInstance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Metko\Galera\Exceptions\InsufisantParticipant;
use Metko\Galera\Exceptions\UserAlreadyInConversation;

class ConversationsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function create_with_less_of_2_participants_will_throw_an_exception()
    {
        $this->expectException(InsufisantParticipant::class);
        $conversation = Galera::addParticipants($this->user)->create();
    }

    /** @test */
    public function a_conversation_can_add_a_user()
    {
        $conversation = Galera::addParticipants($this->user, 3)->create();
        $conversation->fresh()->add(2);
        $this->assertCount(3, $conversation->fresh()->participants);
    }

    /** @test */
    public function a_conversation_can_add_multiple_users_at_the_same_time()
    {
        $conversation = Galera::addParticipants(1, 2)->create();
        $conversation->addMany([$this->user3, '4']);
        $this->assertCount(4, $conversation->fresh()->participants);
    }

    /** @test */
    public function a_conversation_can_remove_a_participant()
    {
        $conversation = Galera::addParticipants([1, 2, 3])->create();
        $conversation->fresh()->remove($this->user2);
        $this->assertCount(2, $conversation->fresh()->participants);
    }

    /** @test */
    public function remove_participants_of_conversation_of_2_will_throw_exception()
    {
        $this->expectException(CantRemoveUser::class);
        $conversation = Galera::addParticipants(1, 2)->create();
        $conversation->remove($this->user2);
    }

    /** @test */
    public function remove_participant_who_doesnt_exist_throw_a_execption()
    {
        $this->conversation->add(3);
        $this->expectException(UserDoesntExist::class);
        $this->conversation->fresh()->remove(10);
    }

    /** @test */
    public function remove_participants_that_is_not_intance_of_user_will_throw_exception()
    {
        $this->conversation->add(3);
        $this->expectException(InvalidUserInstance::class);
        $this->conversation->fresh()->remove(new GlrConversation());
    }

    /** @test */
    public function add_user_that_doesnt_exist_thrown_exeption()
    {
        $this->expectException(UserDoesntExist::class);
        $this->conversation->add(5);
    }

    /** @test */
    public function add_user_that_is_already_in_the_conversation_throw_exeption()
    {
        $this->expectException(UserAlreadyInConversation::class);
        $this->conversation->fresh()->add($this->user2);
    }

    /** @test */
    public function add_user_that_is_not_user_class_throw_exeption()
    {
        $this->expectException(InvalidUserInstance::class);
        $this->conversation->add(new GlrConversation());
    }

    /** @test */
    public function create_conversation_with_invalid_user_type_will_throw_exeption()
    {
        $this->expectException(InvalidUserInstance::class);
        Galera::addParticipants(new GlrConversation(), 2)->create();
    }

    /** @test */
    public function create_conversation_with_inexistant_user_type_will_throw_exeption()
    {
        $this->expectException(UserDoesntExist::class);
        Galera::addParticipants(5, 1)->create();
    }
}
