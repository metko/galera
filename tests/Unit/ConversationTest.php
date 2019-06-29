<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\Models\User;
use Metko\Galera\GlrMessage;
use Metko\Galera\GlrConversation;

class ConversationTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_has_participants()
    {
        $conversation = factory(GlrConversation::class)->create();
        $conversation->add($this->user);
        $this->assertTrue($conversation->fresh()->participants->first()->is($this->user));
    }

    /** @test */
    public function it_has_messages()
    {
        $conversation = factory(GlrConversation::class)->create();
        $message = factory(GlrMessage::class)->create(['conversation_id' => $conversation->id, 'owner_id' => $this->user->id]);
        $this->assertTrue($conversation->messages->first()->is($message));
    }

    /** @test */
    public function it_has_add()
    {
        $conversation = factory(GlrConversation::class)->create();
        $conversation->add($this->user);
        $this->assertInstanceOf(User::class, $conversation->fresh()->participants->first());
    }

    /** @test */
    public function it_has_addMany()
    {
        $conversation = factory(GlrConversation::class)->create();
        $conversation->addMany([$this->user, $this->user2, $this->user3]);
        $this->assertCount(3, $conversation->fresh()->participants);
    }

    /** @test */
    public function it_has_hasUser()
    {
        $conversation = factory(GlrConversation::class)->create();
        $conversation->add($this->user);
        $this->assertTrue($conversation->fresh()->hasUser($this->user));
    }

    /** @test */
    public function it_has_remove()
    {
        $this->conversation->addMany([$this->user3, 4]);
        $this->conversation->fresh()->remove($this->user);
        $this->assertFalse($this->conversation->hasUser($this->user));
    }

    /** @test */
    public function it_has_close()
    {
        $this->conversation->close();
        $this->assertTrue($this->conversation->isClosed());
    }

    /** @test */
    public function it_has_isClosed()
    {
        $this->conversation->close();
        $this->assertTrue($this->conversation->isClosed());
        $this->assertDatabaseHas('glr_conversations', ['closed' => true]);
    }
}
