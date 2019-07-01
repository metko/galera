<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\Models\User;
use Illuminate\Support\Str;
use Metko\Galera\GlrMessage;
use Metko\Galera\GlrConversation;

class MessageTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_has_isResponse()
    {
        $message = factory(GlrMessage::class)->create(['reffer_to' => 1, 'conversation_id' => 1, 'owner_id' => $this->user->id, 'id' => Str::uuid()]);
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

    /** @test */
    public function it_has_owner()
    {
        $this->user->write('message', $this->conversation);
        $message = GlrMessage::all()->last();
        $this->assertInstanceOf(User::class, $message->owner);
        $this->assertTrue($message->owner->is($this->user));
    }

    /** @test */
    public function it_has_conversation()
    {
        $this->user->write('message', $this->conversation);
        $message = GlrMessage::all()->last();
        $this->assertInstanceOf(GlrConversation::class, $message->conversation);
        $this->assertTrue($message->conversation->is($this->conversation));
    }
}
