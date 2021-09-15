<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

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
 * @package    Language
 * @license    LGPL
 * @filesource
 */


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_page']['title']          = array('Page name', 'The name of a page is shown in the website navigation.');
$GLOBALS['TL_LANG']['tl_page']['alias']          = array('Page alias', 'The page alias is a unique reference to the page which can be called instead of the page ID. It is especially useful if TYPOlight uses static URLs.');
$GLOBALS['TL_LANG']['tl_page']['adminEmail']     = array('E-mail address of the website administrator', 'The e-mail address of the website administrator will be used as sender address for autogenerated messages like activation e-mails or subscription confirmation e-mails.');
$GLOBALS['TL_LANG']['tl_page']['type']           = array('Page type', 'Please select the type of page depending on its purpose.');
$GLOBALS['TL_LANG']['tl_page']['language']       = array('Language', 'Please enter the language you are using on the current website. Enter a language using the primary subtag (ISO-639 / RFC3066), e.g. "en" for English.');
$GLOBALS['TL_LANG']['tl_page']['pageTitle']      = array('Page title', 'The page title is shown in the TITLE tag of the page and in the search results. It should not contain more than 65 characters. If you leave this field blank, the page name will be used as title.');
$GLOBALS['TL_LANG']['tl_page']['description']    = array('Page description', 'You can enter a short description of the page which will be shown by search engines. A search engine usually indicates between 150 and 300 characters.');
$GLOBALS['TL_LANG']['tl_page']['cssClass']       = array('CSS Class', 'If you enter a class name here, it will be used as class attribute in the navigation menu. Thus you can format navigation items individually.');
$GLOBALS['TL_LANG']['tl_page']['protected']      = array('Protect page', 'If you choose this option you can restrict access to the page to certain member groups.');
$GLOBALS['TL_LANG']['tl_page']['groups']         = array('Allowed member groups', 'Here you can grant access to one or more user groups. If you do not choose a group, any logged in front end user will be able to access the page.');
$GLOBALS['TL_LANG']['tl_page']['includeLayout']  = array('Assign a layout', 'By default a page uses the same layout as its parent page. If you choose this option, you can assign a new layout to the current page and its subpages.');
$GLOBALS['TL_LANG']['tl_page']['layout']         = array('Page layout', 'Please choose a page layout. You can edit or create layouts using module <em>page layout</em>.');
$GLOBALS['TL_LANG']['tl_page']['includeCache']   = array('Assign cache timeout value', 'By default a page uses the same cache timeout value as its parent page. If you choose this option, you can assign a new cache timeout value to the current page and its subpages.');
$GLOBALS['TL_LANG']['tl_page']['cache']          = array('Cache timeout', 'Within the cache timeout period, a page will be loaded from the cache table. This will decrease the loading time of your webpages.');
$GLOBALS['TL_LANG']['tl_page']['includeChmod']   = array('Assign permissions', 'Permissions allow you to define to what extent a back end user can modify a page and its articles. If you do not choose this option, the page uses the same permissions as its parent page.');
$GLOBALS['TL_LANG']['tl_page']['chmod']          = array('Permissions', 'Each page has three access levels: one for the user, one for the group and one for everyone else. You can assign different permissions to each level.');
$GLOBALS['TL_LANG']['tl_page']['cuser']          = array('Owner', 'Please select a user as owner of the current page.');
$GLOBALS['TL_LANG']['tl_page']['cgroup']         = array('Group', 'Please select a group as owner of the current page.');
$GLOBALS['TL_LANG']['tl_page']['createSitemap']  = array('Create an XML sitemap', 'Create a Google XML sitemap in the root directory.');
$GLOBALS['TL_LANG']['tl_page']['sitemapName']    = array('Filename', 'Please enter a name for the XML file.');
$GLOBALS['TL_LANG']['tl_page']['sitemapBase']    = array('Base URL', 'Please enter the base URL including the protocol (e.g. <em>http://</em>).');
$GLOBALS['TL_LANG']['tl_page']['hide']           = array('Hide page from navigation', 'Hide the page from the navigation menu.');
$GLOBALS['TL_LANG']['tl_page']['guests']         = array('Show to guests only', 'Hide the page from the navigation menu if a member is logged in.');
$GLOBALS['TL_LANG']['tl_page']['noSearch']       = array('Do not search', 'Do not index this page.');
$GLOBALS['TL_LANG']['tl_page']['accesskey']      = array('Access key', 'An access key is a single character which can be assigned to a navigation element. If a visitor simultaneously presses the [ALT] key and the access key, the navigation element is focused.');
$GLOBALS['TL_LANG']['tl_page']['tabindex']       = array('Tabbing navigation', 'This number specifies the position of the current navigation element in the tabbing order. You can enter a number between 1 and 32767.');
$GLOBALS['TL_LANG']['tl_page']['autoforward']    = array('Forward to another page', 'If you choose this option, visitors will be redirected to another page (e.g. a login page or a welcome page).');
$GLOBALS['TL_LANG']['tl_page']['redirect']       = array('Redirect type', 'Temporary redirects will send a HTTP 302 header, permanent ones a HTTP 301 header.');
$GLOBALS['TL_LANG']['tl_page']['jumpTo']         = array('Forward to', 'Please select the target page from the page tree.');
$GLOBALS['TL_LANG']['tl_page']['dns']            = array('Domain name', 'If you assign a domain name to a website root page, your visitors will be redirected to this website when they enter the corresponding domain name (e.g. <em>youdomain.com</em> or <em>subdomain.yourdomain.com</em>).');
$GLOBALS['TL_LANG']['tl_page']['fallback']       = array('Language fallback', 'TYPOlight automatically redirects a visitor to a website root page in his language or to the language fallback page. If there is no language fallback page, the error message <em>No pages found</em> is displayed.');
$GLOBALS['TL_LANG']['tl_page']['published']      = array('Published', 'Unless you choose this option the page is not visible to the visitors of your website.');
$GLOBALS['TL_LANG']['tl_page']['start']          = array('Show from', 'If you enter a date here the current page will not be shown on the website before this day.');
$GLOBALS['TL_LANG']['tl_page']['stop']           = array('Show until', 'If you enter a date here the current page will not be shown on the website after this day.');


/**
 * Cache timeout labels
 */
$GLOBALS['TL_LANG']['CACHE'][0]      = 'No caching';
$GLOBALS['TL_LANG']['CACHE'][15]     = '15 seconds';
$GLOBALS['TL_LANG']['CACHE'][30]     = '30 seconds';
$GLOBALS['TL_LANG']['CACHE'][60]     = '1 minute';
$GLOBALS['TL_LANG']['CACHE'][300]    = '5 minutes';
$GLOBALS['TL_LANG']['CACHE'][900]    = '15 minutes';
$GLOBALS['TL_LANG']['CACHE'][1800]   = '30 minutes'; 
$GLOBALS['TL_LANG']['CACHE'][3600]   = '1 hour';
$GLOBALS['TL_LANG']['CACHE'][21600]  = '6 hours';
$GLOBALS['TL_LANG']['CACHE'][43200]  = '12 hours';
$GLOBALS['TL_LANG']['CACHE'][86400]  = '1 day';
$GLOBALS['TL_LANG']['CACHE'][259200] = '3 days';
$GLOBALS['TL_LANG']['CACHE'][604800] = '7 days';


/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_page']['temporary'] = 'temporary';
$GLOBALS['TL_LANG']['tl_page']['permanent'] = 'permanent';


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_page']['new']        = array('New page', 'Create a new page');
$GLOBALS['TL_LANG']['tl_page']['show']       = array('Page details', 'Show details of page ID %s');
$GLOBALS['TL_LANG']['tl_page']['cut']        = array('Move page', 'Move page ID %s');
$GLOBALS['TL_LANG']['tl_page']['copy']       = array('Duplicate page', 'Duplicate page ID %s');
$GLOBALS['TL_LANG']['tl_page']['copyChilds'] = array('Duplicate page with subpages', 'Duplicate page ID %s with subpages');
$GLOBALS['TL_LANG']['tl_page']['delete']     = array('Delete page', 'Delete page ID %s');
$GLOBALS['TL_LANG']['tl_page']['edit']       = array('Edit page', 'Edit page ID %s');
$GLOBALS['TL_LANG']['tl_page']['all']        = array('Edit all', 'Edit all shown pages');
$GLOBALS['TL_LANG']['tl_page']['pasteafter'] = array('Paste after', 'Paste after page ID %s');
$GLOBALS['TL_LANG']['tl_page']['pasteinto']  = array('Paste into', 'Paste into page ID %s');

?>
