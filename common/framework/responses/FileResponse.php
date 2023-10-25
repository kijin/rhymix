<?php

namespace Rhymix\Framework\Responses;

use Rhymix\Framework\Response;

/**
 * The file response class.
 *
 * This class can be used to send a file to the user.
 */
class FileResponse extends Response
{
	/**
	 * Internal state.
	 */
	protected $_source_path = '';
	protected $_filename = '';
	protected $_force_download = true;

	/**
	 * Get the path to the source file.
	 *
	 * @return string
	 */
	public function getSourcePath(): string
	{
		return $this->_source_path;
	}

	/**
	 * Set the path to the source file.
	 *
	 * @param string $source_path
	 * @return void
	 */
	public function setSourcePath(string $source_path): void
	{
		$this->_source_path = $source_path;
	}

	/**
	 * Get the filename to be displayed to the client.
	 *
	 * @return string
	 */
	public function getFilename(): string
	{
		return $this->_filename;
	}

	/**
	 * Set the filename to be displayed to the client.
	 *
	 * @param string $filename
	 * @return void
	 */
	public function setFilename(string $filename): void
	{
		$this->_filename = $filename;
	}

	/**
	 * Force download.
	 *
	 * @param bool $force_download
	 * @return void
	 */
	public function forceDownload(bool $force_download): void
	{
		$this->_force_download = $force_download;
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
}
