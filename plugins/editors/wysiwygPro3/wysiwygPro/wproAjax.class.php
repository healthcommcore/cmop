<?php

/* 
* WysiwygPro 3.0.3, 3 March 2008.
* (c) Copyright 2007 Chris Bolt and ViziMetrics Inc.
*/

// set the WP file directory
if (!defined('WPRO_DIR')) define('WPRO_DIR', dirname(__FILE__) . '/');

// include WP
require_once(dirname(__FILE__).'/wysiwygPro.class.php');

// set error reporting levels
if (defined('E_STRICT')) {
	if (!isset($WPRO_PRE_ERROR_LEVEL)) {$WPRO_PRE_ERROR_LEVEL = ini_get('error_reporting');}
	if ($WPRO_PRE_ERROR_LEVEL == E_STRICT) {
		error_reporting(E_ALL);
	}
}

// include class file
require_once(WPRO_DIR.'core/libs/wproAjax.class.php');

// re-set error reporting level
if (isset($WPRO_PRE_ERROR_LEVEL)) {
	error_reporting($WPRO_PRE_ERROR_LEVEL);
}

?>