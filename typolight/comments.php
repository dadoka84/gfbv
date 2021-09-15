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
$objEnvironment = Environment::getInstance();


/**
 * Class Comments
 *
 * Merge existing comments archives.
 * @copyright  Leo Feyer 2005
 * @author     Leo Feyer <leo@typolight.org>
 * @package    Controller
 */
class Comments extends Backend
{

	/**
	 * Initialize the controller
	 * 
	 * 1. Import user
	 * 2. Call parent constructor
	 * 3. Authenticate user
	 * 4. Load language files
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
		// Add new fields
		try { $this->Database->execute("ALTER TABLE `tl_content` ADD `com_template` varchar(32) NOT NULL default ''"); } catch (Exception $e) { }
		try { $this->Database->execute("ALTER TABLE `tl_content` ADD `com_perPage` smallint(5) unsigned NOT NULL default '0'"); } catch (Exception $e) { }
		try { $this->Database->execute("ALTER TABLE `tl_content` ADD `com_order` varchar(32) NOT NULL default ''"); } catch (Exception $e) { }
		try { $this->Database->execute("ALTER TABLE `tl_content` ADD `com_moderate` char(1) NOT NULL default ''"); } catch (Exception $e) { }
		try { $this->Database->execute("ALTER TABLE `tl_content` ADD `com_bbcode` char(1) NOT NULL default ''"); } catch (Exception $e) { }
		try { $this->Database->execute("ALTER TABLE `tl_content` ADD `com_disableCaptcha` char(1) NOT NULL default ''"); } catch (Exception $e) { }

		// Drop existing fields
		if ($_POST && array_key_exists('delete', $_POST))
		{
			try { $this->Database->execute("DROP TABLE `tl_comments_archive`"); } catch (Exception $e) { }

			try { $this->Database->execute("ALTER TABLE `tl_module` DROP `com_archive`"); } catch (Exception $e) { }
			try { $this->Database->execute("ALTER TABLE `tl_module` DROP `com_template`"); } catch (Exception $e) { }
			try { $this->Database->execute("ALTER TABLE `tl_module` DROP `com_protected`"); } catch (Exception $e) { }
			try { $this->Database->execute("ALTER TABLE `tl_module` DROP `com_groups`"); } catch (Exception $e) { }

			$this->import('Files');

			$this->Files->delete('system/modules/comments/Comments.php');
			$this->Files->delete('system/modules/comments/ModuleComments.php');
			$this->Files->delete('system/modules/comments/dca/tl_comments_archive.php');
			$this->Files->delete('system/modules/comments/dca/tl_module.php');
			$this->Files->delete('system/modules/comments/languages/de/tl_comments_archive.php');
			$this->Files->delete('system/modules/comments/languages/de/tl_module.php');
			$this->Files->delete('system/modules/comments/languages/en/tl_comments_archive.php');
			$this->Files->delete('system/modules/comments/languages/en/tl_module.php');
			$this->Files->delete('system/modules/comments/templates/mod_comments.tpl');

			return '<p class="tl_confirm">All deprecated fields have been removed</p>';
		}

		// Return if there is no post data
		if (!$_POST || !array_key_exists('update', $_POST))
		{
			return '';
		}

		$objNewOld = $this->Database->execute("SELECT a.title, a.moderate, a.allowHtml, m.com_template, m.com_protected, m.com_groups, m.disableCaptcha, m.com_archive AS old, c.id AS new FROM tl_comments_archive a, tl_content c, tl_module m WHERE m.type='comments' AND m.com_archive=a.id AND c.type='module' AND c.module=m.id");

		if ($objNewOld->numRows < 1)
		{
			return '';
		}

		$return = '';

		while ($objNewOld->next())
		{
			// Update comments
			$this->Database->prepare("UPDATE tl_comments SET pid=? WHERE pid=?")
						   ->execute($objNewOld->new, $objNewOld->old);

			$arrSet = array
			(
				'type' => 'comments',
				'com_perPage' => 0,
				'com_order' => 'ascending',
				'com_moderate' => $objNewOld->moderate,
				'com_bbcode' => $objNewOld->allowHtml,
				'com_template' => (strlen($objNewOld->com_template) ? $objNewOld->com_template : 'com_default'),
				'com_disableCaptcha' => $objNewOld->disableCaptcha,
				'protected' => $objNewOld->com_protected,
				'groups' => $objNewOld->com_groups
			);

			// Update content element
			$this->Database->prepare("UPDATE tl_content %s WHERE id=?")
						   ->set($arrSet)
						   ->execute($objNewOld->new);

			$return .= sprintf('<p class="tl_confirm">Archive "%s" has been updated</p>', $objNewOld->title);
		}

		return $return;
	}
}


/**
 * Instantiate controller
 */
$objComments = new Comments();
$strMessage = $objComments->run();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<!--

	This website was built with TYPOlight :: open source web content management system
	TYPOlight was developed by Leo Feyer (leo@typolight.org) :: released under GNU/GPL
	Visit project page http://www.typolight.org for more information

//-->
<head>
<base href="<?php echo $objEnvironment->base; ?>" />
<title>TYPOlight webCMS <?php echo VERSION; ?> :: Layout merger</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $GLOBALS['TL_CONFIG']['characterSet']; ?>" />
<link rel="stylesheet" type="text/css" href="system/themes/default/basic.css" media="screen" />
<link rel="stylesheet" type="text/css" href="system/themes/default/install.css" media="screen" />
<script type="text/javascript" src="typolight/mootools.js"></script>
<script type="text/javascript" src="typolight/typolight.js"></script>
<script type="text/javascript" src="system/themes/default/hover.js"></script>
</head>
<body>

<div id="header">
<h1>TYPOlight webCMS <?php echo VERSION; ?></h1>
</div>

<div id="container">
<div id="main">

<h2>TYPOlight comments merger</h2>

<h3 class="no_border">Improved comment handling</h3>

<?php echo $strMessage; ?>

<p>This tool merges your existing comment archives so that they can be used with the improved
comments module. The main difference is that the new module allows you to add comments as content
elements and does not require to create archives and modules anymore. Thus, you can basically use
comments on every page if you like.</p>

<form action="<?php echo $objEnvironment->request; ?>" class="tl_layout_merger" method="post">
<div class="tl_formbody">
<div class="tl_submit_container">
<input type="submit" name="update" alt="submit button" value="Check archives" />
<input type="submit" name="delete" alt="submit button" value="Drop deprecated fields" onclick="return confirm('Did you make sure that all archives have been updated?');" />
</div>
</div>
</form>

<p style="margin-top:24px; font-style:italic;">Hit the "Check archives" button until all archives are
marked as up to date. Then hit the "Drop deprecated fields" button to drop deprecated fields (you can
also keep  those fields since they will not do any harm).</p>

<p id="go_to_login"><a href="typolight/main.php?do=maintenance" title="Return to back end">Return to back end</a></p>

</div>
</div>

<div id="footer">
&nbsp;
</div>

</body>
</html>
