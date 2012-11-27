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
require_once (dirname(__FILE__).DS.'helper.php');

modRokajaxsearchHelper::inizialize($params->get('include_css'), $params->get('offset_search_result'), $params);

require(JModuleHelper::getLayoutPath('mod_rokajaxsearch'));