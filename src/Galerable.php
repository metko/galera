<?php

namespace Metko\Galera;

use Metko\Galera\Facades\Galera;
use Metko\Galera\Exceptions\ConversationIsClosed;
use Metko\Galera\Exceptions\UnauthorizedConversation;

trait Galerable
{
    public function conversations()
    {
        return $this->belongsToMany(GlrConversation::class, 'glr_conversation_user', 'user_id', 'conversation_id');
    }

    public function write($message, $conversation)
    {
        if ($this->canWrite($conversation)) {
            //dd($conversation);
            if (!is_array($message)) {
                $message = ['message' => $message];
            }
            $conversation = Galera::getConversation($conversation);

            return $conversation->messages()->create($message);
        } else {
            throw UnauthorizedConversation::create();
        }
    }

    public function canWrite($conversation)
    {
        $conversation = Galera::getConversation($conversation);
        if ($conversation->isClosed()) {
            throw ConversationIsClosed::create($conversation->id);
        }

        if (!$conversation->load('participants')->hasUser($this)) {
            return false;
        }

        return true;
    }
}
