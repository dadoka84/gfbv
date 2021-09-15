<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 *
 * The TYPOlight webCMS is an accessible web content management system that 
 * specializes in accessibility and generates W3C-compliant HTML code. It 
 * provides a wide range of functionality to develop professional websites 
 * including a built-in search engine, form generator, file and user manager, 
 * CSS engine, multi-language support and many more. For more information and 
 * additional TYPOlight applications like the TYPOlight MVC Framework please 
 * visit the project website http://www.typolight.org.
 *
 * PHP version 5
 * @copyright  META-LEVEL Software AG 2008 
 * @author     Andreas Gross
 * @package    AtoZ
 * @license    LGPL 
 */

/**
 * Frontent Module for displaying an alphabetic ordered list of all pages including
 * short description
 * 
 * @copyright  META-LEVEL Software AG 2008 
 * @author     Andreas Gross
 * @package    AtoZ
 * @version    1.0
 */
class ModuleAtoZ extends Module {
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = "mod_AtoZ";
	
		
	/**
	 * Compiles list of all pages including description and returns it
	 * as an array.
	 *
	 */
	protected function getPages($rootId) {
		$objPages = $this->Database->prepare(
			"SELECT * FROM tl_page WHERE ".
			"pid=? AND type!=? AND type!=? AND type!=? AND (start=? OR start<?) AND (stop=? OR stop>?)". 
			' AND hide!=?'. 
			((FE_USER_LOGGED_IN && !BE_USER_LOGGED_IN) ? ' AND guests!=?' : '').
			(!BE_USER_LOGGED_IN ? ' AND published=?' : '') . " ORDER BY title")
			->execute($rootId, 'root', 'error_403', 'error_404', '', time(), '', time(), 1, 1, 1);
									  
		
		$count = 0;
		$limit = $objPages->numRows;
		$items = array();
		$groups = array();
		
		if ($objPages->numRows < 1)
		{
			return $items;
		}
		
		
		// Get all groups of the current front end user
		if (FE_USER_LOGGED_IN)
		{
			$this->import('FrontendUser', 'User');
			$groups = $this->User->groups;
		}		
		
		while($objPages->next()) {
			// Do not show protected pages unless a back end or front end user is logged in
			if (!strlen($objPages->protected) || (!is_array($_groups) && FE_USER_LOGGED_IN) || BE_USER_LOGGED_IN || (is_array($_groups) && count(array_intersect($groups, $_groups))) || $this->showProtected)
			{
				// Check whether there will be subpages
				if (count($this->getChildRecords($objPages->id, 'tl_page')))
				{				
					$subitems = $this->getPages($objPages->id);	
				} else {
					$subitems = array();
				}
				
				// Get href
				switch ($objPages->type)
				{
					case 'redirect':
						$href = $objPages->url;
						break;

					case 'forward':
						$objNext = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=?")
												  ->limit(1)
												  ->execute($objPages->jumpTo);

						if ($objNext->numRows)
						{
							$href = $this->generateFrontendUrl($objNext->fetchAssoc());
							break;
						}
						// DO NOT ADD A break; STATEMENT

					default:
						$href = $this->generateFrontendUrl($objPages->row());
						break;
				}
				
				$items[] = array
				(
					'isActive' => false,
					'title' => specialchars($objPages->title),
					'pageTitle' => ($objPages->pageTitle && strlen($objPages->pageTitle) > 0 ? specialchars($objPages->pageTitle) : specialchars($objPages->title)),
					'link' => $objPages->title,
					'href' => $href,
					'target' => (($objPages->type == 'redirect' && $objPages->target) ? ' window.open(this.href); return false;' : ''),
					'description' => str_replace(array("\n", "\r"), array(' ' , ''), $objPages->description),
				);
				
				$items = array_merge($items, $subitems);
			}
		}
		return $items;
	}
	
	
	
	/**
	 * Generate module
	 * @return string
	 **/
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new Template('be_wildcard');
			$objTemplate->wildcard = '### A-Z ###';

			return $objTemplate->parse();
		}

		return parent::generate();
	}	
	
	
	/**
	 * Generate module
	 *
	 */
	protected function compile()
	{
		// 
		$pages = $this->getPages($this->rootPage);
		
		// Sort it...
		uasort($pages, create_function('$a, $b', 'return $a["pageTitle"] > $b["pageTitle"];'));
		$this->Template->pages = $pages;
		$this->Template->headline = $this->headline;
		$this->Template->hl = $this->hl;
		$this->Template->subheader = $this->atoz_subheader;
	}
}
