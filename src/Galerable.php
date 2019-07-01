<?php

namespace Metko\Galera;

use Illuminate\Support\Str;
use Metko\Galera\Facades\Galera;
use Metko\Galera\Events\MessageWasSent;
use Metko\Galera\Exceptions\ConversationIsClosed;
use Metko\Galera\Exceptions\UnauthorizedConversation;
use Metko\Galera\Exceptions\MessageDoesntBelongsToUser;
use Metko\Galera\Exceptions\MessageDoesntBelongsToConversation;

trait Galerable
{
    public function conversations()
    {
        return $this->belongsToMany(GlrConversation::class, config('galera.table_prefix').'conversation_user', 'user_id', 'conversation_id');
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

            $message['id'] = Str::uuid();
            $message['owner_id'] = $this->id;
            $message = $conversation->messages()->create($message);

            $participants = $conversation->participants->filter(function ($user, $key) {
                if ($user->id != $this->id) {
                    return $user;
                }
            });
            foreach ($participants as $user) {
                $notification = $message->status()->create(['id' => Str::uuid(), 'to_user_id' => $user->id, 'from_user_id' => $this->id, 'conversation_id' => $conversation->id]);
            }

            event(new MessageWasSent($message));
        } else {
            throw UnauthorizedConversation::create();
        }
    }

    public function canReply($reffer)
    {
    }

    public function readMessage($message)
    {
        $message = Galera::message($message);
        //dd($message->status);

        if ($this->canReadMessage($message)) {
            //dd($message->status);
            $noti = $message->status->filter(function ($status, $key) use ($message) {
                if ($status->to_user_id == $this->id &&
                    $status->conversation_id == $message->conversation_id) {
                    return $status;
                }
            })->first();

            return $noti->update(['read_at' => now()]);
        } else {
            throw MessageDoesntBelongsToUser::create($message->id);
        }
    }

    public function hasRead($message)
    {
        $message = Galera::message($message);
        if ($this->canReadMessage($message)) {
            //dd($message->status);
            $noti = $message->status->filter(function ($status, $key) use ($message) {
                if ($status->to_user_id == $this->id &&
                    $status->conversation_id == $message->conversation_id) {
                    return $status;
                }
            })->first();

            if ($noti->read_at) {
                return true;
            }
        }

        return false;
    }

    public function canReadMessage($message)
    {
        $message = Galera::hasNotification($this, $message);
        if ($message) {
            return true;
        }

        return false;
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
