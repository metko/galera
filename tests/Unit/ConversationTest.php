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
        $message = factory(GlrMessage::class)->create(['conversation_id' => $conversation->id]);
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
        $conversation = factory(GlrConversation::class)->create();
        $conversation->add($this->user);
        $conversation->remove($this->user);
        $this->assertFalse($conversation->fresh()->hasUser($this->user));
    }

    /** @test */
    public function it_has_close()
    {
        $this->withoutExceptionHandling();
        $conversation = factory(GlrConversation::class)->create();
        $conversation->close();
        $this->assertTrue($conversation->isClosed());
    }

    /** @test */
    public function it_has_isClosed()
    {
        $conversation = factory(GlrConversation::class)->create();
        $conversation->close();
        $this->assertTrue($conversation->isClosed());
    }
}
