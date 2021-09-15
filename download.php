<?php

/**
 * TYPOlight webCMS
 * Copyright (C) 2005 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at http://www.gnu.org/licenses/.
 *
 * PHP version 5
 * @copyright  Leo Feyer 2005
 * @author     Leo Feyer <leo@typolight.org>
 * @package    Frontend
 * @license    LGPL
 * @filesource
 */


/**
 * Initialize the system
 */
define('TL_MODE', 'FE');
require('system/initialize.php');


/**
 * Class Download
 *
 * This class allows to download a particular file from the application or
 * temp directory automatically opening the "save as..." dialogue. In order
 * to prevent unauthorized downloads, a download token is required that
 * will be validated against a token stored in the current session. File
 * downloads are restricted to those file types defined in the application
 * configuration file (downloadTypes).
 *
 * Supported GET parameters:
 * - src:   file source (path to the file)
 * - token: validation string
 *
 * Usage example:
 * <a href="download.php?src=files/my_image.jpg?token=nd38HD3n3fuu378n">
 * @copyright  Leo Feyer 2005
 * @author     Leo Feyer <leo@typolight.org>
 * @package    Controller
 */
class Download extends Controller
{

	/**
	 * File
	 * @var string
	 */
	protected $strFile;


	/**
	 * Set the current file
	 */
	public function __construct()
	{
		parent::__construct();
		$this->strFile = preg_replace('@^/+@', '', str_replace('%20', ' ' , $this->Input->get('src')));
	}


	/**
	 * Generate the new image
	 */
	public function run()
	{
		// Make sure there are no attempts to hack the file system
		if (preg_match('@^\.+@i', $this->strFile) || preg_match('@\.+/@i', $this->strFile) || preg_match('@(://)+@i', $this->strFile))
		{
			header('HTTP/1.0 404 Not Found');
			die('Invalid file name');
		}

		// Limit downloads to the tl_files directory
		if (!preg_match('@^' . preg_quote($GLOBALS['TL_CONFIG']['uploadPath'], '@') . '@i', $this->strFile))
		{
			header('HTTP/1.0 404 Not Found');
			die('Invalid path');
		}

		// Check whether the file exists
		if (!file_exists(TL_ROOT . '/' . $this->strFile))
		{
			header('HTTP/1.0 404 Not Found');
			die('File not found');
		}

		// Die if there is no token or if the tokens do not match
		if (!strlen($this->Input->get('token')) || !is_array($_SESSION['downloadToken']) || !in_array($this->Input->get('token'), $_SESSION['downloadToken']))
		{
			header('HTTP/1.0 403 Forbidden');
			die('Invalid download token');
		}

		// Die if the file is not allowed
		if (!is_array($_SESSION['downloadFiles']) || !in_array($this->strFile, $_SESSION['downloadFiles']) || !preg_match('/^' . preg_quote($GLOBALS['TL_CONFIG']['uploadPath'], '/') . '\//i', $this->strFile))
		{
			header('HTTP/1.0 403 Forbidden');
			die(sprintf('Download of file "%s" is not allowed', $this->strFile));
		}

		$objFile = new File($this->strFile);
		$arrAllowedTypes = trimsplit(',', strtolower($GLOBALS['TL_CONFIG']['allowedDownload']));

		if (!in_array($objFile->extension, $arrAllowedTypes))
		{
			header('HTTP/1.0 403 Forbidden');
			die(sprintf('File type "%s" is not allowed', $objFile->extension));
		}

		// Open the "save as..." dialogue
		header('Content-Type: ' . $objFile->mime);
		header('Content-Transfer-Encoding: binary');
		header('Content-Disposition: attachment; filename="'.$objFile->basename.'"');
		header('Content-Length: '.$objFile->filesize);
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0'); 
		header('Pragma: public');
		header('Expires: 0');

		$resFile = fopen(TL_ROOT . '/' . $this->strFile, 'rb');
		fpassthru($resFile);
		fclose($resFile);

		// HOOK: post download callback
		if (array_key_exists('postDownload', $GLOBALS['TL_HOOKS']) && is_array($GLOBALS['TL_HOOKS']['postDownload']))
		{
			foreach ($GLOBALS['TL_HOOKS']['postDownload'] as $callback)
			{
				$this->import($callback[0]);
				$this->$callback[0]->$callback[1]($this->strFile);
			}
		}
	}
}


/**
 * Instantiate controller
 */
$objDownload = new Download();
$objDownload->run();

?>
