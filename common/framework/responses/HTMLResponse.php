<?php

namespace Rhymix\Framework\Responses;

use Rhymix\Framework\Response;

/**
 * The HTML response class.
 *
 * This is the default response for most web pages.
 */
class HTMLResponse extends Response
{
	/**
	 * Override the default content type.
	 */
	protected $_content_type = 'text/html';

	/**
	 * Internal state.
	 */
	protected $_layout_dirname = '';
	protected $_layout_filename = '';
	protected $_template_dirname = '';
	protected $_template_filename = '';

	/**
	 * Get the current layout path.
	 *
	 * @return string
	 */
	public function getLayoutPath(): string
	{
		return $this->_layout_dirname;
	}

	/**
	 * Get the current layout filename.
	 *
	 * @return string
	 */
	public function getLayoutFile(): string
	{
		return $this->_layout_filename;
	}

	/**
	 * Set the layout path and filename.
	 *
	 * @param string $dirname
	 * @param string $filename
	 * @return void
	 */
	public function setLayout(string $dirname, string $filename): void
	{
		$this->_layout_dirname = $dirname;
		$this->_layout_filename = $filename;
	}

	/**
	 * Get the current template path.
	 *
	 * @return string
	 */
	public function getTemplatePath(): string
	{
		return $this->_template_dirname;
	}

	/**
	 * Get the current template filename.
	 *
	 * @return string
	 */
	public function getTemplateFile(): string
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
		return '';
	}

	/**
	 * Finalize the response.
	 *
	 * @param string $content
	 * @return string
	 */
	public function finalize(string $content): string
	{
		return $content;
	}
}
