<?php

namespace Rhymix\Framework\Responses;

use Rhymix\Framework\Response;

/**
 * The legacy XML response class.
 *
 * This format was used by XE 1.x for XMLRPC.
 */
class LegacyXMLResponse extends Response
{
	/**
	 * Override the default content type.
	 */
	protected $_content_type = 'text/xml';

	/**
	 * Render the full response.
	 *
	 * @return string
	 */
	public function render(): string
	{
		$lines = ['<?xml version="1.0" encoding="UTF-8"?>'];
		$lines[] = '<response>';
		$lines[] = sprintf('<error>%s</error>', escape($this->_variables['error'] ?? '0'));
		$lines[] = sprintf('<message>%s</message>', escape($this->_variables['message'] ?? 'success'));
		$lines[] = self::_makeXML($this->_variables);
		$lines[] = "</response>\n";
		return implode("\n", $lines);
	}

	/**
	 * Encode an array as XE 1.x-compatible XML.
	 *
	 * @param array $vars
	 * @return string
	 */
	public static function _makeXML(array $vars): string
	{
		$lines = [];
		foreach ($vars as $key => $val)
		{
			if (is_numeric($key))
			{
				$key = 'item';
			}
			else
			{
				$key = escape($key);
			}

			if (is_string($val))
			{
				$lines[] = sprintf('<%s>%s</%s>', $key, escape($val), $key);
			}
			elseif (!is_array($val) && !is_object($val))
			{
				$lines[] = sprintf('<%s>%s</%s>', $key, escape($val), $key);
			}
			else
			{
				$val = is_array($val) ? $val : get_object_vars($val);
				$lines[] = sprintf("<%s>\n%s\n</%s>", $key, self::_makeXML($val), $key);
			}
		}
		return implode("\n", $lines);
	}
}
