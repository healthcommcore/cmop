<?php
if (!defined('IN_WPRO')) exit;
/* 
this is an example of a PHP plugin, it can be used to make any PHP API calls.
you may remove any event functions that you do not need.

Each event function is passed a copy of the current editor object which it should 
receive by reference and use to make API calls.

Some functions may be passed a second paramater containing information for the
specific event. Usually this would be an associative array.
Please see the online documentation for more information.
http://www.wysiwygpro.com/index.php?id=168

Loading this plugin:

$editor = new wysiwygPro();
$editor->loadPlugin('EXAMPLE');
$editor->display();

The class name is always wproPlugin_ followed by the name of the plugin.

*/
class wproPlugin_EXAMPLE {
	
	/* called when the plugin is loaded */
	function init (&$EDITOR) {
		/* you can use PHP plugins to load JSPlugins: */
		$EDITOR->addJSPlugin('EXAMPLE', 'plugin.js');
		
		/* PHP plugins are often used to add custom buttons to the toolbar */
		
		/* first register the buttons */
		$EDITOR->registerButton('exButton1', 'Example Button 1', 'WPro.editors[\'##originalName##\'].openDialogPlugin(\'EXAMPLE&action=1\', 485, 300)', '##editorURL##plugins/EXAMPLE/includes/exButton1.gif');
		$EDITOR->registerButton('exButton2', 'Example Button 2', 'WPro.editors[\'##originalName##\'].openDialogPlugin(\'EXAMPLE&action=2\', 220, 220)', '##editorURL##plugins/EXAMPLE/includes/exButton2.gif');
		$EDITOR->registerButton('exButton3', 'Example Button 3', 'WPro.editors[\'##originalName##\'].plugins[\'EXAMPLE\'].exButton3Clicked()', '##editorURL##plugins/EXAMPLE/includes/exButton3.gif');
	
		/* then add them to the current toolbar layout, 'end:1' adds them to the end of the second toolbar, toolbars are 0 indexed.
		Or instead we could leave it up to the developer to add the buttons if they want to use them */
		$EDITOR->addRegisteredButton('exButton1', 'end:1');
		$EDITOR->addRegisteredButton('exButton2', 'end:1');
		$EDITOR->addRegisteredButton('exButton3', 'end:1');
		
		/* add a separator before the buttons */
		$EDITOR->addRegisteredButton('separator', 'before:exButton1');
	}
	
	
	/* EVENT functions, these optional functions are called when events happen in the editor */
	
	/* called just before the editor parameters are processed */
	function onBeforeMakeEditor (&$EDITOR) {
	
	}
	
	/* called just before any dialog plugin is displayed. It is called inside the context of the dialog window. */
	function onBeforeDisplayDialog (&$EDITOR) {
	
	}
	
	// there are many more event functions that can be placed in here, please see the online documentation.
	// http://www.wysiwygpro.com/index.php?id=168
	
}
?>