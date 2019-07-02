<?php

namespace Metko\Galera;

use Illuminate\Support\Str;
use Metko\Galera\Events\MessageWasSent;
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

    /**
     * Add subject to conversation.
     *
     * @param mixed $subject
     */
    public function subject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Add description to the converation.
     *
     * @param mixed $description
     */
    public function description($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * From user on sending message from the facade.
     *
     * @param mixed $user
     */
    public function from($user)
    {
        if ($user = $this->isValidUser($user)) {
            $this->from = $user;
        }

        return $this;
    }

    /**
     * To user on sending message from the facade.
     *
     * @param mixed $user
     */
    public function to($user)
    {
        if ($user = $this->isValidUser($user)) {
            $this->to = $user;
        }

        return $this;
    }

    /**
     * Conversation on sending message from the facade.
     *
     * @param mixed $conversation
     */
    public function in($conversation)
    {
        if ($conversation = $this->conversation($conversation->id)) {
            $this->conversation = $conversation;
        }

        return $this;
    }

    /**
     * Send the message with the facade.
     *
     * @param mixed $message
     */
    public function send($message)
    {
        $this->conversation->load('participants');
        if (!empty($this->conversation) && $this->conversation->hasUser($this->to)) {
            return $this->from->write($message, $this->conversation->id);
        }

        $this->conversation = self::participants($this->from, $this->to)->make();

        $this->from->write($message, $this->conversation->id);

        return $this->conversation;
    }

    /**
     * Default params of the conversation;.
     */
    protected function defaultConversation()
    {
        return [
            'closed' => $this->closed,
            'subject' => $this->subject ?? '',
            'description' => $this->description ?? '',
        ];
    }

    /**
     * Check if the user is valid before adding it in a conversation.
     *
     * @param mixed $user
     */
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

    /**
     * Set the participants before make().
     *
     * @param mixed $participants
     * @param mixed $second
     */
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

    /**
     * Make a conversation.
     */
    public function make()
    {
        $this->conversation = GlrConversation::create($this->defaultConversation());
        $this->addParticipants();

        return $this->conversation;
    }

    /**
     * Sync the participants with the property participants.
     */
    protected function addParticipants()
    {
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
    }

    /**
     * Retreive the conversation.
     *
     * @param mixed $conversation
     */
    public function conversation($conversationId, $withMessages = false)
    {
        if (!is_numeric($conversationId) || !is_integer($conversationId)) {
            throw  ConversationInvalidType::create($conversationId);
        }
        $conversation = GlrConversation::whereId($conversationId)
            ->withCount([
                'messages',
                'status as unread_messages' => function ($query) {
                    $query->where('read_at', null);
                },
            ]);

        if ($withMessages) {
            $conversation = $conversation->with(['messages' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }]);
        }

        $conversation = $conversation->first();
        if (!$conversation) {
            throw  ConversationDoesntExists::create($conversationId);
        }

        return $conversation;
    }

    /**
     * Retreive the message.
     *
     * @param mixed $message
     */
    public function message($message)
    {
        if ($message instanceof GlrMessage) {
            return $message;
        }
        $messageId = $message;
        $message = GlrMessage::whereId($message)->first();
        if (!$message) {
            throw MessageDoesntExist::create($messageId);
        }

        if (!$message instanceof GlrMessage) {
            throw MessageInvalidType::create();
        }

        return $message;
    }

    /**
     * Check if a message belongs to a conversation.
     *
     * @param mixed $message
     * @param mixed $conversation
     */
    public function messageBelongsToConversation($message, $conversation)
    {
        return $conversation->messages->contains('id', $message->id);
    }

    /**
     * Send notification after user write a message.
     *
     * @param mixed $conversation
     * @param mixed $from_user
     * @param mixed $message
     */
    public function sendNotification($conversation, $from_user, $message)
    {
        $participants = $conversation->participants->filter(function ($user, $key) use ($from_user) {
            if ($user->id != $from_user->id) {
                return $user;
            }
        });

        foreach ($participants as $user) {
            $notification = $message->status()->create([
                'id' => Str::uuid(),
                'to_user_id' => $user->id,
                'from_user_id' => $from_user->id,
                'conversation_id' => $conversation->id,
            ]);
            event(new MessageWasSent($user, $message));
        }
    }

    public function all()
    {
        $this->query = GlrConversation::all();

        return $this->query;
    }

    public function getLastConversations()
    {
        $conversations = GlrConversation::with(['messages' => function ($query) {
            $query->orderBy('updated_at', 'desc');
        }])->orderBy('updated_at', 'desc')->get();

        return $conversations;
    }

    public function ofConversation($conversation, $nbMessages = 25)
    {
        if ($conversation instanceof GlrConversation) {
            $conversation = $conversation->id;
        }

        return GlrMessage::ofConversation($conversation);
    }
}
