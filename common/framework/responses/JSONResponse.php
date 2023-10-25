<?php

namespace Rhymix\Framework\Responses;

use Rhymix\Framework\Response;

/**
 * The JSON response class.
 *
 * This is a new format that doesn't apply any additional conversions.
 * It will produce the raw output of the json_encode() function.
 *
 * For example, [1 => 'foo', 3 => 'bar'] will be printed as
 * {"1":"foo","3":"bar"}.
 */
class JSONResponse extends Response
{
	/**
	 * Override the default content type.
	 */
	protected $_content_type = 'application/json';

	/**
	 * Render the full response.
	 *
	 * @return string
	 */
	public function render(): string
	{
		return json_encode($this->_vars);
	}
}
