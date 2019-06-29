<?php

namespace Metko\Galera;

use Tests\Models\User;
use Illuminate\Support\Str;
use Metko\Galera\Exceptions\UserDoesntExist;
use Metko\Galera\Exceptions\InvalidUserInstance;
use Metko\Galera\Exceptions\ConversationInvalidType;
use Metko\Galera\Exceptions\ConversationDoesntExists;

class GaleraClass
{
    public $participants;
    public $conversation;
    public $closed = false;

    public function isValidUser($user)
    {
        if (is_object($user) && !$user instanceof User) {
            throw InvalidUserInstance::create($user);
        }

        if (!$user instanceof User) {
            if (is_numeric($user) || is_integer($user)) {
                if (!$user = User::whereId($user)->first()) {
                    throw UserDoesntExist::create($user);
                }
            }
        }

        return $user;
    }

    public function create()
    {
        $this->conversation = GlrConversation::create($this->defaultConversation());
        if ($this->participants) {
            if (is_array($this->participants)) {
                //dd($this->participants);
                $this->conversation->addMany($this->participants);
            } else {
                $this->conversation->add($this->participants);
            }
        }

        return $this->conversation;
    }

    public function addParticipants($participants)
    {
        $this->participants = $participants;

        return $this;
    }

    protected function defaultConversation()
    {
        return [
            'closed' => $this->closed,
        ];
    }

    public function getConversation($conversation)
    {
        $conversationData = $conversation;
        if ($conversation instanceof GlrConversation) {
            return $conversation;
        }
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
