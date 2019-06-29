<?php

namespace Tests\Unit;

use Tests\TestCase;
use Metko\Galera\GlrMessage;

class MessageTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_has_isResponse()
    {
        $message = factory(GlrMessage::class)->create(['reffer_to' => 1, 'conversation_id' => 1]);
        $this->assertTrue($message->isResponse());
    }

    /** @test */
    public function it_has_responses()
    {
        $this->user->write('message', $this->conversation);
        $message1 = GlrMessage::all()->first();
        $this->user2->write('response', $this->conversation, $message1);
        $message2 = GlrMessage::all()->last();
        $this->assertInstanceOf(GlrMessage::class, $message2->reffer);
        $this->assertTrue($message2->reffer->is($message1));
    }
}
