# Flourish/Message
## Session based messaging for page-to-page alerts and notifications

The `Message` class provides a simple way to set messages (usually in a controller) and then retrieve them after a redirect or on another page.  This is useful when showing success and error messages, but can also be used for basic alerts or informational pieces that might be raised in some conditions and only shown in others across multiple requests.

Creating a new message object with an actual message will store it in the session:

```php
$message = new Message('success', 'Thanks %s, your profile has been saved!');
```

In most use cases, such a message would be set for example during the `POST` of a form (if successful), then the user might be redirected to a totally different controller, which in turn could retrieve the message and display it:

```php
$message = new Message('success');
$message->retrieve();

echo $message->compose(NULL, $user->getFirstName());
```
