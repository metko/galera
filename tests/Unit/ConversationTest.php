<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\Models\User;
use Illuminate\Support\Str;
use Metko\Galera\GlrMessage;
use Metko\Galera\Facades\Galera;
use Metko\Galera\GlrConversation;

class ConversationTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_have_subject_and_description()
    {
        $attr = [
            'subject' => 'Subject of the conversation',
            'description' => 'Description of the conversation',
        ];
        $conversation = Galera::participants(1, 2)
                        ->subject($attr['subject'])
                        ->description($attr['description'])
                        ->make();

        $this->assertSame($conversation->description, $attr['description']);
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
        $message = factory(GlrMessage::class)->create(['conversation_id' => $conversation->id, 'owner_id' => $this->user->id, 'id' => Str::uuid()]);
        $this->assertTrue($conversation->fresh()->messages->first()->is($message));
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
        $this->assertDatabaseHas(config('galera.table_prefix').'conversations', ['closed' => true]);
    }

    /** @test */
    public function it_has_clear()
    {
        $this->user->write('Message', $this->conversation->id);
        $this->user2->write('Message', $this->conversation->id);
        $this->user->write('Message', $this->conversation->id);
        $this->conversation->fresh()->clear();
        $this->assertCount(0, $this->conversation->fresh()->messages);
    }

    /** @test */
    public function it_has_unreadMessages()
    {
        $this->user->write('test message', $this->conversation->id);
        $this->user2->write('test message', $this->conversation->id);
        $this->assertCount(2, $this->conversation->unreadMessages());
    }

    /** @test */
    public function it_has_unreadCount()
    {
        $this->user->write('test message', $this->conversation->id);
        $this->user2->write('test message', $this->conversation->id);
        $this->assertSame(2, $this->conversation->unreadCount());
    }

    /** @test */
    public function it_has_readAll()
    {
        $this->user->write('test message', $this->conversation->id);
        $this->user2->write('test message', $this->conversation->id);
        $this->conversation->readAll();
        $this->assertSame(0, $this->conversation->unreadCount());
    }
}
