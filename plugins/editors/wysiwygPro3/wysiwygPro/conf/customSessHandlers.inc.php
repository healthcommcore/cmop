<?php
if (!defined('IN_WPRO')) exit();
if (WPRO_SESSION_ENGINE=='PHP'&&!isset($_SESSION)) {
	
	/*
	* WysiwygPro custom session handler setup file.
	* If your application uses custom session handlers (http://www.php.net/manual/en/function.session-set-save-handler.php) 
	* then include your session handler functions into this file.
	* 
	* Or if your session requires a specific name you will need to set it here.
	*
	* If you want to add your application's user authentication routine to WysiwygPro then it should be added to this file.
	*
	* SIMPLIFIED EXAMPLE:
	
	// include custom session handler functions:
	include_once('mySessionHandlers.php');
	session_set_save_handler("myOpen", "myClose", "myRead", "myWrite", "myDestroy", "myGC");
	
	// start the session with a specific name if required:
	session_start('SessionName');
	
	*/
	
	if (isset($_GET['wproPHPSessName'])) {
		session_name($_GET['wproPHPSessName']);
	}
	session_start();
	
}
?>