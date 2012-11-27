<?php
/**
* @package mod_rokajaxsearch
* @copyright	Copyright (C) 2008 RocketTheme. All rights reserved.
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL, see LICENSE.php
* RokAjaxSearch is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*
* Inspired on PixSearch Joomla! module by Henrik Hussfelt <henrik@pixpro.net>
*/

defined('_JEXEC') or die('Restricted access');

class modRokajaxsearchHelper {
	function inizialize($css_style, $offset, &$params){
		global $mainframe;
		
		JHTML::_('behavior.mootools');
		$doc =& JFactory::getDocument();
		
		$css = modRokajaxsearchHelper::getCSSPath('rokajaxsearch.css', 'mod_rokajaxsearch');
		
		if($css_style == 1 && $css != false) $doc->addStyleSheet($css);
		$doc->addScript(JURI::Root(true)."/modules/mod_rokajaxsearch/js/rokajaxsearch.js");

		
		if ($params->get('websearch', 0) == 1 && $params->get('websearch_api') != '') { 
			$doc->addScript("http://www.google.com/jsapi?key=".$params->get('websearch_api'));
			$doc->addScriptDeclaration("google.load('search', '1.0', {nocss: true});");
		}
	}
	
	function getCSSPath($cssfile, $module) {
		global $mainframe;
		$tPath = 'templates/'.$mainframe->getTemplate().'/css/' . $cssfile . '-disabled';
		$bPath = 'modules/'.$module.'/css/' . $cssfile;

		// If the template is asking for it, 
		// don't include default rokajaxsearch css
		if (!file_exists(JPATH_BASE.DS.$tPath)) {
			return JURI::Base().'/'.$bPath;
		} else {
			return false;
		}
	}
	
}