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
 * @package    Backend
 * @license    LGPL
 * @filesource
 */


/**
 * Class ChmodTable
 *
 * Provide methods to handle CHMOD tables.
 * @copyright  Leo Feyer 2005
 * @author     Leo Feyer <leo@typolight.org>
 * @package    Controller
 */
class ChmodTable extends Widget
{

	/**
	 * Submit user input
	 * @var boolean
	 */
	protected $blnSubmitInput = true;

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_widget';


	/**
	 * Add specific attributes
	 * @param string
	 * @param mixed
	 */
	public function __set($strKey, $varValue)
	{
		switch ($strKey)
		{
			case 'mandatory':
				$this->arrConfiguration['mandatory'] = $varValue ? true : false;
				break;

			default:
				parent::__set($strKey, $varValue);
				break;
		}
	}


	/**
	 * Generate the widget and return it as string
	 * @return string
	 */
	public function generate()
	{
		$arrObjects = array('u'=>'cuser', 'g'=>'cgroup', 'w'=>'cworld');

		$return = '  <table cellpadding="2" cellspacing="2" id="ctrl_defaultChmod" class="tl_chmod" summary="Table holds input fields">
    <tr>
      <th></th>
      <th scope="col">'.$GLOBALS['TL_LANG']['CHMOD']['editpage'].'</th>
      <th scope="col">'.$GLOBALS['TL_LANG']['CHMOD']['editnavigation'].'</th>
      <th scope="col">'.$GLOBALS['TL_LANG']['CHMOD']['deletepage'].'</th>
      <th scope="col">'.$GLOBALS['TL_LANG']['CHMOD']['editarticles'].'</th>
      <th scope="col">'.$GLOBALS['TL_LANG']['CHMOD']['movearticles'].'</th>
      <th scope="col">'.$GLOBALS['TL_LANG']['CHMOD']['deletearticles'].'</th>
    </tr>';

		// Build rows for user, group and world
		foreach ($arrObjects as $k=>$v)
		{
			$return .= '
    <tr>
      <td scope="row" class="th">'.$GLOBALS['TL_LANG']['CHMOD'][$v].'</td>';

			// Add checkboxes
			for ($j=1; $j<=6; $j++)
			{
				$return .= '
      <td><input type="checkbox" name="'.$this->strName.'[]" value="'.specialchars($k.$j).'"'.$this->optionChecked($k.$j, $this->varValue).' onfocus="Backend.getScrollOffset();" /></td>';
			}

			$return .= '
    </tr>';
		}

		return $return.'
  </table>';
	}
}

?>
