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
 * Try to disable PHPSESSID
 */
@ini_set('session.use_trans_sid', 0);


/**
 * Initialize the system
 */
define('TL_MODE', 'FE');
require('system/initialize.php');


/**
 * Class Index
 *
 * Main front end controller.
 * @copyright  Leo Feyer 2005
 * @author     Leo Feyer <leo@typolight.org>
 * @package    Controller
 */
class Index extends Frontend
{

	/**
	 * Initialize the object (import user object first!)
	 */
	public function __construct()
	{
		$this->import('FrontendUser', 'User');
		parent::__construct();

		define('BE_USER_LOGGED_IN', $this->getLoginStatus('BE_USER_AUTH'));
		define('FE_USER_LOGGED_IN', $this->getLoginStatus('FE_USER_AUTH'));

		// HOOK: trigger recall extension
		if (!FE_USER_LOGGED_IN && $this->Input->cookie('tl_recall_fe') && in_array('recall', $this->Config->getActiveModules()))
		{
			Recall::frontend($this);
		}
	}


	/**
	 * Run the controller
	 */
	public function run()
	{
		global $objPage;

		// Check whether there is a cached version
		if (!BE_USER_LOGGED_IN && !FE_USER_LOGGED_IN && strlen($this->Environment->request))
		{
			$this->outputFromCache();
		}

		$pageId = $this->getPageIdFromUrl();

		// Load a website root page object if there is no page ID
		if (is_null($pageId))
		{
			$objHandler = new $GLOBALS['TL_PTY']['root']();
			$pageId = $objHandler->generate($this->getRootIdFromUrl(), true);
		}

		// Get the current page object
		$objPage = $this->Database->prepare("SELECT * FROM tl_page WHERE (id=? OR alias=?) AND (start=? OR start<?) AND (stop=? OR stop>?)" . (!BE_USER_LOGGED_IN ? ' AND published=?' : ''))
								  ->execute($pageId, $pageId, '', time(), '', time(), 1);

		// Load an error 404 page object if the page cannot be found
		if ($objPage->numRows < 1)
		{
			$objHandler = new $GLOBALS['TL_PTY']['error_404']();
			$objHandler->generate($pageId);
		}

		// Check the URL of each page if there are multiple results
		if ($objPage->numRows > 1)
		{
			$rootId = $this->getRootIdFromUrl();

			while ($objPage->next())
			{
				$objCurrentPage = $this->getPageDetails($objPage->id);

				// Stop searching if the root IDs match
				if ($objCurrentPage->rootId == $rootId)
				{
					break;
				}
			}

			$objPage = $objCurrentPage;
		}

		// Load a website root page object if the page is a website root page
		if ($objPage->type == 'root')
		{
			$objHandler = new $GLOBALS['TL_PTY']['root']();
			$objHandler->generate($objPage->id);
		}

		// Inherit settings from parent pages if it has not been done yet
		if (!is_bool($objPage->protected))
		{
			$objPage = $this->getPageDetails($objPage->id);
		}

		// Authenticate the current user
		if (!$this->User->authenticate() && $objPage->protected && !BE_USER_LOGGED_IN)
		{
			$objHandler = new $GLOBALS['TL_PTY']['error_403']();
			$objHandler->generate($pageId);
		}

		// Check user groups if the page is protected
		if ($objPage->protected && !BE_USER_LOGGED_IN && is_array($objPage->groups) && count(array_intersect($this->User->groups, $objPage->groups)) < 1)
		{
			$this->log('Page "' . $pageId . '" can only be accessed by groups "' . implode(', ', (array) $objPage->groups) . '" (current user groups: ' . implode(', ', $this->User->groups) . ')', 'Index run()', TL_ERROR);

			$objHandler = new $GLOBALS['TL_PTY']['error_403']();
			$objHandler->generate($pageId);
		}

		// Check whether there are domain name restrictions
		if (strlen($objPage->domain))
		{
			$strDomain = preg_replace('/^www\./i', '', $objPage->domain);
			$strHost = preg_replace('/^www\./i', '', $this->Environment->host);

			if ($strDomain != $strHost)
			{
				// Load an error 404 page object
				$objHandler = new $GLOBALS['TL_PTY']['error_404']();
				$objHandler->generate($objPage->id, $strDomain, $strHost);
			}
		}

		// Load the page object depending on its type
		$objHandler = new $GLOBALS['TL_PTY'][$objPage->type]();

		switch ($objPage->type)
		{
			case 'root':
			case 'error_403':
			case 'error_404':
				$objHandler->generate($pageId);
				break;

			default:
				$objHandler->generate($objPage);
				break;
		}
	}


	/**
	 * Load the page from cache
	 */
	private function outputFromCache()
	{
		$objCache = $this->Database->prepare("SELECT * FROM tl_cache WHERE url=?")
								   ->limit(1)
								   ->execute(preg_replace('@^(index.php/)?([^\?]+)(\?.*)?@i', '$2', $this->Environment->request));

		if ($objCache->numRows < 1 || $objCache->tstamp < time())
		{
			return;
		}

		header('Content-Type: text/html; charset='.$GLOBALS['TL_CONFIG']['characterSet']);
		header('Cache-Control: public, max-age='.$objCache->tstamp);
		header('Expires: '.gmdate('D, d M Y H:i:s', $objCache->tstamp).' GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', time()).' GMT');
		header('Pragma: public');

		echo $this->replaceInsertTags($objCache->data);
		exit;
	}
}


/**
 * Instantiate controller
 */
$objIndex = new Index();
$objIndex->run();

?>
