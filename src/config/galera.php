<?php

return [
      /*w
      /* User class to be used, by default it's App\User, but feel freen to change it as your need
      */
      'user_class' => 'App\User',

      /*
      /* Table prefix, leave empty for null
      */
      'table_prefix' => 'glr_',

      /*
      * The event to fire when a message is sent
      * See Musonza\Chat\Eventing\MessageWasSent if you want to customize.
      */
      'sent_message_event' => 'Metko\Galera\Events\MessageWasSent',

      /*
      * This will allow you to broadcast an event when a message is sent
      * Example:
      * Channel: mc-chat-conversation.2,
      * Event: Musonza\Chat\Eventing\MessageWasSent
      */
      'broadcasts' => false,
];
