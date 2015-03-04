<?php namespace Dotink\Flourish
{
	/**
	 * Provides session-based messaging for page-to-page communication
	 *
	 * @copyright Copyright (c) 2007-2015 Will Bond, Matthew J. Sahagian, others
	 * @author Will Bond [wb] <will@flourishlib.com>
	 * @author Matthew J. Sahagian [mjs] <msahagian@dotink.org>
	 *
	 * @license Please reference the LICENSE.md file at the root of this distribution
	 *
	 * @package Flourish
	 */
	class Message
	{
		const DEFAULT_DOMAIN = 'messages';
		const KEY_SEPARATOR  = '::';

		/**
		 * The domain of the message
		 *
		 * @access protected
		 * @var string
		 */
		protected $domain = NULL;


		/**
		 * A callable formatter for formatting validation messages
		 *
		 * @access protected
		 * @var callable
		 */
		protected $formatter = NULL;


		/**
		 * The text of the message
		 *
		 * @access protected
		 * @var string
		 */
		protected $message = NULL;


		/**
		 * The name of the message
		 *
		 * @access protected
		 * @var string
		 */
		protected $name = NULL;


		/**
		 * Create a new Message
		 *
		 * @static
		 * @access public
		 * @param string $name The name of the message
		 * @param string $message The meessage text, default NULL
		 * @param string $domain The domain of the message, default NULL
		 * @return Message The new message
		 */
		static public function create($name, $message = NULL, $domain = NULL)
		{
			return new self($name, $domain, $message);
		}


		/**
		 * Get a normalized key for the message
		 *
		 * @access protected
		 * @param string $name The name of the message
		 * @param string $domain The domain of the message, default NULL
		 * @return string The normalized key
		 */
		static protected function getKey($name, $domain = NULL)
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
		public function __construct($name = NULL, $domain = NULL, $message = NULL)
		{
			if ($name == NULL) {
				return;
			}

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

			$this->formatter = function($name, $message) {
				echo sprintf('%s: %s', $name, $message);
			};
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
		 * Composes a message for output with an optionally provided formatter
		 *
		 * @access public
		 * @param string $domain The domain to show the message from
		 * @param callable $formatter A callable formatter to output the message
		 * @return string The name and/or message placed in the format string
		 *
		 */
		public function compose(callable $formatter = NULL)
		{
			$formatter = $formatter ?: $this->formatter;

			if ($this->message) {
				ob_start();
				$formatter($this->name, $this->message, $this->domain);

				return ob_get_clean();
			}

			return NULL;
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
			return !isset($_SESSION[self::getKey($this->name, $domain)])
				? FALSE
				: TRUE;
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
