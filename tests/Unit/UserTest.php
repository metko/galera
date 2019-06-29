<?php

namespace Tests\Features;

use Tests\TestCase;
use Metko\Galera\GlrMessage;
use Metko\Galera\Facades\Galera;

class UserTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_has_write()
    {
        $conversation = Galera::addParticipants([1, 2])->create();
        $message = factory(GlrMessage::class)->raw();
        $this->user->write($message, $conversation);
        $this->assertCount(1, $conversation->fresh()->messages);
        $this->assertDatabaseHas('glr_messages', $message);
    }
}
