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
 * Class Image
 *
 * This is the image manipulation class that allows to resize images. Resizing
 * requires the GD library. If an image cannot be resized proportionally, it
 * will be cropped to fit the given width and height. If you pass only one
 * argument (either width or height) image proportions will be preserved.
 *
 * Supported GET parameters:
 * - src:    image source (path to the image)
 * - width:  width of the image
 * - height: height of the image
 *
 * Usage example:
 * <img src="image.php?src=files/my_image.jpg&amp;width=300" alt="" />
 * @copyright  Leo Feyer 2005
 * @author     Leo Feyer <leo@typolight.org>
 * @package    Controller
 */
class Image extends Controller
{

	/**
	 * Image
	 * @var string
	 */
	protected $strImage;


	/**
	 * Set the current image
	 */
	public function __construct()
	{
		parent::__construct();
		$this->strImage = preg_replace('@^/+@', '', str_replace('%20', ' ' , $this->Input->get('src')));
	}


	/**
	 * Generate the new image
	 */
	public function run()
	{
		// Make sure there are no attempts to hack the file system
		if (preg_match('@^\.+@i', $this->strImage) || preg_match('@\.+/@i', $this->strImage) || preg_match('@(://)+@i', $this->strImage))
		{
			header('HTTP/1.0 404 Not Found');
			die('Invalid file name');
		}

		// Limit image processing to the tl_files directory
		if (!preg_match('@^' . preg_quote($GLOBALS['TL_CONFIG']['uploadPath'], '@') . '@i', $this->strImage))
		{
			header('HTTP/1.0 404 Not Found');
			die('Invalid path');
		}

		$strImage = $this->getImage($this->strImage, $this->Input->get('width'), $this->Input->get('height'));
		$arrImage = getimagesize($strImage);

		header('Content-type: ' . $arrImage['mime']);
		readfile(TL_ROOT . '/' . $strImage);
	}
}


/**
 * Instantiate controller
 */
$objImage = new Image();
$objImage->run();

?>
