<?php namespace Dotink\Flourish
{
	/**
	 * Provides session-based messaging for page-to-page communication
	 *
	 * @copyright  Copyright (c) 2007-2012 Will Bond, others
	 * @author     Will Bond           [wb]  <will@flourishlib.com>
	 * @author     Matthew J. Sahagian [mjs] <msahagian@dotink.org>
	 *
	 * @license    Please reference the LICENSE.md file at the root of this distribution
	 *
	 * @package    Flourish
	 */
	class Message
	{
		const DEFAULT_DOMAIN = 'messages';
		const DEFAULT_FORMAT = '<div class="%n">%m</div>';
		const KEY_SEPARATOR  = '::';

		/**
		 * The domain of the message
		 *
		 * @access private
		 * @var string
		 */
		private $domain = NULL;


		/**
		 * The text of the message
		 *
		 * @access private
		 * @var string
		 */
		private $message = NULL;


		/**
		 * The name of the message
		 *
		 * @access private
		 * @var string
		 */
		private $name = NULL;


		/**
		 * Get a normalized key for the message
		 *
		 * @access private
		 * @param string $name The name of the message
		 * @param string $domain The domain of the message, default NULL
		 * @return string The normalized key
		 */
		static private function getKey($name, $domain = NULL)
		{
			if ($domain === NULL) {
				$domain = self::DEFAULT_DOMAIN;
			}

			return implode(self::KEY_SEPARATOR, array(__CLASS__, $domain, $name));
		}


		/**
		 * Creates a new message
		 *
		 * @access public
		 * @param string $name The name of the message
		 * @param string $domain The domain of the message, default NULL
		 * @param string $message The meessage text, default NULL
		 * @return void
		 */
		public function __construct($name, $domain = NULL, $message = NULL)
		{
			$this->name = (string) $name;

			if (func_num_args() == 2) {
				$this->message = func_get_arg(1);
				$this->domain  = NULL;
			} else {
				$this->message = $message;
				$this->domain  = $domain;
			}

			if (isset($this->message)) {
				$_SESSION[self::getKey($this->name, $this->domain)] = $this->message;
			}
		}


		/**
		 * Represents the name of the message
		 *
		 * @access public
		 * @return string The message name
		 */
		public function __toString()
		{
			return $this->name;
		}


		/**
		 * Composes a message for output with a provided format
		 *
		 * The format parameter is an sprintf style string which can contain %n to place the name
		 * of the message, and %m to place the contents.
		 *
		 * @access public
		 * @param string $domain The domain to show the message from
		 * @param string $format The format of the outputted message
		 * @return string The name and/or message placed in the format string
		 *
		 */
		public function compose($format = NULL)
		{
			$args    = array_slice(func_get_args(), 1);
			$format  = !$format
				? self::DEFAULT_FORMAT
				: $format;

			$message = class_exists(__NAMESPACE__ . '\Text')
				? Text::create($this->message)->compose($this->domain, $args)
				: vsprintf($this->message, $args);

			if (preg_match_all('#[%]+(n|m)#', $format, $matches)) {
				foreach ($matches[0] as $i => $token) {
					$num_percents = strlen(rtrim($token, 'nm'));

					if ($num_percents % 2 == 1) {
						switch ($matches[1][$i]) {
							case 'n':
								$replacement = str_replace('%n', $this->name, $token);
								$format      = str_replace($token, $replacement, $format);
								break;
							case 'm':
								$replacement = str_replace('%m', $message, $token);
								$format      = str_replace($token, $replacement, $format);
								break;
						}
					}
				}
			}

			return str_replace('%%', '%', $format);
		}


		/**
		 * Checks to see if a message exists in a given domain
		 *
		 * @access public
		 * @param string $domain The domain of the message
		 * @return boolean TRUE if a message of the name and domain exists, FALSE otherwise
		 */
		public function exists($domain = NULL)
		{
			return isset($_SESSION[self::getKey($this->name, $domain)])
				? TRUE
				: FALSE;
		}


		/**
		 * Redirects a message from one domain to another
		 *
		 * @access public
		 * @param string $domain The original domain of the message
		 * @param string $target_domain The domain to redirect the message to
		 * @return void
		 */
		public function redirect($target_domain = NULL)
		{
			if ($this->retrieve($this->domain)) {
				return new self($this->name, $target_domain, $this->message);
			}
		}


		/**
		 * Retrieves a message from a particular domain
		 *
		 * @access public
		 * @param string $domain The domain to retrieve the message from
		 * @return Message The message object populated with the message
		 */
		public function retrieve($domain = NULL)
		{
			$this->domain = $domain;

			if ($this->exists($this->domain)) {
				$message_key   = self::getKey($this->name, $this->domain);
				$this->message = $_SESSION[$message_key];

				unset($_SESSION[$message_key]);
			} else {
				$this->message = NULL;
			}

			return $this;
		}
	}
}
