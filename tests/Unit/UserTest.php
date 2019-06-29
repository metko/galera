<?php

namespace Tests\Features;

use Tests\TestCase;
use Metko\Galera\GlrMessage;

class UserTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_has_write()
    {
        $message = factory(GlrMessage::class)->raw();
        $this->user->write($message, $this->conversation);
        $this->assertCount(1, $this->conversation->fresh()->messages);
        $this->assertDatabaseHas('glr_messages', $message);
    }
}
