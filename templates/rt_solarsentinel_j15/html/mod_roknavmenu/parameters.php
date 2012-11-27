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
	    
	    $document   =& JFactory::getDocument();
	    $css        = ".paramlist_value span {background:#eee;font-weight:bold;padding:2px 4px;width:170px;display:inline-block;border-bottom:1px solid #ddd}";
	    
	    $document->addStyleDeclaration($css);
							
		$html = '<p>These custom template parameters are for the <strong>RocketTheme Solar Sentinel</strong> Template</p>';
							
		$html .= '<p><span>Drop Down Rows Per Column:</span>' .
				 '<input type="text" name="'.$control_name.'['.$basename.'_menuRowsPerColumn]" id="'.$control_name.$basename.'_menuRowsPerColumn" value="'.$params->get($basename.'_menuRowsPerColumn').'" class="text_area" size="2" /></p>';
		
		
		$html .= '<p><span>Drop Down Columns:</span>' .
		 		 '<input type="text" name="'.$control_name.'['.$basename.'_menuColumns]" id="'.$control_name.$basename.'_menuColumns" value="'.$params->get($basename.'_menuColumns').'" class="text_area" size="2" /></p>';
		  
 		$html .= '<p><span>Multi-Column Level:</span>' .
 		 		 '<input type="text" name="'.$control_name.'['.$basename.'_menuMultiColLevel]" id="'.$control_name.$basename.'_menuMultiColLevel" value="'.$params->get($basename.'_menuMultiColLevel').'" class="text_area" size="2" /></p>';
				
		return $html;
	}
}