<?php

namespace Metko\Galera;

use Illuminate\Support\Str;
use Metko\Galera\Exceptions\ConversationIsClosed;
use Metko\Galera\Exceptions\ConversationInvalidType;
use Metko\Galera\Exceptions\ConversationDoesntExists;

trait Galerable
{
    public function conversations()
    {
        return $this->belongsToMany(GlrConversation::class, 'glr_conversation_user', 'user_id', 'conversation_id');
    }

    public function write($message, $conversation)
    {
        if (!$conversation instanceof GlrConversation) {
            $conversation = $this->getConversation($conversation);
        }

        if ($conversation->isClosed()) {
            throw ConversationIsClosed::create($conversation->id);
        }
        if (!is_array($message)) {
            $message = ['message' => $message];
        }

        return $conversation->messages()->create($message);
    }

    protected function getConversation($conversation)
    {
        $conversationData = $conversation;

        if (is_numeric($conversation) || is_integer($conversation)) {
            $conversation = GlrConversation::find($conversation);
        } elseif (is_string($conversation)) {
            $name = Str::slug($conversation);
            $conversation = GlrConversation::wshereSlug($name)->first();
        } else {
            throw  ConversationInvalidType::create($conversationData);
        }
        if (!$conversation) {
            throw  ConversationDoesntExists::create($conversationData);
        }

        return $conversation;
    }
}
