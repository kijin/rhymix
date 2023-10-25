<?php

namespace Rhymix\Framework\Responses;

use Rhymix\Framework\Response;

/**
 * The legacy JSON response class.
 *
 * This format was used by XE 1.x for JSON-based AJAX responses.
 * It is similar to the regular JSON response, except:
 *
 * 1) It will convert associative arrays with all-numeric keys into lists.
 *    For example, [1 => 'foo', 3 => 'bar'] will be printed as ["foo","bar"].
 *
 * 2) It will produce arrays nested under an 'items' key for compatibility
 *    with legacy XML responses, but only if the request method was XMLRPC.
 */
class LegacyJSONResponse extends Response
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
		self::_convertCompat($this->_vars, \Context::getRequestMethod());
		return json_encode($this->_vars);
	}

	/**
	 * Convert arrays in a format that is compatible with XE 1.x.
	 *
	 * @param array &$array
	 * @param string $compat_type
	 * @return void
	 */
	protected static function _convertCompat(&$array, $compat_type = 'JSON')
	{
		foreach ($array as $key => &$value)
		{
			if (is_object($value))
			{
				$value = get_object_vars($value);
			}
			if (is_array($value))
			{
				self::_convertCompat($value, $compat_type);
				if (self::_isNumericArray($value))
				{
					if ($compat_type === 'XMLRPC')
					{
						$value = array('item' => array_values($value));
					}
					else
					{
						$value = array_values($value);
					}
				}
			}
		}
	}

	/**
	 * Check if an array only has numeric keys.
	 *
	 * @param array $array
	 * @return bool
	 */
	protected static function _isNumericArray(array $array): bool
	{
		if (!is_array($array) || !count($array))
		{
			return false;
		}
		foreach ($array as $key => $value)
		{
			if (!is_numeric($key))
			{
				return false;
			}
		}
		return true;
	}
}
