<?php
/**
* @version		$Id: mod_rokstories.php 9764 2009-04-17 07:48:11Z djamil $
* @package		RocketTheme
* @copyright	Copyright (C) 2005 - 2008 RocketTheme, LLC. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');

JHTML::_('behavior.mootools');
$doc =& JFactory::getDocument();
$doc->addScript(JURI::Root(true)."/modules/mod_rokstories/tmpl/js/rokstories.js");

// Cache this basd on access level
$conf =& JFactory::getConfig();
if ($conf->getValue('config.caching') && $params->get("module_cache", 0)) { 
	$user =& JFactory::getUser();
	$aid  = (int) $user->get('aid', 0);
	switch ($aid) {
	    case 0:
	        $level = "public";
	        break;
	    case 1:
	        $level = "registered";
	        break;
	    case 2:
	        $level = "special";
	        break;
	}
	// Cache this based on access level
	$cache =& JFactory::getCache('mod_rokstories-' . $level);
	$list = $cache->call(array('modRokStoriesHelper', 'getList'), $params);
}
else {
    $list = modRokStoriesHelper::getList($params);
}


require(JModuleHelper::getLayoutPath('mod_rokstories'));