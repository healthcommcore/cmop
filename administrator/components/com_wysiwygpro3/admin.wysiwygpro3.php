<?php

// This Joomla application integration file is licensed under the LGPL. 
// Unless specifically indicated otherwise, all other wysiwygPro FILES AND FOLDERS are NOT licensed under the LGPL! 
// WysiwygPro is a COMMERCIAL PRODUCT.
// Please see wysiwygpPro3/wysiwygPro/LICENSE AND COPYRIGHT.txt

// Do not allow direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

function wpro_stripTrailingSlash($var) {
	if (!empty($var)) {
		if (substr($var, strlen($var)-1) == '/') $var = substr($var, 0, strlen($var)-1);
	}
	return $var;
}

$db			=& JFactory::getDBO();
$language	=& JFactory::getLanguage();
$url		= wpro_stripTrailingSlash($mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base());
$path		= wpro_stripTrailingSlash($mainframe->isAdmin() ? JPATH_SITE : JPATH_BASE);
$adminside = $mainframe->isAdmin() ? 1 : 0;

define('WPRO_DIR', $path.'/plugins/editors/wysiwygPro3/wysiwygPro/');

switch (JRequest::getCmd('task')) {
	
	// routing
	case 'route' :
		ob_end_clean();
		
		include_once(WPRO_DIR.'wproRoute.class.php');
		wproRoute::processRequests();
		
		exit;
		break;
	// list content items	
	case 'content' :
	
		ob_end_clean();
		
		define('IN_WPRO', true);
		include_once(WPRO_DIR.'wysiwygPro.class.php');
		include_once($path.'/plugins/editors/wysiwygPro3/wysiwygPro/core/libs/wproTemplate.class.php');
		
		// get WP params
		$query = "SELECT params FROM #__plugins WHERE element = 'WysiwygPro3' AND folder = 'editors'";
		$db->setQuery( $query );
		$params = $db->loadResult();
		$params = & new JParameter($params);
		
		$wp_sef = $params->get( 'seo_links', 0);
		
		// include SEF
		
		// include SEF functions
		// TODO.
		
		// get item requests
		$req_content_type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'menu';
		$limit = intval( isset($_POST['limit']) ? $_POST['limit'] : 10 );
		$limitstart = intval( isset($_POST['limitstart']) ? $_POST['limitstart'] : 0 );
		
		//echo 'LIMIT '.$_POST['limit'];
		//echo $limit;
		
		comWysiwygPro3ShowTop($req_content_type);
		
		switch($req_content_type) {
			case 'article' :
				
				$req_section = isset($_POST['section']) ? $_POST['section'] : '-1';
				$req_category = isset($_POST['category']) ? $_POST['category'] : '-1';
				$req_search = $db->getEscaped(isset($_POST['search']) ? $_POST['search'] : '' );
				// $req_author = $db->getEscaped(isset($_POST['author']) ? $_POST['author'] : 0); // do another time
				//echo $req_section.' ';
				// build search query
				if (!empty($req_search)) {
					$arr = explode(' ',$req_search);
					$arr2 = array();
					foreach ($arr as $word) {
						array_push($arr2, 'title LIKE \'%'.addslashes($word).'%\'');
					}
					$search_sql =  ' AND ('.implode(' OR ',$arr2).')';
				} else {
					$search_sql = '';
				}
				
				// get sections
				$db->setQuery( "SELECT id, title FROM #__sections WHERE published = '1' ORDER BY ordering ASC" );
				$sections = $db->loadObjectList();
				
				// get categories
				$sql = "SELECT id, title, section FROM #__categories WHERE published = '1'";
				if (is_numeric($req_section)&&$req_section>=0) {
					$sql .= " AND section = '".$req_section."'";
				}			
				$sql .= " ORDER BY ordering ASC";
				$db->setQuery( $sql );
				$categories = $db->loadObjectList();
				
				// set up page navigation
				// get the total number of records
				$sql = "SELECT COUNT(*) FROM #__content WHERE state = '1'";
				if (is_numeric($req_section)&&$req_section>=0) {
					$sql .= " AND sectionid = '".$req_section."'";
				} 
				if (is_numeric($req_category)&&$req_category>=0) {
					$sql .= " AND catid = '".$req_category."'";
				}
				$sql .= $search_sql	;
				$db->setQuery( $sql);
				$total = $db->loadResult();
				require_once( $path . '/administrator/includes/pageNavigation.php' );
				//echo $limit;
				$pageNav = new JPagination( $total, $limitstart, $limit );
				if ($limit>0&&$limit==$total) {
					$pageNav->_viewall = false;
				}
				
				// get articles
				$sql = "SELECT id, title FROM #__content WHERE state = '1'";
				if (is_numeric($req_section)&&$req_section>=0) {
					$sql .= " AND sectionid = '".$req_section."'";
				}
				if (is_numeric($req_category)&&$req_category>=0) {
					$sql .= " AND catid = '".$req_category."'";
				}
				$sql .= $search_sql	;
				$sql .= " ORDER BY ordering ASC";
				//$sql .= $limit_sql;
				$db->setQuery($sql, $pageNav->limitstart, $pageNav->limit);
				$contentItems = $db->loadObjectList();
				comWysiwygPro3ShowArticles($contentItems, $sections, $categories, $req_search, $req_section, $req_category, $pageNav);
				
			break;
			
			case 'menu' :
			default :
				// Menu Structure
				$links = array();
				
				// menu items
				$db->setQuery( "SELECT params, title, id FROM #__modules WHERE module = 'mod_mainmenu' ORDER BY title" );
				$sections = $db->loadObjectList();
				$num = count($sections);
				for ($i=0; $i<$num; $i++) {
					$sParams = & new JParameter( $sections[$i]->params );
					//$sub = comWysiwygPro3GetMenuItems($db, $sParams->menutype, 0, 2, (($mosConfig_sef&&$wp_sef)?true:false));
					$sub = comWysiwygPro3GetMenuItems($db, $sParams->get('menutype'), 0, 2, false);
					if (!empty($sub)) {
						array_push($links, array(1, 'folder', $sections[$i]->title) );
						foreach($sub as $item) {
							array_push($links, $item);
						}
					}
					
				}
			
				comWysiwygPro3ShowMenu($links);
			
		}
		
		comWysiwygPro3ShowFooter($req_content_type);
		exit;
		
		break;
	// view configuration
	case 'config':
	default :
		if ($adminside) {
			// get WysiwygPro Mambot ID
			$db = & JFactory::getDBO();
			$query = "SELECT id"
			. "\n FROM #__plugins"
			. "\n WHERE element = 'wysiwygPro3' AND folder = 'editors'"
			;
			$db->setQuery( $query );
			$eid = $db->loadResult();
			if($eid){
				$mainframe->redirect( 'index2.php?option=com_plugins&client=site&task=edit&cid[]=' . $eid. '' );
			} else {
				$mainframe->redirect( 'index.php', 'The WysiwygPro plugins are not installed. Please install them now.' );
			}
		}
		break;
}


function comWysiwygPro3GetMenuItems($db, $menutype, $parent=0, $depth=0, $sef=false) {
	global $mainframe;

	$db			=& JFactory::getDBO();
	$url		= wysiwygPro::stripTrailingSlash($mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base());

	$links = array();
	$db->setQuery( "SELECT parent, link, sublevel, componentid, id, name as title FROM #__menu WHERE published = '1' AND menutype = '".$menutype."' AND parent='".intval($parent)."' ORDER BY ordering" );
	$contentItems = $db->loadObjectList();
	$num2 = count($contentItems);
	for ($j=0; $j<$num2; $j++) {
		
		$addItemid = ((substr($contentItems[$j]->link, 0, 10)=='index.php?') ? (strstr(strtolower($contentItems[$j]->link), '&itemid=')?false:true) : false);
		
		$link = $contentItems[$j]->link . ($addItemid ? '&Itemid='. $contentItems[$j]->id : '');
		if ($sef) {
			if (!preg_match("/^[a-z]+:/i", $link)) {
				$link = sefRelToAbs($link);
			}
		}
		if (!preg_match("/^[a-z]+:/i", $link)) {
			$link = $url.'/'.$link;
		}

		array_push($links, array(($depth+$contentItems[$j]->sublevel), $link, $contentItems[$j]->title) );
		$sub = comWysiwygPro3GetMenuItems($db, $menutype, $contentItems[$j]->id, $depth+1, $sef);
		if (!empty($sub)) {
			foreach($sub as $item) {
				array_push($links, $item);
			}
		}
	}
	
	return $links;
}


function comWysiwygPro3ShowTop($selected) {
	global $mainframe;
	
	$iso = split( '=', (defined('_ISO') ? _ISO : 'charset=utf-8') );
	header('Content-Type: text/html; charset='.$iso[1]);

	$url		= wysiwygPro::stripTrailingSlash($mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base());
	$path		= wysiwygPro::stripTrailingSlash($mainframe->isAdmin() ? JPATH_SITE : JPATH_BASE);
	
	?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $iso[1] ?>" />
<title></title>
<link rel="stylesheet" href="<?php echo $url ?>/administrator/templates/system/css/system.css" type="text/css" />
<link href="<?php echo $url ?>/administrator/templates/khepri/css/template.css" rel="stylesheet" type="text/css" />
<style type="text/css">
body {
	margin: 0px;
	padding: 0px;
}
p {
	margin: 0px;
	padding: 2px;
}
input {
	border: 1px solid #999999;
	font-size: 11px;
}
table.adminlist {
	margin-top: 2px;
}
table.adminlist th {
	padding: 0px;
}
table.adminlist td {
	padding-top: 2px;
	padding-bottom: 3px;
}
</style>
</head>

<body>
	<p style="border-bottom: 1px solid #cccccc; margin-bottom: 6px;">
	<a href="index2.php?option=com_wysiwygpro3&amp;task=content&amp;type=menu"<?php if ($selected=='menu') : ?> style="font-weight: bold"<?php endif ?>>Menu Items</a> |
	<a href="index2.php?option=com_wysiwygpro3&amp;task=content&amp;type=article"<?php if ($selected=='article') : ?> style="font-weight: bold"<?php endif ?>>Articles</a>
	</p>
	<?php
	
}
	
function comWysiwygPro3ShowFooter($req_content_type) {
	if ($req_content_type == 'menu') echo '<script type="text/javascript">RedrawAllTrees()</script>';
	?></body></html><?php
}
    
function comWysiwygPro3ShowArticles($contentItems, $sections, $categories, $req_search, $req_section, $req_category, $pageNav) {
	global $mainframe;

	$url		= wysiwygPro::stripTrailingSlash($mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base());
	$path		= wysiwygPro::stripTrailingSlash($mainframe->isAdmin() ? JPATH_SITE : JPATH_BASE);
	?>
	<script type="text/javascript">
	function submitform() {
		document.adminForm.submit();
	}
	</script>
	<form action="index2.php?option=com_wysiwygpro3" method="post" name="adminForm">
	
	<select name="section" onchange="document.forms[0].category.value='-1';document.forms[0].submit();">
	<option value="-1">Sections</option>
	<option value="0"<?php if ($req_section==0) echo ' selected="selected"'; ?>>Uncategorized</option>
	<?php 
	$num2 = count($sections);
	for ($j=0; $j<$num2; $j++) {
		echo '<option value="'.intval($sections[$j]->id).'"';
		if ($sections[$j]->id == $req_section) {
			echo ' selected="seleced"';
		}
		echo '>'.htmlspecialchars($sections[$j]->title).'</option>';
	}
	?>
	</select>
	
	<select name="category" onchange="document.forms[0].submit();">
	<option value="-1">Categories</option>
	<?php 
	$num2 = count($categories);
	for ($j=0; $j<$num2; $j++) {
		if (!is_numeric($categories[$j]->section)) continue;
		echo '<option value="'.intval($categories[$j]->id).'"';
		if ($categories[$j]->id == $req_category) {
			echo ' selected="seleced"';
		}
		echo '>'.htmlspecialchars($categories[$j]->title).'</option>';
	}
	?>
	</select>
	
	<br />
	
	Filter: <input type="text" value="<?php echo htmlspecialchars($req_search) ?>" name="search" />

	<table class="adminlist">
	<thead>
	<tr>
		<th>Title</th>
		<th>ID</th>
	  </tr>
	 </thead>
	 <tfoot>
	 <tr><td colspan="2">
	 <?php
	echo $pageNav->getListFooter();
	?>
	 </td></tr>
	 </tfoot>
	 <tbody>
	<?php
	require_once($path.'/includes/application.php');
	$num2 = count($contentItems);
	for ($j=0; $j<$num2; $j++) {
		@$itemId=$mainframe->getItemid($contentItems[$j]->id);
		$link = 'index.php?option=com_content&view=article&id='.$contentItems[$j]->id.($itemId ? '&Itemid='.$itemId : '');
		//if ($mosConfig_sef && $wp_sef) {
		//	if (!preg_match("/^[a-z]+:/i", $link)) {
		//		$link = sefRelToAbs($link);
		//	}
		//}
		if (!preg_match("/^[a-z]+:/i", $link)) {
			$link = $url.'/'.$link;
		}
		$title = $contentItems[$j]->title;
		?><tr class="row<?php echo ($j%2) ? '1' : '0' ?>">
		<td><a href="javascript:top.localLink('<?php echo addslashes($link) ?>', '<?php echo addslashes($title) ?>')"><?php echo htmlspecialchars($title) ?></a></td>
		<td><?php echo intval($contentItems[$j]->id) ?></td>
		</tr>			
		<?php
	}
	?>
	</tbody>
	</table>

	<input type="hidden" name="option" value="com_wysiwygpro3" />
	<input type="hidden" name="task" value="content" />
	<input type="hidden" name="type" value="article" />
	
	</form>
	<?php
}
	

function comWysiwygPro3BuildLinksMenu(&$UI, &$pNode, $links) {
	//print_r($links);
	for ($i=0; $i<count($links); $i++) {
		if (empty($links[$i]['title'])) continue;
		$node = $UI->createNode();
		$id = '';
		if (isset($pNode->id)) {
			$id .= $pNode->id.'_';
		} else {
			$id = 'linksTree_';
		}
		$id.=$i;
		$node->id = $id;
		$node->caption = $links[$i]['title'];
		if (!empty($links[$i]['URL'])) {
			if ($links[$i]['URL']=='folder') {
				$node->isFolder = true;
			} else {
				$node->caption_onclick = 'function (node) {parent.localLink(\''.addslashes($links[$i]['URL']).'\', \''.addslashes($links[$i]['title']).'\');}';
			}
		}
		if (!empty($links[$i]['children'])) comWysiwygPro3BuildLinksMenu($UI, $node, $links[$i]['children']);
		
		$pNode->appendChild($node);
	}
}

	
function comWysiwygPro3ShowMenu($links) {
	// convert links to V3 API
	$links = comWysiwygPro3_set_links($links);
	
	
	
	//print_r($EDITOR->links);
	$UI = new comWysiwygPro3_UITree();
	//$UI->width = 280;
	//$UI->height = 327;

	comWysiwygPro3BuildLinksMenu($UI, $UI, $links);
	$UI->display();
}

function comWysiwygPro3_set_links_children($links, &$parent) {
	for($i=0; $i<count($links); $i++) {
		$f = array();
		$depth = $links[$i][0];
		$url = $links[$i][1];
		$name = $links[$i][2];

		$f['title'] = $name;
		$f['URL'] = $url;
		$f['children'] = array();
		
		if (isset($links[$i+1])) {
			if ($links[$i+1][0]>$depth) {
				// we have gone in one folder
				$children = array();
				for($j=$i+1; $j<count($links); $j++) {
					if ($links[$j][0]<=$depth) {
						// we have left the folder
						break;
					} else {
						array_push($children, $links[$j]);
						$i++;
					}
				}
				comWysiwygPro3_set_links_children($children, $f['children']);
			}
		}
		if (is_array($parent)) array_push($parent, $f);
		//$parent[] = $f;
	}
}

function comWysiwygPro3_set_links($links) {
	$return = array();

	comWysiwygPro3_set_links_children($links, $return);
	
	return $return;
}

class comWysiwygPro3_UITree_node {
	var $id = '';
	var $caption = '';
	var $URL = NULL;
	var $target = NULL;
	var $folders = array();
	var $buttons = array();
	var $isFolder = false;
	var $expanded = false;
	var $caption_onclick = '';
	var $caption_onmouseover = '';
	var $caption_onmouseout = '';
	var $image_onclick = '';
	var $image_onmouseover = '';
	var $image_onmouseout = '';
	var $button_onclick = '';
	var $button_onmouseover = '';
	var $button_onmouseout = '';
	var $childNodes = array();
	
	function appendChild(&$node) {
		//static $c = 0;
		//$this->childNodes[$c] = &$node;
		//$c++;
		array_push($this->childNodes, $node);
	}
}
class comWysiwygPro3_UITree {
	
	var $uid = 0;
	var $nodes = array();
	var $width = 0;
	var $height = 0;
	
	function wproTemplate_UITree() {
		$this->rootNode = new comWysiwygPro3_UITree_node();
	}
	
	function createNode() {
		$node = new comWysiwygPro3_UITree_node();
		return $node;
	}
	
	function appendChild(&$node) {
		array_push($this->nodes, $node);
	}
	
	function make () {
		global $mainframe;

		$url		= wysiwygPro::stripTrailingSlash($mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base());
	
		$tpl = new wproTemplate();
		//$tpl->templates = $this->template->templates;
		$tpl->bulkAssign(array(
			'nodes' => $this->nodes,
			'UID' => 'treeUI'.$this->uid,
			'editorURL' => $url.'/plugins/editors/wysiwygPro3/wysiwygPro/',
			'themeURL' => $url.'/plugins/editors/wysiwygPro3/wysiwygPro/themes/default/wysiwygpro/',
			'width' => $this->width,
			'height' => $this->height,
		));
		$output = $tpl->fetch( WPRO_DIR.'core/tpl/UITree.tpl.php' );
		//if ($this->uid==1) {
			$output = '<script type="text/javascript" src="'.$url.'/plugins/editors/wysiwygPro3/wysiwygPro/core/js/COOLjsTreePro/cooltreepro.js"></script>
			<script type="text/javascript" src="'.$url.'/plugins/editors/wysiwygPro3/wysiwygPro/core/js/COOLjsTreePro/tree_format.js"></script>'.$output;
		//}
		return $output;
	}
	
	function fetch () {
		return $this->make();
	}
	
	function display() {
		echo $this->make();
	}


}



?>