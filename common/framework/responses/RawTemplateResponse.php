<?php

namespace Rhymix\Framework\Responses;

use Rhymix\Framework\Response;
use Rhymix\Framework\Template;

/**
 * The raw template response class.
 *
 * This class will print the raw output of a template file,
 * without assuming that it contains any HTML.
 *
 * This can be used to print a CSV file or RSS feed, for example.
 */
class RawTemplateResponse extends Response
{
	/**
	 * Internal state.
	 */
	protected $_template_dirname;
	protected $_template_filename;

	/**
	 * Get the current template path.
	 *
	 * @return ?string
	 */
	public function getTemplatePath(): ?string
	{
		return $this->_template_dirname;
	}

	/**
	 * Get the current template filename.
	 *
	 * @return ?string
	 */
	public function getTemplateFile(): ?string
	{
		return $this->_template_filename;
	}

	/**
	 * Set the template path and filename.
	 *
	 * @param string $dirname
	 * @param string $filename
	 * @return void
	 */
	public function setTemplate(string $dirname, string $filename): void
	{
		$this->_template_dirname = $dirname;
		$this->_template_filename = $filename;
	}

	/**
	 * Render the full response.
	 *
	 * @return string
	 */
	public function render(): string
	{
		if ($this->_template_dirname && $this->_template_filename)
		{
			$tpl = new Template($this->_template_dirname, $this->_template_filename);
			if ($this->_vars)
			{
				$tpl->setVars($this->_vars);
			}
			return $tpl->compile();
		}
		else
		{
			$output = '';
		}
	}
}
