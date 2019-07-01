# galera

Galera is small package to handle conversation between two or multiple user.

Installations
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

###### Create a conversation
```php
Galera::addParticipants($user1, $user2)->make();
// Or pass an array of multiple user
Galera::addParticipants([1,2,3,"10"])->make();
```

###### Get a conversation 
```php
Galera::conversation(1);
//Or get by title slugged
Galera::conversation('My conversation'); // automatically becomes 'my-conversation'
```

###### Clear a conversation
```php
Galera::conversation(1)->clear();
```

###### Write a messsage
```php
$user->write('My message', $conversation); // You can pass a model or an id for the conversation param
```

###### Write a messsage reffering another
```php
$user->write('My message', $conversation, $message->id); // You can pass a model or an id for the message param
```

###### Get a messsage
```php
Galera::messages(1);
```

Test cases
----------
 
The package includes three test cases:

* `TestCase` - Effectively the normal Laravel test case. Use it the same way you would your normal Laravel test case
* `SimpleTestCase` - Extends the default PHPUnit test case, so it doesn't set up a Laravel application, making it quicker and well-suited to properly isolated unit tests
* `BrowserKitTestCase` - Sets up BrowserKit
