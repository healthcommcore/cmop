<?php
/**
 * @version $Id$
 * @package RocketWerx
 * @subpackage	RokNavMenu
 * @copyright Copyright (C) 2009 RocketWerx. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.base.tree');
//require_once (dirname(__FILE__).DS.'RokNavMenuNode.php');


/**
 * Base Class for menu tree nodes
 */
 
 class RokMenuTreeBase {
 	
	
 	/**
	 * Base ID for the menu  as ultimate parent
	 */
	var $id = 0;
	var $parent 	= 0;
	var $_parentRef = null;
	var $level 		= -1;
	var $access 	= 2;
	
	var $_check_access_level = null;
	
	var $_children = array();
	
	function __construct($params = null)
	{	
		$this->_params = &$params;
	}
	/**
	 * Menu parameters
	 */
	var $_params = null;
	
	function addChild(&$node) {
		if (!$node->isAccessable()){
			return true;
		}
		if ( $this->id == $node->parent) {
			$node->_parentRef = &$this;
			$this->_children[$node->id] = & $node;
			return true;			
		}
		else if ($this->hasChildren()) {
			reset($this->_children);
			while (list($key, $value) = each($this->_children)) {
				$child =& $this->_children[$key]; 
				if ($child->addChild($node)) {
					return true;
				}
			}
		}
		return false;
	}
	
 	function hasChildren()
	{
		return count($this->_children);
	}

	function &getChildren()
	{
		return $this->_children;
	}
	
	function setParameters($params) {
		if (null != $params) { 
			$this->_params = new JParameter($params);
		}
	}
	
	function getParameter($param) {
		if (null == $param || null == $this->_params) {
			return null;
		}
		return $this->_params->get($param);		
	}
	
	function &findChild($node_id) {
		if (array_key_exists($node_id, $this->_children)) {
			return $this->_children[$node_id];
		}
		else if ($this->hasChildren()) {
			reset($this->_children);
			while (list($key, $value) = each($this->_children)) {
				$child =& $this->_children[$key]; 
				$wanted_node = $child->findChild($node_id);
				if ($wanted_node !== false) {
					return $wanted_node;
				}
			}
		}
		$ret = false;
		return $ret;
	}
	
	function removeChild($node_id) {
		if (array_key_exists($node_id, $this->_children)) {
			unset($this->_children[$node_id]);
			return true;
		}
		else if ($this->hasChildren()) {
			reset($this->_children);
			while (list($key, $value) = each($this->_children)) {
				$child =& $this->_children[$key]; 
				$ret = $child->removeChild($node_id);
				if ($ret === true) {
					return $ret;
				}
			}
		}
		return false;
	}
	
	function removeLevel($end) {
		if ( $this->level == $end ) {
			$this->_children = array();
		}
		else if ($this->level < $end) {
			if ($this->hasChildren()) { 
				reset($this->_children);
				while (list($key, $value) = each($this->_children)) {
					$child =& $this->_children[$key]; 
					$child->removeLevel($end);
				}
			}
		}
	}
	
	function isAccessable(){
		$user =& JFactory::getUser();
		//$aid  = (int) $user->get('aid', 0);
		$aid = ($this->_check_access_level != null)? (int)$this->_check_access_level: (int) $user->get('aid', 0);
		if (null == $this->access ) {
			return null;
		}
		else if ($aid >= $this->access) {
			return true;	
		}
		else {
			return false;
		}
	}
	
	function getParent() {
		return $this->_parentRef;
	}
	
 }
 
 
/**
 * Rok Nav Menu Tree Class.
 */
class RokNavMenuTree extends RokMenuTreeBase
{
//	const MENUPARAM_MENU_IMAGES 		= "menu_images";
//	const MENUPARAM_LIMIT_LEVELS		= "limit_levels";
//	const MENUPARAM_START_LEVEL 		= "startLevel";
//	const MENUPARAM_END_LEVEL 			= "endLevel";
//	const MENUPARAM_SHOW_ALL_CHILDREN 	= "showAllChildren";
//	const MENUPARAM_TAG_ID 			= "tag_id";
//	const MENUPARAM_CLASS_SUFFIX 		= "class_sfx";
//	const MENUPARAM_MENU_IMAGES_LINK	= "menu_images_link";
//	const MENUPARAM_MAX_DEPTH 			= "maxdepth";
	

	
	function addNode(&$params, $item)
	{
		// Get menu item data
		
		$node = $this->_getItemData($params, $item);
		$node->_check_access_level = $params->get('check_access_level',null);
		if ($node !== false) {
			return $this->addChild($node);	
		}
		else {
			return true;
		}
		
	}
	
	
	function resetTop($top_node_id) {
		$new_top_node = $this->findChild($top_node_id); 
		if ($new_top_node !== false)
		{
			$this->id = $new_top_node->id;
			$this->_children = $new_top_node->getChildren();
		}
		else {
			return false;
		}
	}
	
	function _getItemData(&$params, $item)
	{		
		//Create the new Node
		$node = new RokNavMenuNode();
		
		$tmp = null;
		// Menu Link is a special type that is a link to another item
		if ($item->type == 'menulink')
		{
			$menu = &JSite::getMenu();
			if ($newItem = $menu->getItem($item->query['Itemid'])) {
    			$tmp = clone($newItem);
				$tmp->name	 = $item->name;
				$tmp->mid	 = $item->id;
				$tmp->parent = $item->parent;
				$tmp->url = null;
				$tmp->nav = 'current';
			} 
		} 
		
		if ($item->type != 'menulink' || ($item->type == 'menulink' && $tmp == null)){
			$tmp = clone($item);
			$tmp->name = $item->name;
			$tmp->mid = $tmp->id;
			$tmp->url = null;
			$tmp->nav = 'current';
		}

		$iParams = new JParameter($tmp->params);
		
		if ($params->get('menu_images') && $iParams->get('menu_image') && $iParams->get('menu_image') != -1) {
			$image = JURI::base(true).'/images/stories/'.$iParams->get('menu_image');
			if($tmp->ionly){
				 $tmp->name = null;
			 }
		} else {
			$image = null;
		}
		
		
		switch ($tmp->type)
		{
			case 'separator':
				$tmp->outtype = 'separator';
				break;
			case 'url':
				if ((strpos($tmp->link, 'index.php?') === 0) && (strpos($tmp->link, 'Itemid=') === false)) {
					$tmp->url = $tmp->link.'&amp;Itemid='.$tmp->id;
				} else {
					$tmp->url = $tmp->link;
				}
				$tmp->outtype = 'menuitem';
				break;
			default :
				$router = JSite::getRouter();
				$tmp->url = $router->getMode() == JROUTER_MODE_SEF ? 'index.php?Itemid='.$tmp->id : $tmp->link.'&Itemid='.$tmp->id;
				$tmp->outtype = 'menuitem';
				break;
		}


		if ($tmp->url != null)
		{
			// set the target based on menu item options
			switch ($tmp->browserNav)
			{
				default:
				case 1:
					$tmp->nav = 'new';
					break;
				case 2:
					$tmp->url = str_replace('index.php', 'index2.php', $tmp->url);
					$tmp->nav = 'newnotool';
					break;
				case 0:
				default:	
					$tmp->nav = 'current';
					break;
			}
			
			// Get the final URL
			if ($tmp->home == 1) {  											// Set Home Links to the Base
				$tmp->url = JURI::base();
			} 
			else if ($tmp->type != 'separator' && $tmp->type != 'url') {		
				$iSecure = $iParams->def('secure', 0);	
			
				// Set Content links that can be put through JRoute through it
				if (strcasecmp(substr($tmp->url, 0, 4), 'http') && (strpos($tmp->url, 'index.php?') !== false)) {
					$prefix = '';	
					if ($this->_params->get('url_type','relative') == 'full') {
						$uri =& JURI::getInstance();
						$prefix = $uri->toString( array('scheme', 'host', 'port'));
					}
					$tmp->url = $prefix.JRoute::_($tmp->url, true, $iSecure);
				}
				// TODO force index2.php urls to ssl
				else if (strcasecmp(substr($tmp->url, 0, 4), 'http') && (strpos($tmp->url, 'index.php2?') !== false)) {
					
				}
			} 
			else if($tmp->type == 'url') {
				$tmp->url = str_replace('&', '&amp;', $tmp->url);
			}
			else {
				
			}
		}

		
		$node->id 		= $tmp->mid;
		$node->parent 	= $tmp->parent;
		$node->title	= $tmp->name;
		$node->access	= $tmp->access;
		$node->link		= $tmp->url;
		$node->level 	= $item->sublevel;
		$node->image 	= $image;
		$node->alias 	= $tmp->alias;
		$node->nav		= $tmp->nav;
		
		
		$node->setParameters($tmp->params);
		$node->type = $tmp->outtype;
		$node->order = $item->ordering;
		$node->addListItemClass("item" . $node->id);
		$node->addSpanClass($tmp->outtype);		
		return $node;
	}
}

/**
 * RokNavMenuNode
 */
class RokNavMenuNode extends RokMenuTreeBase
{	
//	const TYPE_MENU_LINK 				= "menulink";
//	const TYPE_SEPARATOR 				= "separator";
//	const TYPE_MENU_ITEM 				= "menuitem";
//	const TYPE_URL 						= "url";
//	
//	const TARGET_CURRENT 				= "current";
//	const TARGET_NEW 					= "new";
//	const TARGET_NEW_NO_TOOLBAR 		= "newnotool";
//	
//	const CLASS_PARENT					= "parent";
//	const CLASS_ACTIVE					= "active";
//	const ID_CURRENT					= "current";
	
	
	var $title 		= null;
	var $link 		= null;
	
	var $image 		= null;
	var $alias 		= null;
	var $type 		= null;
	var $target 	= null;
	var $order 		= null;
	var $nav		= null;
	

	var $_li_classes = array();
	var $_a_classes = array();
	var $_span_classes = array();
	var $css_id = null;
	
	function getListItemClasses() {
		$html_classes = " ";
		foreach ($this->_li_classes as $class) {
			$html_classes .= $class. " ";			
		}
		return rtrim(ltrim($html_classes));		
	}
	
	function addListItemClass($class) {
		$this->_li_classes[] = $class;	
	}
	
	function hasListItemClasses(){
		return count($this->_li_classes);
	}
	
	
	function getLinkClasses() {
		$html_classes = " ";
		foreach ($this->_a_classes as $class) {
			$html_classes .= $class. " ";			
		}
		return rtrim(ltrim($html_classes));		
	}
	
	function addLinkClass($class) {
		$this->_a_classes[] = $class;	
	}
	
	function hasLinkClasses(){
		return count($this->_a_classes);
	}
		
	function getSpanClasses() {
		$html_classes = " ";
		foreach ($this->_span_classes as $class) {
			$html_classes .= $class. " ";			
		}
		return rtrim(ltrim($html_classes));		
	}
	
	function addSpanClass($class) {
		$this->_span_classes[] = $class;	
	}
	function hasSpanClasses(){
		return count($this->_span_classes);
	}
	
	function addChild(&$node) {
		if($node->isAccessable()) {
			
			//$ret = parent::addChild($node);
			$ret = false;
			
			if (!$node->isAccessable()){
				$ret = true;
			}
			if ( $this->id == $node->parent) {
				$node->_parentRef = &$this;
				$this->_children[$node->id] = & $node;
				$ret = true;			
			}
			else if ($this->hasChildren()) {
				reset($this->_children);
				while (list($key, $value) = each($this->_children)) {
					$child =& $this->_children[$key]; 
					if ($child->addChild($node)) {
						return true;
					}
				}
			}
			if ($ret === true) { 
				if (!array_search('parent', $this->_li_classes)) { 
					$this->addListItemClass('parent');
				}
			}
			return $ret;
		}
		return true; // item is not accessable so return true to remove from the stack 
	}
}

