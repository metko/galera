<?php

namespace Tests\Features;

use Tests\TestCase;
use Metko\Galera\GlrConversation;
use Metko\Galera\Exceptions\UserDoesntExist;
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
        $conversation = factory(GlrConversation::class)->create();
        $conversation->add($this->user);
        $this->assertCount(1, $conversation->fresh()->participants);
    }

    /** @test */
    public function a_conversation_can_add_multiple_users_at_the_same_time()
    {
        $conversation = factory(GlrConversation::class)->create();
        $conversation->addMany([$this->user, $this->user2, $this->user3]);
        $this->assertCount(3, $conversation->fresh()->participants);
    }

    /** @test */
    public function a_conversation_can_remove_a_participant()
    {
        $conversation = factory(GlrConversation::class)->create();
        $conversation->add($this->user);
        $conversation->remove($this->user);
        $this->assertCount(0, $conversation->fresh()->participants);
    }

    /** @test */
    public function add_user_that_doesnt_exist_thrown_exeption()
    {
        $this->expectException(UserDoesntExist::class);
        $conversation = factory(GlrConversation::class)->create();
        $conversation->addMany([$this->user, 5, $this->user3]);
        $this->assertCount(2, $conversation->fresh()->participants);
    }

    /** @test */
    public function add_user_that_is_already_in_the_conversation_throw_exeption()
    {
        $this->expectException(UserAlreadyInConversation::class);
        $conversation = factory(GlrConversation::class)->create();
        $conversation->add($this->user3);
        $conversation->fresh()->add($this->user3);
    }
}
