<?php

namespace Metko\Galera;

use Illuminate\Support\Str;
use Metko\Galera\Facades\Galera;
use Metko\Galera\Exceptions\ConversationIsClosed;
use Metko\Galera\Exceptions\UnauthorizedConversation;
use Metko\Galera\Exceptions\MessageDoesntBelongsToConversation;

trait Galerable
{
    /**
     * conversations.
     */
    public function conversations()
    {
        return $this->belongsToMany(GlrConversation::class, config('galera.table_prefix').'conversation_user', 'user_id', 'conversation_id')
                ->orderby('updated_at', 'desc');
    }

    public function getLastConversations($withMessage = false, $nbMessages = 25)
    {
        $conversations = GlrConversation::whereHas('participants', function ($query) {
            $query->where('user_id', $this->id);
        })->with('participants');

        if ($withMessage) {
            $conversations = $conversations->with(['messages' => function ($query) use ($nbMessages) {
                $query->take($nbMessages)->orderBy('created_at', 'desc');
            }]);
        }

        $conversations = $conversations->withCount([
            'messages',
            'status as unread_messages_count' => function ($query) {
                $query->where('read_at', null)->where('to_user_id', $this->id);
            },
        ]);

        $conversations = $conversations->orderBy('updated_at', 'desc');

        return $conversations->get();
    }

    /**
     * conversations.
     */
    public function messages()
    {
        return $this->hasMany(GlrMessage::class, 'owner_id');
    }

    /**
     * write.
     *
     * @param mixed $message
     * @param mixed $conversation
     * @param mixed $response_to
     */
    public function write($message, $conversationId, $response_to = null)
    {
        if (!$conversationId instanceof GlrConversation) {
            $conversation = Galera::conversation($conversationId);
        } else {
            $conversation = $conversationId;
        }

        if ($this->canWrite($conversation)) {
            if (!is_array($message)) {
                $message = ['message' => $message];
            }

            if ($response_to) {
                $reffer = Galera::message($response_to);

                if (!Galera::messageBelongsToConversation($reffer, $conversation)) {
                    throw MessageDoesntBelongsToConversation::create($reffer->id);
                }
                $message['reffer_to'] = $reffer->id;
            }

            $message['id'] = Str::uuid();
            $message['owner_id'] = $this->id;
            $message = $conversation->messages()->create($message);

            Galera::sendNotification($conversation, $this, $message);
        } else {
            throw UnauthorizedConversation::create();
        }
    }

    /**
     * canWrite.
     *
     * @param mixed $conversation
     */
    public function canWrite($conversation)
    {
        if ($conversation->isClosed()) {
            throw ConversationIsClosed::create($conversation->id);
        }

        if (!$conversation->load('participants')->hasUser($this)) {
            return false;
        }

        return true;
    }

    /**
     * readAll.
     *
     * @param mixed $conversation_id
     */
    public function readAll($conversation_id)
    {
        return GlrMessageNotification::unreadMessagesUser($this->id)
                    ->where('conversation_id', $conversation_id)
                    ->where('read_at', null)
                    ->update(['read_at' => now()]);
    }

    /**
     * unreadMessages.
     *
     * @param mixed $conversation_id
     */
    public function unreadMessages($conversation_id = null)
    {
        $conversation = GlrMessageNotification::unreadMessagesUser($this->id);
        if ($conversation_id) {
            $conversation = $conversation->where('conversation_id', $conversation_id);
        }
        $conversation = $conversation->get();

        return $conversation;
    }

    /**
     * hasUnreadMessage.
     *
     * @param mixed $conversation_id
     */
    public function hasUnreadMessage($conversation_id = null)
    {
        $message = $conversation_id ? $this->unreadMessages($conversation_id) : $this->unreadMessages();
        if ($message->first()) {
            return true;
        }

        return false;
    }
}
