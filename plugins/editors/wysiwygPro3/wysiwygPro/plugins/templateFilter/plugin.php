<?php
if (!defined('IN_WPRO')) exit;
class wproPlugin_templateFilter {
	
	var $open = array();
	var $close = array();
	
	var $replace = array();
	
	function assign($tag, $value) {
		$this->replace[$tag] = $value;
	}
	
	function protect($open, $close) {
		array_push($this->open, $open);
		array_push($this->close, $close);
	}
	
	function _makeNamedArray($arr) {
		$str = '{';
		foreach ($arr as $k => $v) {
			$str .= "'".addslashes($k)."':'".addslashes($v)."',";
		}
		if (substr($str, strlen($str)-1)==',') {
			$str = substr($str, 0, strlen($str)-1);
		}
		return $str.'}';
	}
	
	function _makeArray($arr) {
		$str = '[';
		foreach ($arr as $v) {
			$str .= "'".addslashes($v)."',";
		}
		if (substr($str, strlen($str)-1)==',') {
			$str = substr($str, 0, strlen($str)-1);
		}
		return $str.']';
	}
	
	function onBeforeMakeEditor(&$EDITOR) {
		$EDITOR->addJSPlugin('templateFilter', 'plugin_src.js');
		$EDITOR->addConfigJS('WPro.##name##._templateFilterTags = '.$this->_makeNamedArray($this->replace).';
WPro.##name##._templateFilterOpen = '.$this->_makeArray($this->open).';
WPro.##name##._templateFilterClose = '.$this->_makeArray($this->close).';');
	}
		
}
?>