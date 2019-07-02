# galera

Galera is small package to handle conversation between two or multiple user.

### Installations
----------------
###### Step 1
```bash
composer require metko/galera 
```

###### Step 2
Publish the config file and the asset 
```bash
php artisan pusblish --tag:galera
```

How to use it
----------------
### Usage

#### Use the trait
Use the trait Galerable in your user model
```php
// Metko\Galera\Galerable;
use Galerable;
```

#### Conversation
###### Create a conversation
```php
Galera::participants($user1, $user2)->make();
*// Or pass an array of multiple user*
Galera::addParticipants([1,2,3,'10'])->make();
```

###### Get a conversation 
```php
Galera::conversation($id);
// Or with the messages
Galera::conversation($id, true);
```
*Return a conversation with [‘messages_count’, ’unread_messages_count’] by default *

###### Clear a conversation
```php
Galera::conversation($id)->clear();
```
*Will soft delete all the message of the given conversation*

###### Close a conversation
```php
Galera::conversation($id)->close(); return Metko\Galera\Conversation
```
*Nobody can submit a message in a close conversation*

###### Check if a conversation is closed
```php
Galera::conversation($id)->isCLosed(); // return bool
```

###### Add participants in a conversation
```php
Galera::conversation($id)->add(1);
// Or many user at the same time
Galera::conversation($id)->addMany([1, $user2, '3']); 
```

###### A Conversation can remove a participant
```php
Galera::conversation($id)->remove(1); 
```
*A conversation must have at least 2 participants. It will return an error if you try to do it.*

###### Read all message of a conversation
```php
Galera::conversation($id)->readAll(); 
```
*A conversation must have at least 2 participants. It will return an error if you try to do it.*

#### User
###### A user can write a message
```php
$user->write(‘My message’, $conversationId); // You can pass a model or an id for the conversation 
```

###### Delete a message
```php
Galera::message(1)->delete(); // You can pass a model or an id for the conversation param*
```

###### Write a message refering another
```php
$user->write(‘My message’, $conversationId, $message->id); // You can pass a model or an id for the message param*
```

###### Check if a user has unread message on a specific conversation
```php
$user->hasUnreadMessage($convzersationId); // Return bool
//Or in all conversation where he is participant
$user->hasUnreadMessage(); // Return bool
```

###### Get unread messages for user
```php
$user->unreadMessages(); 
```

###### Read all the message unread for a user in a conversation
```php
$user->readAll($convzersationId); // Return Collection
```
*Return a list of all conversation ordered by updated_at, count of message, and unread_message_count*

###### Get the last conversation of a user
```php
$user->getLastConversation($withMessage = false, $nbMessage = 25); // Return Collection
```
*Return a list of all conversation ordered by updated_at, count of message, and unread_message_count*


#### Messagess
###### Get messages from a conversation
```php
Galera::ofConversation($conversationId)->get(); 
```

###### Get a unread_message count  in conversation
```php
Galera::conversation($id)->unread_messages_count; 
```

###### Get a total_message  count n conversation
```php
Galera::conversation($id)->messages_count; 
```



Test cases
----------
 
The package includes three test cases:

* `TestCase` - Effectively the normal Laravel test case. Use it the same way you would your normal Laravel test case
* `SimpleTestCase` - Extends the default PHPUnit test case, so it doesn’t set up a Laravel application, making it quicker and well-suited to properly isolated unit tests
* `BrowserKitTestCase` - Sets up BrowserKit

