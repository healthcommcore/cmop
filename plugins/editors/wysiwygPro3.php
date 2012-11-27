<?php
/* WYSIWYGPRO EDITOR PLUG-IN */

/* (C) Copyright Chris Bolt 2007 */

// This particular application integration file is licensed under the LGPL. 
// Unless specifically indicated otherwise, all other wysiwygPro FILES AND FOLDERS are NOT licensed under the LGPL! 
// WysiwygPro is a COMMERCIAL PRODUCT.
// Please see wysiwygpPro3/wysiwygPro/LICENSE AND COPYRIGHT.txt

// Do not allow direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.event.plugin');

/**
 * WysiwygPro 3 WYSIWYG Editor Plugin
 *
 * @author Chris Bolt <chris@wysiwygpro.com>
 * @package Editors
 * @since 1.5
 */
class plgEditorWysiwygPro3 extends JPlugin {
	
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param 	object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 * @since 1.5
	 */
	function plgEditorWysiwygPro3(& $subject, $config) {
		parent::__construct($subject, $config);
	}	
	
	
	/**
	 * Method to handle the onInit event.
	 *  - Initializes the WysiwygPro 3 WYSIWYG Editor
	 *
	 * @access public
	 * @return string JavaScript Initialization string
	 * @since 1.5
	 */
	function onInit() {
		// nothing required at present.
		return '';
	}

	/**
	 * WysiwygPro 3 WYSIWYG Editor - get the editor content
	 *
	 * @param string 	The name of the editor
	 * @param string    The name of the hidden form field
	 */
	function onGetContent( $editor ) {
		return "WPro.editors['".addslashes($editor)."'].getValue();";
	}
	
	/**
	 * WysiwygPro 3 WYSIWYG Editor - set the editor content
	 *
	 * @param string 	The name of the editor
	 * @param string 	The HTML code
	 */
	function onSetContent( $editor, $html ) {
		return "WPro.editors['".addslashes($editor)."'].setValue(".$html.");";
	}
	
	/**
	 * WysiwygPro 3 WYSIWYG Editor - copy editor content to form field
	 *
	 * @param string 	The name of the editor
	 */
	function onSave( $editor ) {
		return "WPro.editors['".addslashes($editor)."'].prepareSubmission();";
	}
		
	/**
	 * WysiwygPro 3 WYSIWYG Editor - display the editor
	 *
	 * @param string The name of the editor area
	 * @param string The content of the field
	 * @param string The width of the editor area
	 * @param string The height of the editor area
	 * @param int The number of columns for the editor area
	 * @param int The number of rows for the editor area
	 * @param mixed Can be boolean or array.
	 */
	function onDisplay( $name, $content, $width, $height, $col, $row, $buttons = true) {
	//function botWysiwygPro3EditorEditorArea( $name, $content, $hiddenField, $width, $height, $col, $row ) {
		
		// error_reporting ( E_ALL );
	
		$return = "";
		
		global $mainframe;

		$db			=& JFactory::getDBO();
		$language	=& JFactory::getLanguage();
		$url		= $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();
		$path		= $mainframe->isAdmin() ? JPATH_SITE : JPATH_BASE;
		$adminside = $mainframe->isAdmin() ? 1 : 0;
		
		// include WP3
		include_once ($path.DS.'plugins'.DS.'editors'.DS.'wysiwygPro3'.DS.'wysiwygPro'.DS.'wysiwygPro.class.php');
	
		// init editor
		$editor = new wysiwygPro();
		
		// required for WP session integration
		$editor->appendToQueryStrings = 'JAdminside='.$adminside;
			
		// WP Configuration
		$urlpath = $url;
		if (preg_match('/^http(|s):\/\/.*?(\/|$)/smi', $urlpath)) {
			$urlpath = $editor->stripTrailingSlash(preg_replace('/^http(|s):\/\/[^\/]+/smi', '', $urlpath));
		}
		if (file_exists($path.'/administrator/components/com_wysiwygpro3/')) {
			if ($adminside) {
				$editor->route = $urlpath.'/administrator/index2.php?option=com_wysiwygpro3&task=route';
			} else {
				$editor->route = $urlpath.'/index2.php?option=com_wysiwygpro3&task=route';
			}
		} else {
			$editor->appendToQueryStrings = 'wproPHPSessName='.session_name();
		}
	
		
		// set name
		$editor->name = $name;
		
		// protect mambot tags
		$editor->loadPlugin('templateFilter');
		$editor->plugins['templateFilter']->protect('{mos', '}');
		$editor->plugins['templateFilter']->protect('{jos', '}');
		
		$plugins = $this->params->get( 'loadPlugins', 'tagPath');
		if ($plugins && $plugins != 'null') {
			$plugins = explode(',',str_replace(' ', '', $plugins ) );
			$editor->loadPlugins($plugins);
		}
			
		// work out stylesheet path
		static $stylesheet = array();
		if (empty($stylesheet)) {
			if ($styles = $this->params->get( 'stylesheet', '' )) {
				$stylesheet = array($styles);
			} else {
				static $template = '';
				if (empty($template)) {
					
					$query = 'SELECT template'
					. ' FROM #__templates_menu'
					. ' WHERE client_id = 0'
					. ' AND menuid = 0'
					;
					$db->setQuery( $query );
					$template = $db->loadResult();
					
					$template_css_exists = is_file($editor->stripTrailingSlash($path).DS.'templates'.DS.$template.DS.'css'.DS.'template.css');
					$editor_content_css_exists = is_file($editor->stripTrailingSlash($path).DS.'templates'.DS.$template.DS.'css'.DS.'editor.css');
					
					if ($template_css_exists) {
						array_push($stylesheet, $editor->stripTrailingSlash($url).'/templates/'.$template.'/css/template.css');
					} else {
						array_push($stylesheet, $editor->stripTrailingSlash($url).'/templates/system/css/editor.css');
					}
					
					if ($template_css_exists && !$editor_content_css_exists) {
						// add default override CSS
						array_push($stylesheet, $editor->stripTrailingSlash($url).'/plugins/editors/wysiwygPro3/document.css');
					}
					
					if ($editor_content_css_exists) {
						array_push($stylesheet, $editor->stripTrailingSlash($url).'/templates/'.$template.'/css/editor.css');
					}
					
				}
			}
		}
		if (!empty($stylesheet)) {
			$editor->stylesheets = $stylesheet;
		}
		// system styles
		$editor->addCSSText('hr#system-readmore  { border: red dashed 1px; color: red; } hr.system-pagebreak { border: gray dashed 1px; color: gray; }');
		
		// image manager
		if (!$this->params->get( 'image_thumbnails', 1 )) {
			$editor->disablethumbnails();
		}
		$img = false; $doc = false; $med = false;
		if ($this->params->get( 'image_manager', 1 ) ) {
			$img = new wproDirectory();
			$img->type='image';
			$img->name='Shared Images';
			$img->URL = $editor->stripTrailingSlash($url).$editor->addLeadingSlash($this->params->get( 'image_folder', '/images/stories/'));
			$img->dir = $editor->stripTrailingSlash($path).$editor->addLeadingSlash($this->params->get( 'image_folder', '/images/stories/'));
			if (!file_exists($img->dir)) {
				mkdir($img->dir);
				chmod($img->dir, 0777);
			}
			$img->diskQuota = $this->params->get( 'imageDiskQuota', '16 MB' );
		}
		if ($this->params->get( 'document_manager', 1 ) ) {
			// document manager
			$doc = new wproDirectory();
			$doc->type='document';
			$doc->name='Shared Documents';
			$doc->URL = $editor->stripTrailingSlash($url).$editor->addLeadingSlash($this->params->get( 'document_folder', '/images/stories/'));
			$doc->dir = $editor->stripTrailingSlash($path).$editor->addLeadingSlash($this->params->get( 'document_folder', '/images/stories/'));
			if (!file_exists($doc->dir)) {
				mkdir($doc->dir);
				chmod($doc->dir, 0777);
			}
			$doc->diskQuota = $this->params->get( 'documentDiskQuota', '16 MB' );
		}
		if ($this->params->get( 'media_manager', 1 ) ) {
			// media manager
			$med = new wproDirectory();
			$med->type='media';
			$med->name='Shared Media';
			$med->URL = $editor->stripTrailingSlash($url).$editor->addLeadingSlash($this->params->get( 'media_folder', '/images/stories/'));
			$med->dir = $editor->stripTrailingSlash($path).$editor->addLeadingSlash($this->params->get( 'media_folder', '/images/stories/'));
			if (!file_exists($med->dir)) {
				mkdir($med->dir);
				chmod($med->dir, 0777);
			}
			$med->diskQuota = $this->params->get( 'mediaDiskQuota', '16 MB' );
		}
		if ($this->params->get( 'JSEmbed', 0 ) ) {
			$editor->loadPlugin('JSEmbed');	
		}
		
		// permissions
		$permissions = 0;
		if ($adminside) {
			$permissions = 'everything';
		} else {
		
			$my = $mainframe->getUser();
			$gid = intval( $my->gid );
					
			if ($gid) {
				
				switch(strtolower($my->usertype)) {
					case 'manager' :
					case 'administrator' :
					case 'super administrator' :
						$permissions = 'everything';
						break;
					case 'author' :
						$permissions = $this->params->get( 'author_permissions', 'read-only' );
						break;
					case 'editor' :
						$permissions = $this->params->get( 'editor_permissions', 'read-only' );
						break;
					case 'publisher' :
						$permissions = $this->params->get( 'publisher_permissions', 'read-only' );
						break;
					default :
						$permissions = $this->params->get( 'registered_permissions', 'read-only' );
						break;
				}
				
			} else {
				// user not logged in anywhere, no permissions.
				$permissions = $this->params->get( 'anonymous_permissions', 0 );
			}
		
		}
		
		if ($img) $img->setPermissions($permissions);
		if ($doc) $doc->setPermissions($permissions);
		if ($med) $med->setPermissions($permissions);
		
		// add folders
		if ($img&&$permissions) $editor->addDirectory($img);
		if ($doc&&$permissions) $editor->addDirectory($doc);
		if ($med&&$permissions) $editor->addDirectory($med);
		
		// User folders
		if ($this->params->get( 'user_folders', 0 )) {
			
			//if ($adminside) {
			//	$gid = $_SESSION['session_user_id'];
			//} else {
				$my = $mainframe->getUser();
				$gid = intval( $my->id );
				$ut = $my->usertype;
				switch(strtolower($my->usertype)) {
					case 'manager' :
					case 'administrator' :
					case 'super administrator' :
					case 'author' :
					case 'editor' :
					case 'publisher' :
						break;
					default :
						if (!$this->params->get( 'user_folders_allow_registered', 0 )) {
							$gid = 0;
						}
						break;
				}
			//}
			
			if ($gid) {
			
				$uimg = new wproDirectory();
				$uimg->type = 'image';
				$uimg->name = 'My Images';
				$uimg->setPermissions('everything');
				$uimg->diskQuota = $this->params->get( 'userDiskQuota', '4 MB' );
				
				$basedir = $editor->stripTrailingSlash($path).$editor->addLeadingSlash($editor->addTrailingSlash($this->params->get( 'user_folder', '/images/users/')));
				
				$uimg->URL = $editor->stripTrailingSlash($url).$editor->addLeadingSlash($editor->addTrailingSlash($this->params->get( 'user_folder', '/images/users/'))).$gid.'/';
				$uimg->dir = $editor->stripTrailingSlash($path).$editor->addLeadingSlash($editor->addTrailingSlash($this->params->get( 'user_folder', '/images/users/'))).$gid.'/';
				
				if (!file_exists($basedir)) {
					mkdir($basedir);
					chmod($basedir, 0777);
				}
				
				if (!file_exists($uimg->dir)) {
					mkdir($uimg->dir);
					chmod($uimg->dir, 0777);
				}
				
				$udoc = new wproDirectory();
				$udoc->type = 'document';
				$udoc->name = 'My Documents';
				$udoc->setPermissions('everything');
				$udoc->diskQuota = $this->params->get( 'userDiskQuota', '4 MB' );
				$udoc->URL = $editor->stripTrailingSlash($url).$editor->addLeadingSlash($editor->addTrailingSlash($this->params->get( 'user_folder', '/images/users/'))).$gid.'/';
				$udoc->dir = $editor->stripTrailingSlash($path).$editor->addLeadingSlash($editor->addTrailingSlash($this->params->get( 'user_folder', '/images/users/'))).$gid.'/';
				
				$umed = new wproDirectory();
				$umed->type = 'media';
				$umed->name = 'My Media';
				$umed->setPermissions('everything');
				$umed->diskQuota = $this->params->get( 'userDiskQuota', '4 MB' );
				$umed->URL = $editor->stripTrailingSlash($url).$editor->addLeadingSlash($editor->addTrailingSlash($this->params->get( 'user_folder', '/images/users/'))).$gid.'/';
				$umed->dir = $editor->stripTrailingSlash($path).$editor->addLeadingSlash($editor->addTrailingSlash($this->params->get( 'user_folder', '/images/users/'))).$gid.'/';
			
				$editor->addDirectory($uimg);
				$editor->addDirectory($udoc);
				$editor->addDirectory($umed);
			
			}
			
		}
		
		// What types of images can be uploaded? Separate with a comma.
		$editor->allowedImageExtensions = $this->params->get( 'allowedImageExtensions', '.jpg, .jpeg, .gif, .png' );
		
		// What types of documents can be uploaded? Separate with a comma.
		$editor->allowedDocExtensions = $this->params->get( 'allowedDocExtensions', '.html, .htm, .pdf, .doc, .docx, .rtf, .txt, .xls, .xlsx, .ppt, pptx, .pps, .ppsx, .zip, .tar, .gzip, .bzip, .sit, .dmg' );
		
		// What types of media can be uploaded? Separate with a comma.
		$editor->allowedMediaExtensions = $this->params->get( 'allowedMediaExtensions', '.swf, .wmv, .mov' );
		
		
		// maximum width of uploaded images in pixels set this to ensure that users don't destroy your site's design!!
		$editor->maxImageWidth = $this->params->get( 'maxImageWidth', 500 );
		
		// maximum height of uploaded images in pixels set this to ensure that users don't destroy your site's design!!
		$editor->maxImageHeight = $this->params->get( 'maxImageHeight', 500 );
		
		// maximum image filesize to upload
		$editor->maxImageSize = $this->params->get( 'maxImageSize', '140 KB' );
		
		// maximum size of documents to upload
		$editor->maxDocSize = $this->params->get( 'maxDocSize', '2 MB' );
		
		// maximum size of media to upload
		$editor->maxMediaSize = $this->params->get( 'maxMediaSize', '2 MB' );
		
		
		// CHMOD files and folders?
		
		$file_CHMOD = $this->params->get( 'fileCHMOD', 0 );
		$folder_CHMOD = $this->params->get( 'folderCHMOD', 0 );
		if (!empty($file_CHMOD)) {
			$editor->fileCHMOD = octdec($file_CHMOD);
		}
		if (!empty($folder_CHMOD)) {
			$editor->folderCHMOD = octdec($folder_CHMOD);
		}
		
		// color swatches
		if ($values = $this->params->get( 'colors', '' )) {
			$editor->set_color_swatches($values);
		}
		
		// set menus
	
		// styles menu
		if ($values = $this->params->get( 'styles', '' )) {
			$arr = explode(',', $values);
			$styles = array();
			foreach ($arr as $k=>$v) {
				$a2 = explode(':', $v);
				$styles[$a2[1]]=$a2[0];
			}
			$editor->stylesMenu = $styles;
		}
		
		// font menu
		if ($values = $this->params->get( 'fonts', '' )) {
			$editor->set_fontmenu($values);
		}
		
		// size menu
		if ($values = $this->params->get( 'sizes', '' )) {
			$values = explode(',', $values);
			$array = array();
			$num = count($values);
			for ($i=0; $i<$num; $i++) {
				$array[$values[$i]] = $values[$i];
			}
			$editor->set_sizemenu($array);
		}
		
		// toolbar layout
		if ($this->params->get( 'toolbarLayout', 0 )) {
			$editor->clearToolbarLayout();
		}
		if ($this->params->get( 'toolbar1', '' )) {
			$editor->addToolbar('customToolbar1', explode(',', $this->params->get( 'toolbar1', '' ) ));
		}
		if ($this->params->get( 'toolbar2', '' )) {
			$editor->addToolbar('customToolbar2', explode(',', $this->params->get( 'toolbar2', '' ) ));
		}
		if ($this->params->get( 'toolbar3', '' )) {
			$editor->addToolbar('customToolbar3', explode(',', $this->params->get( 'toolbar3', '' ) ));
		}
		
		if ($this->params->get( 'disableFeatures', '' )) {
			$f = explode(',',$this->params->get( 'disableFeatures', '' ));
			for ($z=0; $z<count($f);$z++) {
				$f[$z] = trim($f[$z]);
			}
			$editor->disableFeatures($f);
		}
		if ($this->params->get( 'enableFeatures', '' )) {
			$f = explode(',',$this->params->get( 'enableFeatures', '' ));
			for ($z=0; $z<count($f);$z++) {
				$f[$z] = trim($f[$z]);
			}
			$editor->enableFeatures($f);
		}
		
		// build links menu
		if ($this->params->get( 'content_links', 1)) {
			if (file_exists($path.'/administrator/components/com_wysiwygpro3/')) {
				if ($adminside) {
					$editor->linksBrowserURL = $urlpath.'/administrator/index2.php?option=com_wysiwygpro3&task=content&type=menu';
				} else {
					$editor->linksBrowserURL = $urlpath.'/index2.php?option=com_wysiwygpro3&task=content&type=menu';
				}
			}
		}
				
		// build inserts menu
		static $inserts = array();
		if (empty($inserts)) {
				if ( $label = $this->params->get( 'snippet1_label', '' ) ) {
					$html = $this->params->get( 'snippet1_html', '' );
					$inserts[$label] = preg_replace("/[\r\n]+<br[^>]*>/smi", ' ', $html);
				}
				if ( $label = $this->params->get( 'snippet2_label', '' ) ) {
					$html = $this->params->get( 'snippet2_html', '' );
					$inserts[$label] = preg_replace("/[\r\n]+<br[^>]*>/smi", ' ', $html);
				}
				if ( $label = $this->params->get( 'snippet3_label', '' ) ) {
					$html = $this->params->get( 'snippet3_html', '' );
					$inserts[$label] = preg_replace("/[\r\n]+<br[^>]*>/smi", ' ', $html);
				}
				if ( $label = $this->params->get( 'snippet4_label', '' ) ) {
					$html = $this->params->get( 'snippet4_html', '' );
					$inserts[$label] = preg_replace("/[\r\n]+<br[^>]*>/smi", ' ', $html);
				}
				if ( $label = $this->params->get( 'snippet5_label', '' ) ) {
					$html = $this->params->get( 'snippet5_html', '' );
					$inserts[$label] = preg_replace("/[\r\n]+<br[^>]*>/smi", ' ', $html);
				}
				$editor->set_inserts($inserts);
		}
		
		// base url
		$base = $editor->addTrailingSlash($url);
		$editor->baseURL = $base;
		
		// editor URL
		$editor->editorURL = $base.'plugins/editors/wysiwygPro3/wysiwygPro/';
		
		// line returns
		$editor->lineReturns = $this->params->get( 'lineReturns', 'P' );
		
		// charset and HTML version
		$editor->htmlVersion = $this->params->get( 'htmlVersion', 'XHTML 1.0 Transitional' );
		$iso = split( '=', (defined('_ISO') ? _ISO : 'charset=iso-8859-1') );
		$editor->htmlCharset = $iso[1];
		
		// language
		$editor->htmlLang = strtolower($language->getTag());
		$editor->htmlDirection = $language->isRTL() ? 'rtl' : 'ltr';
		
		// escape_chars
		$editor->escapeCharacters = $this->params->get( 'escapeCharacters', false );
		
		// Full URLS?
		$editor->urlFormat = $this->params->get( 'urlFormat', 'relative' );
			
		// set editor GUI language
		$editor->lang = $this->params->get( 'lang', 'en-us' );
		
		// set theme
		$editor->theme = $this->params->get( 'theme', 'default' );
		
		// set the editor code:
		$content = str_replace("&lt;", "<", $content);
		$content = str_replace("&gt;", ">", $content);
		$content = str_replace("&amp;", "&", $content);
		//$content = str_replace("&nbsp;", " ", $content);
		$content = str_replace(array("&quot;","&#039;"), array("\"", "'"), $content);
	
		$editor->value = $content;
		
		$width = $this->params->get('fixedWidth', '');
		
		$memoryUsed = $this->_memory_get_usage();
		$MB = 1048576;  // number of bytes in 1M
		if (!$memoryLimit = $this->_returnBytes(ini_get('memory_limit'))) {
			$memoryLimit = 8 * $MB;
		}
		$memoryNeeded = 2 * $MB;
		if (($memoryUsed + $memoryNeeded) > $memoryLimit) {
			$newLimit = ceil(($memoryUsed + $memoryNeeded)/$MB);
			if (ini_set( 'memory_limit', $newLimit . 'M' ));
		} 
		
		$return .= $editor->fetch((empty($width) ? '100%' : $width), intval($height)+84);
		
		$return .= $this->_displayButtons($name, $buttons);
		
		return $return;
		
	}
	
	function _returnBytes($val) {
	   if (!empty($val)) {
		   $val = trim($val);
		   $last = strtolower(preg_replace("/^[0-9]+\s*([A-Za-z]+)$/smi", "$1", $val));//strtolower($val{strlen($val)-1});
		   $val = preg_replace("/[^0-9]/smi", "", $val);
		   switch($last) {
			   // The 'G' modifier is available since PHP 5.1.0
			   case 't':
			   case 'tb':
					$val *= 1024;
			   case 'g':
			   case 'gb':
				   $val *= 1024;
			   case 'm':
			   case 'mb':
				   $val *= 1024;
			   case 'k':
			   case 'kb':
				   $val *= 1024;
		   }
		}
	   return $val;
	}
	
	function _memory_get_usage() {
		// the memroy_get_usage function is not available on all platforms
		// so make up a realistic figure if it is not
		if (function_exists('memory_get_usage')) {
			$memory_usage = memory_get_usage(true);
		} else {
			$memory_usage = 6 * 1048576;
		}
		return $memory_usage;
	}
	
	function onGetInsertMethod($name) {
		
		$doc = & JFactory::getDocument();

		$js= "function jInsertEditorText( text ) {
			WPro.editors['".addslashes($name)."'].insertAtSelection( text );
		}";
		$doc->addScriptDeclaration($js);

		return true;
	}
	
	function _displayButtons($name, $buttons) {
	
		// Load modal popup behavior
		JHTML::_('behavior.modal', 'a.modal-button');

		$args['name'] = $name;
		$args['event'] = 'onGetInsertMethod';

		$return = '';
		$results[] = $this->update($args);
		foreach ($results as $result) {
			if (is_string($result) && trim($result)) {
				$return .= $result;
			}
		}

		if(!empty($buttons))
		{
			$results = $this->_subject->getButtons($name, $buttons);

			/*
			 * This will allow plugins to attach buttons or change the behavior on the fly using AJAX
			 */
			$return .= "\n<div id=\"editor-xtd-buttons\">\n";
			foreach ($results as $button)
			{
				/*
				 * Results should be an object
				 */
				if ( $button->get('name') ) 
				{
					$modal		= ($button->get('modal')) ? 'class="modal-button"' : null;
					$href		= ($button->get('link')) ? 'href="'.$button->get('link').'"' : null;
					$onclick	= ($button->get('onclick')) ? 'onclick="'.$button->get('onclick').'"' : null;
					$return .= "<div class=\"button2-left\"><div class=\"".$button->get('name')."\"><a ".$modal." title=\"".$button->get('text')."\" ".$href." ".$onclick." rel=\"".$button->get('options')."\">".$button->get('text')."</a></div></div>\n";
				}
			}
			$return .= "</div>\n";
		}
		
		return $return;
	}

		
}

?>