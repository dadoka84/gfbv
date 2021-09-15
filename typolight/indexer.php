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
 * @package    Backend
 * @license    LGPL
 * @filesource
 */


/**
 * Initialize the system
 */
define('TL_MODE', 'BE');
require_once('../system/initialize.php');


/**
 * Class Indexer
 *
 * Back end page indexer.
 * @copyright  Leo Feyer 2005
 * @author     Leo Feyer <leo@typolight.org>
 * @package    Controller
 */
class Indexer extends Backend
{

	/**
	 * Initialize the controller
	 * 
	 * 1. Import user
	 * 2. Call parent constructor
	 * 3. Authenticate user
	 * DO NOT CHANGE THIS ORDER!
	 */
	public function __construct()
	{
		$this->import('BackendUser', 'User');
		parent::__construct();

		$this->User->authenticate();
	}


	/**
	 * Run controller and parse the template
	 */
	public function run()
	{
		$arrPages = array();
		$strCache = md5('search_index' . session_id());

		// Get cache file
		if (file_exists(TL_ROOT . '/system/tmp/' . $strCache))
		{
			$objFile = new File('system/tmp/' . $strCache);

			if ($objFile->mtime < time() + 3600)
			{
				$arrPages = deserialize($objFile->getContent());
			}
		}

		// Get all searchable pages
		if (count($arrPages) < 1)
		{
			$arrPages = $this->getSearchablePages();

			// HOOK: take additional pages
			if (array_key_exists('getSearchablePages', $GLOBALS['TL_HOOKS']) && is_array($GLOBALS['TL_HOOKS']['getSearchablePages']))
			{
				foreach ($GLOBALS['TL_HOOKS']['getSearchablePages'] as $callback)
				{
					$this->import($callback[0]);
					$arrPages = $this->$callback[0]->$callback[1]($arrPages);
				}
			}

			$objFile = new File('system/tmp/' . $strCache);
			$objFile->write(serialize($arrPages));
			$objFile->close();
		}

		$intStart = $this->Input->get('start') ? $this->Input->get('start') : 0;
		$intPages = $this->Input->get('ppc') ? $this->Input->get('ppc') : 10;

		// Rebuild search index
		if ($intPages && count($arrPages))
		{
			$this->import('Search');

			if ($intStart < 1)
			{
				$this->Database->execute("TRUNCATE TABLE tl_search");
				$this->Database->execute("TRUNCATE TABLE tl_search_index");
				$this->Database->execute("TRUNCATE TABLE tl_cache");
			}

			echo '<div style="font-family:Verdana, sans-serif; font-size:11px; line-height:16px; margin-bottom:12px;">';

			for ($i=$intStart; $i<$intStart+$intPages && $i<count($arrPages); $i++)
			{
				echo 'File <strong>' . $arrPages[$i] . '</strong> has been indexed<br />';

				$objRequest = new Request();
				$objRequest->send($this->Environment->base . $arrPages[$i]);
			}

			echo '<div style="margin-top:12px;">';

			// Redirect to the next cycle
			if ($i < (count($arrPages) - 1))
			{
				$url = $this->Environment->base . 'typolight/indexer.php?start=' . ($intStart + $intPages) . '&ppc=' . $intPages;

				echo '<script type="text/javascript">setTimeout(\'window.location="' . $url . '"\', 1000);</script>';
				echo '<a href="' . $url . '">Please click here to proceed if you are not using JavaScript</a>';
			}

			// Redirect back home
			else
			{
				$url = $this->Environment->base . 'typolight/main.php?do=maintenance';

				// Delete temporary file
				$objFile = new File('system/tmp/' . $strCache);
				$objFile->delete();
				$objFile->close();

				echo '<script type="text/javascript">setTimeout(\'window.location="' . $url . '"\', 1000);</script>';
				echo '<a href="' . $url . '">Please click here to proceed if you are not using JavaScript</a>';
			}

			echo '</div></div>';
		}
	}
}


/**
 * Instantiate controller
 */
$objHelp = new Indexer();
$objHelp->run();

?>
