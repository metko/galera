<?php

namespace Tests\Unit;

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
        $this->assertDatabaseHas(config('galera.table_prefix').'messages', $message);
    }

    /** @test */
    public function it_has_readMessage()
    {
        $this->conversation->add(3);
        $this->user->write('test message', $this->conversation->id);
        $message = $this->conversation->messages->first();
        $this->user2->readMessage($message->id);
        $this->assertTrue($this->user2->hasRead($message));
    }

    /** @test */
    public function it_has_canReadMessage()
    {
        $this->user->write('test message', $this->conversation->id);
        $message = $this->conversation->messages->first();
        $this->asserttrue($this->user2->canReadMessage($message));
    }
}
