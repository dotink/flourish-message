<?php namespace Dotink\Lab
{
	use Dotink\Flourish\Message;

	return [

		'setup' => function($data){
			needs($data['root'] . '/src/Message.php');

		},

		'tests' => [

			//
			//
			//

			'Instantiation [Name Only]' => function($data)
			{
				$msg = new Message('success');

				assert('Dotink\Flourish\Message::$name')
					-> using  ($msg)
					-> equals ('success')
				;
			},

			//
			//
			//

			'Instantiation [Name + Message]' => function($data)
			{
				$msg = new Message('success', 'This is a test message');

				assert('Dotink\Flourish\Message::$name')
					-> using  ($msg)
					-> equals ('success')
				;

				assert('Dotink\Flourish\Message::$message')
					-> using  ($msg)
					-> equals ('This is a test message')
				;
			},

			//
			//
			//

			'Instantiation [All Args]' => function($data)
			{
				$msg = new Message('success', 'test', 'This is a test message');

				assert('Dotink\Flourish\Message::$name')
					-> using  ($msg)
					-> equals ('success')
				;

				assert('Dotink\Flourish\Message::$message')
					-> using  ($msg)
					-> equals ('This is a test message')
				;

				assert('Dotink\Flourish\Message::$domain')
					-> using  ($msg)
					-> equals ('test')
				;
			},

			//
			//
			//

			'exists()' => function($data)
			{
				$msg1 = new Message('success', 'This is a test message');
				$msg2 = new Message('success', 'test', 'This is a test message');
				$msg3 = new Message('success');

				assert('Dotink\Flourish\Message::exists')
					-> using  ($msg3)
					-> equals (TRUE)

					-> using  ($msg3)
					-> with   ('test')
					-> equals (TRUE)
				;

			},

			//
			//
			//

			'retrieve()' => function($data)
			{
				$msg1 = new Message('success', 'test', 'This is a test message');
				$msg2 = new Message('success');

				$msg2->retrieve('test');

				assert('Dotink\Flourish\Message::$name')
					-> using  ($msg2)
					-> equals ('success')
				;

				assert('Dotink\Flourish\Message::$message')
					-> using  ($msg2)
					-> equals ('This is a test message')
				;

				assert('Dotink\Flourish\Message::$domain')
					-> using  ($msg2)
					-> equals ('test')
				;
			},

			//
			//
			//

			'redirect()' => function($data)
			{
				$msg1 = new Message('error', 'This is a test message');
				$msg1->redirect('test');

				$msg2 = new Message('error');
				$msg2->retrieve('test');

				$msg3 = new Message('error');
				$msg3->retrieve();

				assert('Dotink\Flourish\Message::$message')
					-> using  ($msg2)
					-> equals ('This is a test message')
				;

				assert('Dotink\Flourish\Message::$message')
					-> using  ($msg3)
					-> equals (NULL)
				;
			},

			//
			//
			//

			'compose() [Simple]' => function($data)
			{
				$msg1 = new Message('error', 'This is a test message');

				assert('Dotink\Flourish\Message::compose')
					-> using  ($msg1)
					-> equals ('<div class="error">This is a test message</div>')
				;
			},

			//
			//
			//

			'compose() [Custom Format]' => function($data)
			{
				$msg1 = new Message('error', 'This is a test message');

				assert('Dotink\Flourish\Message::compose')
					-> using  ($msg1)
					-> with   ('%n: %m')
					-> equals ('error: This is a test message')
				;
			},

			//
			//
			//

			'compose() [With Components]' => function($data)
			{
				$msg1 = new Message('error', 'This is %s message #%d');

				assert('Dotink\Flourish\Message::compose')
					-> using  ($msg1)
					-> with   ('%n: %m', 'probably', 5)
					-> equals ('error: This is probably message #5')
				;
			},

		]
	];
}
