<?php

namespace Metko\Galera;

use Illuminate\Support\Str;
use Metko\Galera\Exceptions\UserDoesntExist;
use Metko\Galera\Exceptions\MessageDoesntExist;
use Metko\Galera\Exceptions\MessageInvalidType;
use Metko\Galera\Exceptions\InvalidUserInstance;
use Metko\Galera\Exceptions\InsufisantParticipant;
use Metko\Galera\Exceptions\ConversationInvalidType;
use Metko\Galera\Exceptions\ConversationDoesntExists;

class Galera
{
    public $participants;
    public $conversation;
    public $closed = false;

    public function __construct()
    {
        $this->userInstance = config('galera.user_class');
    }

    public function isValidUser($user)
    {
        if (is_object($user) && !$user instanceof $this->userInstance) {
            throw InvalidUserInstance::create($user);
        }

        if (!$user instanceof $this->userInstance) {
            if (is_numeric($user) || is_integer($user)) {
                if (!$user = $this->userInstance::whereId($user)->first()) {
                    throw UserDoesntExist::create($user);
                }
            }
        }

        return $user;
    }

    public function test()
    {
        return $this->participants;
    }

    public function make()
    {
        $this->conversation = GlrConversation::create($this->defaultConversation());
        if ($this->participants) {
            if (is_array($this->participants)) {
                $this->conversation->addMany($this->participants);
            } else {
                $this->conversation->add($this->participants);
            }
            $this->participants = null;
        } else {
            throw InsufisantParticipant::create();
        }

        return $this->conversation;
    }

    public function participants($participants, $second = null)
    {
        if (!$second && !is_array($participants)) {
            throw InsufisantParticipant::create();
        }

        if (is_array($participants)) {
            if (count($participants) < 2) {
                throw InsufisantParticipant::create();
            }
            $this->participants = $participants;

            return $this;
        }
        $this->participants = [$participants, $second];

        return $this;
    }

    protected function defaultConversation()
    {
        return [
            'closed' => $this->closed,
        ];
    }

    public function conversation($conversation)
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

    public function message($message)
    {
        if (is_numeric($message) || is_integer($message)) {
            $messageId = $message;
            $message = GlrMessage::whereId($message)->first();
            if (!$message) {
                throw MessageDoesntExist::create($messageId);
            }
        }
        if (!$message instanceof GlrMessage) {
            throw MessageInvalidType::create();
        }

        return $message;
    }

    public function messageBelongsToConversation($message, $conversation)
    {
        return $conversation->messages->contains('id', $message->id);
    }
}
