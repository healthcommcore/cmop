<?php

defined( '_JEXEC' ) or die('Restricted access');

class menuJombib{

function BIB_MENU() {
JToolBarHelper::editList();
JToolBarHelper::addNew();
JToolBarHelper::addNew('catNew','New Cat');
JToolBarHelper::deleteList();
JToolBarHelper::custom('allDelete','delete.png', 'delete_f2.png', 'Delete All', false);
}
function BACK_MENU() {
JToolBarHelper::back();
}
function EDIT_MENU() {
JToolBarHelper::save('saveEdit','Save');
JToolBarHelper::cancel();
}
function CONF_MENU() {
JToolBarHelper::save('confSave','Save');
JToolBarHelper::back();
}
function CATADD_MENU() {
JToolBarHelper::save('catSave','Add');
JToolBarHelper::back();
}
function CAT_MENU() {
JToolBarHelper::addNew('catNew','New Cat');
JToolBarHelper::deleteList('This will remove all references in the catagories being deleted','catDelete','Delete');
JToolBarHelper::back();
}
}

?>
