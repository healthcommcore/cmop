<?php
if (!defined('IN_WPRO')) exit;
/*
This is an example of a dialog plugin. This plugin can be used to display two 
very different dialog screens.

To open the first dialog (from javascript):

WPro.editors["editorName"].openDialogPlugin("EXAMPLE&action=1", 485, 300);

To open the second dialog (from javascript):

WPro.editors["editorName"].openDialogPlugin("EXAMPLE&action=2", 220, 220);

For more information see the developer documentation:
http://www.wysiwygpro.com/index.php?id=292


There are a number of global variables that are available to a dialog plugin:

$EDITOR
A wysiwygPro object, the current editor object. Use this to get information about the
current editor's configuration.

$DIALOG
A wproDialog object, contains properties and methods for configuring the dialog.


The class name is always wproDialogPlugin_ followed by the name of the plugin.

*/

class wproDialogPlugin_EXAMPLE {
	
	// the init function is called when the editor loads. This example does not make use of it.
	function init (&$DIALOG) {
				
	}
	
	/*
	The runAction function is called after the init function. It is passed the action paramater.
	If this plugin is used to display more than one dialog then the action paramater can be used to specify which dialog screen to display.
	*/
	function runAction($action, $params) {
		global $DIALOG, $EDITOR;
		switch (strtolower($action)) {
			case '1' :
				// this is dialog window one.
			
				// sets the title of the dialog:
				$DIALOG->title = 'Example Dialog 1';
				
				// we can add content to the head of the dialog like this:
				$DIALOG->headContent->add('<link rel="stylesheet" href="plugins/EXAMPLE/includes/dialog.css" type="text/css" />');
				
				// sets the template file to use (this forms the body of the dialog):
				$DIALOG->bodyInclude = WPRO_DIR.'plugins/EXAMPLE/includes/1.tpl.php';
				
				// you can assign variables to the template file like this:
				$message = 'Enter some HTML code below, then press Insert to insert it into your document:';
				$DIALOG->assign('message', $message);
				
				// sets the buttons to display at the bottom of the dialog:
				// (you can use an empty array for no buttons)
				// (if you don't set any buttons then the default Ok, Cancel buttons are used)
				$DIALOG->options = array(
					array(
						'type'=>'submit',
						'name'=>'ok',
						'value'=>$DIALOG->langEngine->get('core', 'insert'),
					),
					array(
						'onclick' => 'dialog.close()',
						'type'=>'button',
						'name'=>'close',
						'value'=>$DIALOG->langEngine->get('core', 'cancel'),
					),
				);
				break;
			case '2' :
				// this is dialog window 2
			
				// sets the title of the dialog:
				$DIALOG->title = 'Example Dialog 2';
				
				/*
				It is easy to use AJAX from within a dialog plugin. 
				To make a method of your plugin callable from JavaScript:
				
				$DIALOG->registerAjaxFunction(array('jsName', &$this, 'methodName'));
				
				You can then call it from JavaScript like this:
				
				ajax_jsName(ard1, arg2, etc);
				
				Your method should return an AJAX response object. You can create one like this:
				
				$response = $DIALOG->createAjaxResponse();
				
				The response object contains methods for manipulating the dialog.
				For example, to change the innerHTML of an element you would use:
				
				$response = $DIALOG->createAjaxResponse();
				$response->addAssign("elementID", "innerHTML", "<p>Some html...</p>");
				return $response;
				
				The AJAX response object is provided by XAJAX a free BSD licensed AJAX framework
				Please see: http://www.xajaxproject.org/ for more information and documentation.
				
				*/
				
				// Example, specifying functions that can be called using Ajax:
				$DIALOG->registerAjaxFunction(array('confirmTest', &$this, 'confirmTest'));
				$DIALOG->registerAjaxFunction(array('callScript', &$this, 'callScript'));
				
				// Instead of using the template engine we could assign content directly:
				$DIALOG->bodyContent = '
<script type="text/javascript">
function myJSFunction(firstArg, numberArg, myArrayArg) {
	var newString = firstArg + " and " + (+numberArg + 100) + "\n";
	newString += myArrayArg["myKey"] + " | " + myArrayArg.key2;
	alert(newString);
	document.getElementById(\'myDiv\').innerHTML = newString;
}
</script>
<p>
<!-- Demonstrates how to call the ajax functions from JavaScript: -->
<button type="button" class="largeButton" onclick="ajax_confirmTest()">Test Ajax Confirm...</button><br /><br />
<button type="button" class="largeButton" onclick="ajax_callScript()">Test Ajax Script Call...</button><br />
<pre id="myDiv">[blank]</pre>
</p>';
				
				// sets the buttons to display at the bottom of the dialog:
				$DIALOG->options = array(
					array(
						'type'=>'button',
						'onclick'=>'dialog.close()',
						'name'=>'ok',
						'value'=>$DIALOG->langEngine->get('core', 'ok'),
					),
				);
				break;
		}
	}
	
	// ajax functions defined above...
	function confirmTest() {
		global $DIALOG;
		// create AJAX response object
		$response = $DIALOG->createAjaxResponse();
		
		// Example: adding an alert
		$response->addAlert("Here is an alert.");
		
		// Example: adding a confirm. 
		/* 
		The first param sets how may of the following ajax calls should 
		be done if the user clicks OK (or skipped if they click cancel)
		*/
		$response->addConfirmCommands(2, "Are you sure you want to show two (2) more alerts?");
		$response->addAlert("This will only happen if the user presses OK.");
		$response->addAlert("This also will only happen if the user presses OK.");
		
		$response->addAlert("This will always happen.");
		return $response;
	}
	function callScript() {
		global $DIALOG;
		$response = $DIALOG->createAjaxResponse();
		$value2 = "this is a string";
		$response->addScriptCall("myJSFunction", "arg1", 9432.12, array("myKey" => "some value", "key2" => $value2));
		return $response;
	}
	
}


?>