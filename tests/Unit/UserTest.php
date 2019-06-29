<?php

namespace Tests\Features;

use Tests\TestCase;
use Metko\Galera\GlrMessage;
use Metko\Galera\GlrConversation;

class UserTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_has_write()
    {
        $conversation = factory(GlrConversation::class)->create();
        $message = factory(GlrMessage::class)->raw();
        $this->user->write($message, $conversation);
        $this->assertCount(1, $conversation->messages);
        $this->assertDatabaseHas('glr_messages', $message);
    }
}
