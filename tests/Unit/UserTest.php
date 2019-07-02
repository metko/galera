<?php

namespace Tests\Unit;

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
        $message = factory(GlrMessage::class)->raw();
        $this->user->write($message, $this->conversation);
        $this->assertCount(1, $this->conversation->fresh()->messages);
        $this->assertDatabaseHas(config('galera.table_prefix').'messages', $message);
    }

    /** @test */
    public function it_has_read_all()
    {
        $this->user->write('test message', $this->conversation->id);
        $this->user->write('test message 2', $this->conversation->id);
        $this->user2->readAll($this->conversation->id);
        $this->assertFalse($this->user2->hasUnreadMessage());
        $this->user->write('test message 3', $this->conversation->id);
        $this->assertTrue($this->user2->hasUnreadMessage());
    }

    /** @test */
    public function it_has_hasUnreadMessage()
    {
        $conversation = Galera::participants([1, 2, 3])->make();
        $this->user->write('test message', $this->conversation->id);
        $this->user->write('test message 2', $this->conversation->id);
        $this->user3->write('testmessage 3', $conversation->id);
        $this->assertTrue($this->user->hasUnreadMessage());
        $this->assertTrue($this->user2->hasUnreadMessage());
        $this->assertCount(3, $this->user2->unreadMessages());
        $this->assertCount(2, $this->user2->unreadMessages($this->conversation->id));
        $this->assertCount(1, $this->user2->unreadMessages($conversation->id));
    }
}
