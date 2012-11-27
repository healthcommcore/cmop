<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_BASE.DS.'..'.DS.'modules'.DS.'mod_roknavmenu'.DS.'lib'.DS.'BaseRokNavMenuTemplateParams.php');

/*
 * Created on Jan 16, 2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class RokNavMenuTemplateParams extends BaseRokNavMenuTemplateParams {
	function getTemplateParams($basename, $control_name, &$params){ 
							
		$html = 'Anything can really go here.  Javascript interaction.  Pull Menu items from database and out them in a javascript list in ordr to apply classes to them.<p/>';
							
		$html .= 'Sample Text Field:<br/>' .
				 '<input type="text" name="'.$control_name.'['.$basename.'_textfield]" id="'.$control_name.$basename.'_textfield" value="'.$params->get($basename.'_textfield').'" class="text_area"/><p/>';
		
		
		$html .= 'Sample Text Field 2:<br/>' .
		 		 '<input type="text" name="'.$control_name.'['.$basename.'_textfield2]" id="'.$control_name.$basename.'_textfield2" value="'.$params->get($basename.'_textfield2').'" class="text_area"/><br/>';
				
		return $html;
	}
}