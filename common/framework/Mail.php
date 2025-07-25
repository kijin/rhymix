<?php

namespace Rhymix\Framework;

/**
 * The mail class.
 */
class Mail
{
	/**
	 * Instance properties.
	 */
	public $message = null;
	public $driver = null;
	public $caller = '';
	protected $content_type = 'text/html';
	protected $attachments = array();
	public $errors = array();
	protected $sent = false;

	/**
	 * Static properties.
	 */
	public static $default_driver = null;
	public static $custom_drivers = array();

	/**
	 * Set the default driver.
	 *
	 * @param Drivers\MailInterface $driver
	 * @return void
	 */
	public static function setDefaultDriver(Drivers\MailInterface $driver): void
	{
		self::$default_driver = $driver;
	}

	/**
	 * Get the default driver.
	 *
	 * @return Drivers\MailInterface
	 */
	public static function getDefaultDriver(): Drivers\MailInterface
	{
		if (!self::$default_driver)
		{
			$default_driver = config('mail.type');
			$default_driver_class = '\\Rhymix\\Framework\\Drivers\Mail\\' . $default_driver;
			if (class_exists($default_driver_class))
			{
				$default_driver_config = config('mail.' . $default_driver) ?: array();
				self::$default_driver = $default_driver_class::getInstance($default_driver_config);
			}
			else
			{
				self::$default_driver = Drivers\Mail\MailFunction::getInstance(array());
			}
		}
		return self::$default_driver;
	}

	/**
	 * Add a custom mail driver.
	 *
	 * @param Drivers\MailInterface $driver
	 * @return void
	 */
	public static function addDriver(Drivers\MailInterface $driver): void
	{
		self::$custom_drivers[] = $driver;
	}

	/**
	 * Get the list of supported mail drivers.
	 *
	 * @return array
	 */
	public static function getSupportedDrivers(): array
	{
		$result = array();
		foreach (Storage::readDirectory(__DIR__ . '/drivers/mail', false) as $filename)
		{
			$driver_name = substr($filename, 0, -4);
			$class_name = '\Rhymix\Framework\Drivers\Mail\\' . $driver_name;
			if ($class_name::isSupported())
			{
				$result[$driver_name] = array(
					'name' => $class_name::getName(),
					'required' => $class_name::getRequiredConfig(),
					'api_types' => $class_name::getAPITypes(),
					'spf_hint' => $class_name::getSPFHint(),
					'dkim_hint' => $class_name::getDKIMHint(),
				);
			}
		}
		foreach (self::$custom_drivers as $driver)
		{
			if ($driver->isSupported())
			{
				$result[strtolower(class_basename($driver))] = array(
					'name' => $driver->getName(),
					'required' => $driver->getRequiredConfig(),
					'api_types' => $driver->getAPITypes(),
					'spf_hint' => $class_name::getSPFHint(),
					'dkim_hint' => $class_name::getDKIMHint(),
				);
			}
		}
		ksort($result);
		return $result;
	}

	/**
	 * The constructor.
	 */
	public function __construct()
	{
		$this->message = new \Swift_Message;
		$this->driver = self::getDefaultDriver();
	}

	/**
	 * Set the sender (From:).
	 *
	 * @param string $email E-mail address
	 * @param string $name Name (optional)
	 * @return bool
	 */
	public function setFrom(string $email, ?string $name = null): bool
	{
		try
		{
			$this->message->setFrom($name === null ? $email : array($email => $name));
			return true;
		}
		catch (\Exception $e)
		{
			$this->errors[] = 'setFrom: ' . $e->getMessage();
			return false;
		}
	}

	/**
	 * Get the sender (From:).
	 *
	 * @return string|null
	 */
	public function getFrom(): ?string
	{
		$list = $this->message->getFrom();
		return $list ? array_first($this->formatAddresses($list)) : null;
	}

	/**
	 * Add a recipient (To:).
	 *
	 * @param string $email E-mail address
	 * @param string $name Name (optional)
	 * @return bool
	 */
	public function addTo(string $email, ?string $name = null): bool
	{
		try
		{
			$this->message->addTo($email, $name);
			return true;
		}
		catch (\Exception $e)
		{
			$this->errors[] = 'addTo: ' . $e->getMessage();
			return false;
		}
	}

	/**
	 * Add a recipient (CC:).
	 *
	 * @param string $email E-mail address
	 * @param string $name Name (optional)
	 * @return bool
	 */
	public function addCc(string $email, ?string $name = null): bool
	{
		try
		{
			$this->message->addCc($email, $name);
			return true;
		}
		catch (\Exception $e)
		{
			$this->errors[] = 'addCc: ' . $e->getMessage();
			return false;
		}
	}

	/**
	 * Add a recipient (BCC:).
	 *
	 * @param string $email E-mail address
	 * @param string $name Name (optional)
	 * @return bool
	 */
	public function addBcc(string $email, ?string $name = null): bool
	{
		try
		{
			$this->message->addBcc($email, $name);
			return true;
		}
		catch (\Exception $e)
		{
			$this->errors[] = 'addBcc: ' . $e->getMessage();
			return false;
		}
	}

	/**
	 * Get the list of recipients.
	 *
	 * @return array
	 */
	public function getRecipients(): array
	{
		$result = array();

		foreach ($this->formatAddresses($this->message->getTo() ?: []) as $address)
		{
			$result[] = $address;
		}
		foreach ($this->formatAddresses($this->message->getCc() ?: []) as $address)
		{
			$result[] = $address;
		}
		foreach ($this->formatAddresses($this->message->getBcc() ?: []) as $address)
		{
			$result[] = $address;
		}

		return array_unique($result);
	}

	/**
	 * Set the Reply-To: address.
	 *
	 * @param string $replyTo
	 * @return bool
	 */
	public function setReplyTo(string $replyTo): bool
	{
		try
		{
			$this->message->setReplyTo(array($replyTo));
			return true;
		}
		catch (\Exception $e)
		{
			$this->errors[] = 'setReplyTo: ' . $e->getMessage();
			return false;
		}
	}

	/**
	 * Set the Return-Path: address.
	 *
	 * @param string $returnPath
	 * @return bool
	 */
	public function setReturnPath(string $returnPath): bool
	{
		try
		{
			$this->message->setReturnPath($returnPath);
			return true;
		}
		catch (\Exception $e)
		{
			$this->errors[] = 'setReturnPath: ' . $e->getMessage();
			return false;
		}
	}

	/**
	 * Set the Message ID.
	 *
	 * @param string $message_id
	 * @return bool
	 */
	public function setMessageID(string $message_id): bool
	{
		try
		{
			$headers = $this->message->getHeaders();
			$headers->get('Message-ID')->setId($message_id);
			return true;
		}
		catch (\Exception $e)
		{
			$this->errors[] = 'setMessageID: ' . $e->getMessage();
			return false;
		}
	}

	/**
	 * Set the In-Reply-To: header.
	 *
	 * @param string $in_reply_to
	 * @return bool
	 */
	public function setInReplyTo(string $in_reply_to): bool
	{
		try
		{
			$headers = $this->message->getHeaders();
			$headers->addTextHeader('In-Reply-To', $in_reply_to);
			return true;
		}
		catch (\Exception $e)
		{
			$this->errors[] = 'setInReplyTo: ' . $e->getMessage();
			return false;
		}
	}

	/**
	 * Set the References: header.
	 *
	 * @param string $references
	 * @return bool
	 */
	public function setReferences(string $references): bool
	{
		try
		{
			$headers = $this->message->getHeaders();
			$headers->addTextHeader('References', $references);
			return true;
		}
		catch (\Exception $e)
		{
			$this->errors[] = 'setReferences: ' . $e->getMessage();
			return false;
		}
	}

	/**
	 * Set the subject.
	 *
	 * @param string $subject
	 * @return bool
	 */
	public function setSubject(string $subject): bool
	{
		try
		{
			$this->message->setSubject($subject);
			return true;
		}
		catch (\Exception $e)
		{
			$this->errors[] = 'setSubject: ' . $e->getMessage();
			return false;
		}
	}

	/**
	 * Get the subject.
	 *
	 * @return string
	 */
	public function getSubject(): string
	{
		return $this->message->getSubject();
	}

	/**
	 * Set the subject (alias to setSubject).
	 *
	 * @param string $subject
	 * @return bool
	 */
	public function setTitle(string $subject): bool
	{
		return $this->setSubject($subject);
	}

	/**
	 * Get the subject (alias to getSubject).
	 *
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->getSubject();
	}

	/**
	 * Set the body content.
	 *
	 * @param string $content
	 * @param string $content_type (optional)
	 * @return void
	 */
	public function setBody(string $content, ?string $content_type = null): void
	{
		if ($content_type !== null)
		{
			$this->setContentType($content_type);
		}

		if (strpos($this->content_type, 'html') !== false)
		{
			$content = Filters\HTMLFilter::fixRelativeUrls($content);
		}

		$this->message->setBody($content, $this->content_type);
	}

	/**
	 * Get the body content.
	 *
	 * @return string
	 */
	public function getBody(): string
	{
		return $this->message->getBody();
	}

	/**
	 * Set the body content (alias to setBody).
	 *
	 * @param string $content
	 * @param string $content_type (optional)
	 * @return void
	 */
	public function setContent(string $content, ?string $content_type = null): void
	{
		$this->setBody($content, $content_type);
	}

	/**
	 * Get the body content (alias to getBody).
	 *
	 * @return string
	 */
	public function getContent(): string
	{
		return $this->getBody();
	}

	/**
	 * Set the content type.
	 *
	 * @param string $mode The type
	 * @return void
	 */
	public function setContentType(string $type = 'text/html'): void
	{
		$this->content_type = (strpos($type, 'html') !== false) ? 'text/html' : ((strpos($type, '/') !== false) ? $type : 'text/plain');
	}

	/**
	 * Get the content type.
	 *
	 * @return string
	 */
	public function getContentType(): string
	{
		return $this->content_type;
	}

	/**
	 * Attach a file.
	 *
	 * @param string $local_filename
	 * @param string $display_filename (optional)
	 * @return bool
	 */
	public function attach(string $local_filename, ?string $display_filename = null): bool
	{
		if ($display_filename === null)
		{
			$display_filename = basename($local_filename);
		}
		if (!Storage::exists($local_filename))
		{
			return false;
		}

		$attachment = \Swift_Attachment::fromPath($local_filename);
		$attachment->setFilename($display_filename);
		$result = $this->message->attach($attachment);

		if ($result)
		{
			$this->attachments[] = (object)array(
				'type' => 'attach',
				'local_filename' => $local_filename,
				'display_filename' => $display_filename,
				'cid' => null,
			);
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Embed a file.
	 *
	 * @param string $local_filename
	 * @param string $cid (optional)
	 * @return string|false
	 */
	public function embed(string $local_filename, ?string $cid = null)
	{
		if (!Storage::exists($local_filename))
		{
			return false;
		}

		$embedded = \Swift_EmbeddedFile::fromPath($local_filename);
		if ($cid !== null)
		{
			$embedded->setId(preg_replace('/^cid:/i', '', $cid));
		}
		$result = $this->message->embed($embedded);

		if ($result)
		{
			$this->attachments[] = (object)array(
				'type' => 'embed',
				'local_filename' => $local_filename,
				'display_filename' => null,
				'cid' => $result,
			);
			return $result;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Get the list of attachments to this message.
	 *
	 * @return array
	 */
	public function getAttachments(): array
	{
		return $this->attachments;
	}

	/**
	 * Send the email.
	 *
	 * @param bool $sync
	 * @return bool
	 */
	public function send(bool $sync = false): bool
	{
		// If queue is enabled, send asynchronously.
		if (!$sync && config('queue.enabled') && !defined('RXQUEUE_CRON'))
		{
			Queue::addTask(self::class . '::' . 'sendAsync', $this);
			return true;
		}

		// Get caller information.
		$backtrace = debug_backtrace(0);
		if(count($backtrace) && isset($backtrace[0]['file']))
		{
			$this->caller = $backtrace[0]['file'] . ($backtrace[0]['line'] ? (' line ' . $backtrace[0]['line']) : '');
		}

		$output = \ModuleHandler::triggerCall('mail.send', 'before', $this);
		if(!$output->toBool())
		{
			$this->errors[] = $output->getMessage();
			return false;
		}

		// Reset Message-ID in case send() is called multiple times.
		$random = substr(hash('sha256', mt_rand() . microtime() . getmypid()), 0, 32);
		$sender = $this->message->getFrom();
		$sender_email = strval(array_key_first($sender));
		$id = $random . '@' . (preg_match('/^(.+)@([^@]+)$/', $sender_email, $matches) ? $matches[2] : 'swift.generated');
		$this->message->getHeaders()->get('Message-ID')->setId($id);

		try
		{
			$this->sent = $this->driver->send($this) ? true : false;
		}
		catch(\Exception $e)
		{
			$this->errors[] = $e->getMessage();
			$this->sent = false;
		}

		$output = \ModuleHandler::triggerCall('mail.send', 'after', $this);
		if(!$output->toBool())
		{
			$this->errors[] = $output->getMessage();
		}

		return $this->sent;
	}

	/**
	 * Send an email asynchronously (for Queue integration).
	 *
	 * @param self $mail
	 * @return void
	 */
	public static function sendAsync(self $mail): void
	{
		$mail->send();
	}

	/**
	 * Check if the message was sent.
	 *
	 * @return bool
	 */
	public function isSent(): bool
	{
		return $this->sent;
	}

	/**
	 * Get caller information.
	 *
	 * @return string
	 */
	public function getCaller(): string
	{
		return $this->caller;
	}

	/**
	 * Get errors.
	 *
	 * @return array
	 */
	public function getErrors(): array
	{
		return $this->errors;
	}

	/**
	 * Convert image paths to absolute URLs.
	 *
	 * @see Mail::setContent()
	 * @param array $matches Match info.
	 * @return string
	 */
	protected function convertImageURLs(array $matches): string
	{
		return Filters\HTMLFilter::fixRelativeUrls($matches[0]);
	}

	/**
	 * Format an array of addresses for display.
	 *
	 * @param array $addresses
	 * @return array
	 */
	protected function formatAddresses(array $addresses): array
	{
		$result = array();

		if (!$addresses)
		{
			return array();
		}

		foreach($addresses as $email => $name)
		{
			if(strval($name) === '')
			{
				$result[] = $email;
			}
			else
			{
				$result[] = $name . ' <' . $email . '>';
			}
		}

		return $result;
	}
}
