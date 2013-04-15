# Message

The `Message` class is a simple session-based messaging tool that allows for various notifications, success, and error messages to be passed cleanly between multiple areas of code.


## Creating Messages

```php
$success = new Message('success', 'Your order was successfully processed.');
```

The first parameter is the name of the message, essentially a key which will allow you to later recall that message.  In addition to this key, an optional domain can be specified.

```php
$success = new Message('success', 'checkout', 'Your order was successfully processed');
```

The domain of the messaging allows for modular translations as well as for ensuring various modular components do not step on each others toes by overwriting their success/error messages if they are intermediately accessed by the user.


## Retrieving a Message

```php
$success = new Message('success');
$success->retrieve('checkout');
```

## Composing a Message

Messages by default are used for outputting notifications to a screen, usually in HTML.  For this reason the default message format looks like the following:

```html
<div class="%n">
	%m
</div>
```

In the above example `%n` represents the name of the message, such as 'success' or 'error' and the `%m` represents the message itself.

```php
$success = new Message('success');
$success->retrieve('checkout');

echo $success->compose();
```

**Output**
```html
<div class="success">
	Your order was successfully processed.
</div>
```

You can pass an alternative formatting string to the `compose()` method using `%n` and `%m` as placeholders for the name and the message.

```php
echo $success->compose('%n: %m');
```

**Output**
```
success: Your order was sucessfully processed.
```

In addition to this, the message itself can contain any valid `sprintf()` placeholders which can be filled in when the message is composed.

```php
$num_users = 5;
$group     = 'Admins'
$success   = new Message('success', '%d users have been removed from the %s group.');

echo $success->compose(NULL, $num_users, $group);
```

**Output**
```html
<div class="success">
	5 users have been removed from the Admins group.
</div>
```

Note that in the above example the default format is used by explicitly passing NULL as the parameter.


## Translations

The message class will render all messages using the `Text` class if it is available.


## Comparing

You can check/compare a message's name to a string since the `__toString()` method will return the name of the message.  This is useful if you receive a message from another component and need to know if it a certain type of message.

```php
if ($message == 'success') {

	//
	// Do something specific to success
	//

}
```
