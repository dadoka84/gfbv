<?php
/**
 * Add palettes to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['atoz'] 							= 'name,type;headline,atoz_subheader;rootPage';

/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['atoz_subheader'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['atoz_subheader'],
	'default'                 => 'h2',
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'                 => array('h1', 'h2', 'h3', 'h4', 'h5', 'h6')
);
?>
