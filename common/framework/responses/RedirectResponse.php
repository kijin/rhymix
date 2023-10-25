<?php

namespace Rhymix\Framework\Responses;

use Rhymix\Framework\Response;
use Rhymix\Framework\Exception;

/**
 * The raw string response class.
 *
 * This class will print the raw string supplied to it.
 */
class RedirectResponse extends Response
{
	/**
	 * Internal state.
	 */
	protected $_url = '';

	/**
	 * Get the content.
	 *
	 * @return string
	 */
	public function getRedirectUrl(): string
	{
		return $this->_url;
	}

	/**
	 * Set the content.
	 *
	 * @param string $url
	 * @return void
	 */
	public function setRedirectUrl(string $url): void
	{
		$this->_url = $url;
	}

	/**
	 * Render the full response.
	 *
	 * @return string
	 */
	public function render(): string
	{
		if ($this->_url)
		{
			header('Location: ' . utf8_normalize_spaces($this->_url));
			return '';
		}
		else
		{
			throw new Exception('Redirect URL is empty');
		}
	}
}
