<?php

defined( '_JEXEC' ) or die('Restricted access');

require_once( $mainframe->getPath( 'toolbar_html' ) ); 
switch ( $act ) {
	case 'view':
		switch ($task) {
		case "edit":
			menuJombib::EDIT_MENU();
			break;
		
		default:
			menuJombib::BIB_MENU();
			break;

		}
		break;

	case 'input':
		menuJombib::BACK_MENU();
		break;
	case 'categories':
		switch ($task) {
			case "catNew":
				menuJombib::CATADD_MENU();
				break;
			default:
				menuJombib::CAT_MENU();
				break;

		}
		break;

	case 'config':
		menuJombib::CONF_MENU();
		break;
		
	default:
		menuJombib::BACK_MENU();
		break;
}
?>
