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
 * @package    System
 * @license    LGPL
 * @filesource
 */


/**
 * Check the PHP version
 */
if (version_compare(phpversion(), '5.1.0', '<'))
{
	die('You need at least PHP version 5.1.0 to run TYPOlight!');
}


/**
 * Define root path to TYPOlight installation
 */
define('TL_ROOT', dirname(dirname(__FILE__)));


/**
 * Include functions, constants and interfaces
 */
require(TL_ROOT . '/system/functions.php');
require(TL_ROOT . '/system/constants.php');
require(TL_ROOT . '/system/interface.php');


/**
 * Set error and exception handler
 */
@set_error_handler('__error');
@set_exception_handler('__exception');


/**
 * Log PHP errors
 */
@ini_set('error_log', TL_ROOT . '/system/logs/error.log');


/**
 * Start the session
 */
session_start();


/**
 * Initialize the download manager
 */
if (!is_array($_SESSION['downloadFiles']))
{
	$_SESSION['downloadFiles'] = array();
}

if (!is_array($_SESSION['downloadToken']) || count($_SESSION['downloadToken']) > (4 * count($_SESSION['downloadFiles'])))
{
	$_SESSION['downloadToken'] = array();
}


/**
 * Load basic classes
 */
$objInput = Input::getInstance();
$objConfig = Config::getInstance();
$objEnvironment = Environment::getInstance();


/**
 * Set error_reporting
 */
@ini_set('display_errors', ($GLOBALS['TL_CONFIG']['displayErrors'] ? 1 : 0));
@error_reporting(($GLOBALS['TL_CONFIG']['displayErrors'] ? E_ALL : 0));


/**
 * Set timezone
 */
@ini_set('date.timezone', $GLOBALS['TL_CONFIG']['timeZone']);
@date_default_timezone_set($GLOBALS['TL_CONFIG']['timeZone']);


/**
 * Define relativ path to TYPOlight installation
 */
if (is_null($GLOBALS['TL_CONFIG']['websitePath']))
{
	$path = preg_replace('/\/typolight\/[^\/]*$/i', '', $objEnvironment->requestUri);
	$path = preg_replace('/\/$/i', '', $path);

	try
	{
		$GLOBALS['TL_CONFIG']['websitePath'] = $path;
		$objConfig->update("\$GLOBALS['TL_CONFIG']['websitePath']", $path);
	}

	catch (Exception $e)
	{
		log_message($e->getMessage());
	}
}

define('TL_PATH', $GLOBALS['TL_CONFIG']['websitePath']);


/**
 * Set mbstring encoding
 */
if (USE_MBSTRING && function_exists('mb_regex_encoding'))
{
	mb_regex_encoding($GLOBALS['TL_CONFIG']['characterSet']);
}


/**
 * Set the default language
 */
$languages = $objEnvironment->httpAcceptLanguage;
krsort($languages);

foreach ($languages as $v)
{
	if (is_dir(TL_ROOT . '/system/modules/backend/languages/' . $v))
	{
		$GLOBALS['TL_LANGUAGE'] = $v;
	}

	unset($v);
}

if ($objInput->post('language'))
{
	$GLOBALS['TL_LANGUAGE'] = $objInput->post('language');
}

unset($languages);


/**
 * Check referer address if there are $_POST variables
 */
if ($_POST && !$GLOBALS['TL_CONFIG']['disableRefererCheck'])
{
	$self = parse_url($objEnvironment->url);
	$referer = parse_url($objEnvironment->httpReferer);

	if (!strlen($referer['host']) || $referer['host'] != $self['host'])
	{
		trigger_error(sprintf('The current host address (%s) does not match the current referer host address (%s)', $self['host'], $referer['host']), E_USER_ERROR);
		exit;
	}
}


/**
 * Include file runonce.php if it exists
 */
if (file_exists(TL_ROOT . '/system/runonce.php'))
{
	include(TL_ROOT . '/system/runonce.php');

	$objFiles = Files::getInstance();
	$objFiles->delete('system/runonce.php');
}

?>
