<?php

namespace Metko\Galera;

use Metko\Galera\Facades\Galera;
use Metko\Galera\Events\MessageSent;
use Metko\Galera\Exceptions\ConversationIsClosed;
use Metko\Galera\Exceptions\UnauthorizedConversation;
use Metko\Galera\Exceptions\MessageDoesntBelongsToConversation;

trait Galerable
{
    public function conversations()
    {
        return $this->belongsToMany(GlrConversation::class, 'glr_conversation_user', 'user_id', 'conversation_id');
    }

    public function write($message, $conversation, $response_to = null)
    {
        if ($this->canWrite($conversation)) {
            //dd($conversation);
            if (!is_array($message)) {
                $message = ['message' => $message];
            }

            $conversation = Galera::conversation($conversation);

            if ($response_to) {
                $reffer = Galera::message($response_to);

                if (!Galera::messageBelongsToConversation($reffer, $conversation)) {
                    throw MessageDoesntBelongsToConversation::create($reffer->id);
                }
                $message['reffer_to'] = $reffer->id;
            }

            $message['owner_id'] = $this->id;
            $message = $conversation->messages()->create($message);
            event(new MessageSent($message));
        } else {
            throw UnauthorizedConversation::create();
        }
    }

    public function canReply($reffer)
    {
    }

    public function canWrite($conversation)
    {
        $conversation = Galera::conversation($conversation);
        if ($conversation->isClosed()) {
            throw ConversationIsClosed::create($conversation->id);
        }

        if (!$conversation->load('participants')->hasUser($this)) {
            return false;
        }

        return true;
    }
}
