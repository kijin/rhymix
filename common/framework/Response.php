<?php

namespace Rhymix\Framework;

/**
 * The base class for responses.
 *
 * This is an abstract class that cannot be instantiated directly.
 * Please use one of the subclasses defined in Rhymix\Framework\Responses.
 */
abstract class Response
{
	/**
	 * Internal variables.
	 */
	protected $_status_code = 200;
	protected $_content_type = '';
	protected $_vars = [];

	/**
	 * The constructor accepts a status code and an optional collection of vars.
	 *
	 * @param int $status_code
	 * @param array|object $vars
	 */
	public function __construct(int $status_code = 200, $vars = null)
	{
		$this->_status_code = $status_code;
		$this->_vars = is_array($vars) ? $vars : (is_object($vars) ? get_object_vars($vars) : []);
	}

	/**
	 * Get the content type.
	 *
	 * @return string
	 */
	public function getContentType(): string
	{
		return $this->_content_type;
	}

	/**
	 * Set the content type.
	 *
	 * @param string $content_type
	 * @return void
	 */
	public function setContentType(string $content_type): void
	{
		$this->_content_type = $content_type;
	}

	/**
	 * Get the status code.
	 *
	 * @return int
	 */
	public function getStatusCode(): int
	{
		return $this->_status_code;
	}

	/**
	 * Set the status code.
	 *
	 * @param int $status_code
	 * @return void
	 */
	public function setStatusCode(int $status_code): void
	{
		$this->_status_code = $status_code;
	}

	/**
	 * Get all variables.
	 *
	 * @return array
	 */
	public function getVars(): array
	{
		return $this->_vars;
	}

	/**
	 * Set the status code.
	 *
	 * @param array $vars
	 * @return void
	 */
	public function setVars(array $vars): void
	{
		$this->_vars = $vars;
	}

	/**
	 * Get a variable.
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function __get(string $name)
	{
		return isset($this->_vars[$name]) ? $this->_vars[$name] : null;
	}

	/**
	 * Add a variable.
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public function __set(string $name, $value): void
	{
		$this->_vars[$name] = $value;
	}

	/**
	 * Check if a variable is set.
	 *
	 * @param string $name
	 * @return bool
	 */
	public function __isset(string $name): bool
	{
		return isset($this->_vars[$name]);
	}

	/**
	 * Unset a variable.
	 *
	 * @param string $name
	 * @return void
	 */
	public function __unset(string $name): void
	{
		unset($this->_vars[$name]);
	}

	/**
	 * Treating this object as a string will call the render() method.
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->render();
	}

	/**
	 * Render the full response.
	 *
	 * This method must be implemented by each subclass.
	 *
	 * @return string
	 */
	abstract public function render(): string;

	/**
	 * Finalize the response.
	 *
	 * This method may be overridden by each subclass.
	 *
	 * @param string $content
	 * @return string
	 */
	public function finalize(string $content): string
	{
		return $content;
	}
}
