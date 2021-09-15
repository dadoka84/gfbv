<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * -------------------------------------------------------------------------
 * FRONT END MODULES
 * -------------------------------------------------------------------------
 *
 * List all frontend modules and their classes.
 */
array_insert($GLOBALS['FE_MOD'], 2, array
(
	'navigationMenu' => array
	(
		'atoz'							=> 'ModuleAtoZ',
	)
));

?>
