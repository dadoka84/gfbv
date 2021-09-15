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
 * Class Layout
 *
 * Merge old layouts to the new CSS framework.
 * @copyright  Leo Feyer 2005
 * @author     Leo Feyer <leo@typolight.org>
 * @package    Controller
 */
class Layout extends Backend
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
		try { $this->Database->execute("ALTER TABLE tl_layout ADD `widthLeft` varchar(255) NOT NULL default ''"); } catch (Exception $e) { }
		try { $this->Database->execute("ALTER TABLE tl_layout ADD `widthRight` varchar(255) NOT NULL default ''"); } catch (Exception $e) { }
		try { $this->Database->execute("ALTER TABLE tl_layout ADD `header` char(1) NOT NULL default ''"); } catch (Exception $e) { }
		try { $this->Database->execute("ALTER TABLE tl_layout ADD `headerHeight` varchar(255) NOT NULL default ''"); } catch (Exception $e) { }
		try { $this->Database->execute("ALTER TABLE tl_layout ADD `footer` char(1) NOT NULL default ''"); } catch (Exception $e) { }
		try { $this->Database->execute("ALTER TABLE tl_layout ADD `footerHeight` varchar(255) NOT NULL default ''"); } catch (Exception $e) { }
		try { $this->Database->execute("ALTER TABLE tl_layout ADD `static` char(1) NOT NULL default ''"); } catch (Exception $e) { }
		try { $this->Database->execute("ALTER TABLE tl_layout ADD `width` varchar(255) NOT NULL default ''"); } catch (Exception $e) { }

		// Drop existing fields
		if ($_POST && array_key_exists('delete', $_POST))
		{
			try { $this->Database->execute("ALTER TABLE `tl_layout` DROP `type`"); } catch (Exception $e) { }
			try { $this->Database->execute("ALTER TABLE `tl_layout` DROP `size`"); } catch (Exception $e) { }
			try { $this->Database->execute("ALTER TABLE `tl_layout` DROP `heightHF`"); } catch (Exception $e) { }
			try { $this->Database->execute("ALTER TABLE `tl_layout` DROP `widthLPx`"); } catch (Exception $e) { }
			try { $this->Database->execute("ALTER TABLE `tl_layout` DROP `widthLPc`"); } catch (Exception $e) { }
			try { $this->Database->execute("ALTER TABLE `tl_layout` DROP `widthMPx`"); } catch (Exception $e) { }
			try { $this->Database->execute("ALTER TABLE `tl_layout` DROP `widthMPc`"); } catch (Exception $e) { }
			try { $this->Database->execute("ALTER TABLE `tl_layout` DROP `widthRPx`"); } catch (Exception $e) { }
			try { $this->Database->execute("ALTER TABLE `tl_layout` DROP `widthRPc`"); } catch (Exception $e) { }
			try { $this->Database->execute("ALTER TABLE `tl_layout` DROP `paddingPx`"); } catch (Exception $e) { }
			try { $this->Database->execute("ALTER TABLE `tl_layout` DROP `paddingPc`"); } catch (Exception $e) { }

			return '<p class="tl_confirm">All deprecated fields have been removed</p>';
		}

		// Return if there is no post data
		if (!$_POST || !array_key_exists('update', $_POST))
		{
			return '';
		}

		$return = '';
		$objLayout = $this->Database->execute("SELECT * FROM tl_layout");

		while ($objLayout->next())
		{
			// Layout is up to date
			if ($objLayout->widthLeft || $objLayout->widthRight || $objLayout->header || $objLayout->headerHeight || $objLayout->footer || $objLayout->footerHeight || $objLayout->static || $objLayout->width)
			{
				$return .= sprintf('<p class="tl_info">Layout "%s" is up to date</p>', $objLayout->name);
				continue;
			}

			$arrSet = array();

			// Static layouts
			if ($objLayout->type == 'static')
			{
				$arrSet['static'] = 1;
			}

			$arrHF = deserialize($objLayout->heightHF);

			// Header
			if ($arrHF[0])
			{
				$arrSet['header'] = 1;
				$arrSet['headerHeight'] = serialize(array('value'=>$arrHF[0], 'unit'=>(($objLayout->type == 'static') ? 'px' : '%')));
			}

			// Footer
			if ($arrHF[1])
			{
				$arrSet['footer'] = 1;
				$arrSet['footerHeight'] = serialize(array('value'=>$arrHF[1], 'unit'=>(($objLayout->type == 'static') ? 'px' : '%')));
			}

			// Static columns
			if ($objLayout->type == 'static')
			{
				$arrSet['widthLeft'] = serialize(array('value'=>$objLayout->widthLPx, 'unit'=>'px'));
				$arrSet['widthRight'] = serialize(array('value'=>$objLayout->widthRPx, 'unit'=>'px'));
			}

			// Liquid columns
			else
			{
				$arrSet['widthLeft'] = serialize(array('value'=>$objLayout->widthLPc, 'unit'=>'%'));
				$arrSet['widthRight'] = serialize(array('value'=>$objLayout->widthRPc, 'unit'=>'%'));
			}

			// Overall width
			if ($objLayout->type == 'static')
			{
				$intWidth = $objLayout->widthMPx;

				if ($objLayout->cols == '2cll' || $objLayout->cols == '3cl')
				{
					$intWidth += $objLayout->widthLPx;
				}

				if ($objLayout->cols == '2clr' || $objLayout->cols == '3cl')
				{
					$intWidth += $objLayout->widthRPx;
				}

				$arrSet['width'] = serialize(array('value'=>$intWidth, 'unit'=>'px'));
			}

			// Centered box
			if ($objLayout->cols == '1c')
			{
				$arrSet['cols'] = '1cl';
			}

			// Update record
			$this->Database->prepare("UPDATE tl_layout %s WHERE id=?")
						   ->set($arrSet)
						   ->execute($objLayout->id);

			$return .= sprintf('<p class="tl_confirm">Layout "%s" has been updated</p>', $objLayout->name);
		}

		return $return;
	}
}


/**
 * Instantiate controller
 */
$objLayout = new Layout();
$strMessage = $objLayout->run();

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

<h2>TYPOlight layout merger</h2>

<h3 class="no_border">New CSS framework</h3>

<?php echo $strMessage; ?>

<p>This tool merges your old page layouts so they can be used with the new CSS framework. The new CSS 
framework handles especially liquid layouts much better and supports even more browsers. It also allows 
to use any type of unit instead of either pixel or percent. You can even combine different units!</p>

<form action="<?php echo $objEnvironment->request; ?>" class="tl_layout_merger" method="post">
<div class="tl_formbody">
<div class="tl_submit_container">
<input type="submit" name="update" alt="submit button" value="Check layouts" />
<input type="submit" name="delete" alt="submit button" value="Drop deprecated fields" onclick="return confirm('Did you make sure that all layouts have been updated?');" />
</div>
</div>
</form>

<p style="margin-top:24px; font-style:italic;">Hit the "Check layouts" button until all layouts are marked 
as up to date. Then hit the "Drop deprecated fields" button to drop deprecated fields (you can also keep 
those fields since they will not do any harm).</p>

<p style="margin-top:36px; padding:12px 12px 9px 12px; border:1px solid #ff9900; text-align:center; background-color:#ffffcc;"><a href="typolight/comments.php" title="Merge comment archives" style="font-size:18px;">Once you are finished click here to merge your comment archives</a></p>

</div>
</div>

<div id="footer">
&nbsp;
</div>

</body>
</html>
