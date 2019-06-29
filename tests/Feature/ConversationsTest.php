<?php

namespace Tests\Features;

use Tests\TestCase;
use Metko\Galera\Facades\Galera;
use Metko\Galera\GlrConversation;
use Metko\Galera\Exceptions\UserDoesntExist;
use Metko\Galera\Exceptions\InvalidUserInstance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Metko\Galera\Exceptions\UserAlreadyInConversation;

class ConversationsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function a_conversation_can_add_a_user()
    {
        $conversation = Galera::addParticipants($this->user)->create();
        $this->assertCount(1, $conversation->fresh()->participants);
    }

    /** @test */
    public function a_conversation_can_add_multiple_users_at_the_same_time()
    {
        $conversation = Galera::addParticipants([1, '2', $this->user3])->create();
        $this->assertCount(3, $conversation->fresh()->participants);
    }

    /** @test */
    public function a_conversation_can_remove_a_participant()
    {
        $conversation = Galera::addParticipants($this->user)->create();
        $conversation->remove($this->user);
        $this->assertCount(0, $conversation->fresh()->participants);
    }

    /** @test */
    public function remove_participant_who_doesnt_exist_throw_a_execption()
    {
        $this->expectException(UserDoesntExist::class);
        $conversation = Galera::create();
        $conversation->fresh()->remove(10);
    }

    /** @test */
    public function remove_participants_that_is_not_intance_of_user_will_throw_exception()
    {
        $this->expectException(InvalidUserInstance::class);
        $conversation = Galera::create();
        $fake = new GlrConversation();
        $conversation->fresh()->remove($fake);
    }

    /** @test */
    public function add_user_that_doesnt_exist_thrown_exeption()
    {
        $this->expectException(UserDoesntExist::class);
        $conversation = Galera::addParticipants([$this->user, 5, $this->user3])->create();
        $this->assertCount(2, $conversation->fresh()->participants);
    }

    /** @test */
    public function add_user_that_is_already_in_the_conversation_throw_exeption()
    {
        $this->expectException(UserAlreadyInConversation::class);
        $conversation = Galera::addParticipants($this->user3)->create();
        $conversation->fresh()->add($this->user3);
    }

    /** @test */
    public function add_user_that_is_not_user_class_throw_exeption()
    {
        $this->expectException(InvalidUserInstance::class);
        $fake = new GlrConversation();
        $conversation = Galera::addParticipants($fake)->create();
    }
}
