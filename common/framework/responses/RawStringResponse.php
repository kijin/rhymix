<?php

namespace Rhymix\Framework\Responses;

use Rhymix\Framework\Response;

/**
 * The raw string response class.
 *
 * This class will print the raw string supplied to it.
 */
class RawStringResponse extends Response
{
	/**
	 * Internal state.
	 */
	protected $_content = '';

	/**
	 * Get the content.
	 *
	 * @return string
	 */
	public function getContent(): string
	{
		return $this->_content;
	}

	/**
	 * Set the content.
	 *
	 * @param string $content
	 * @return void
	 */
	public function setContent(string $content): void
	{
		$this->_content = $content;
	}

	/**
	 * Render the full response.
	 *
	 * @return string
	 */
	public function render(): string
	{
		return $this->_content;
	}
}
