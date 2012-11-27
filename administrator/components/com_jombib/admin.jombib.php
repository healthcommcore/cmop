<?php
error_reporting(E_ALL);
defined( '_JEXEC' ) or die('Restricted access');

// ensure user has access to this function
$acl =& JFactory::getACL();
    $acl->addACL( 'com_jombib', 'manage', 'users', 'super administrator' );
    /* Additional access groups */
    $acl->addACL( 'com_jombib', 'manage', 'users', 'administrator' );
    $acl->addACL( 'com_jombib', 'manage', 'users', 'manager' );
	$my = & JFactory::getUser();
if (!$my->authorize( 'com_jombib', 'manage' )) {
	$mainframe->redirect( 'index.php', JText::_('ALERTNOTAUTH') );
}

require_once JPATH_ROOT .'/includes/PEAR/PEAR.php';
require_once JPATH_ROOT .'/components/com_jombib/BibTex.php';
require_once( JApplicationHelper::getPath('admin_html') );

$id = JRequest::getVar(  'cid', array(0) );

if (!is_array( $id )) {

$id = array(0);

}

//retrieve config settings
$database = &JFactory::getDBO();
$query = "SELECT * from #__bib_config;";
$database->setQuery( $query);
$set=$database->loadRowList();
foreach($set as $row){
	$sets[$row[0]]=$row[1];
	$tips[$row[0]]=$row[2];
	$names[$row[0]]=$row[3];
}

$act = JRequest::getVar(  'act', "view" );

switch($act)
{
	case "view":
		switch($task) {
			case "remove":
				delBib( $id ,$option);
			break;
			case "allDelete":
				delAllBib($option);
			break;
			case "new":
				global $mainframe;$mainframe->redirect("index2.php?option=$option&act=input");
			break;
			case "catNew":
				global $mainframe;$mainframe->redirect("index2.php?option=$option&act=categories&task=catNew");
			break;
			case "edit":
				editBib($id ,$option);
			break;
			case "saveEdit":
				saveEditBib($option,$sets);
			break;
			case 'cancel':
				checkin($option);
				break;
			default:
				viewBib($option,$sets);
				break;
		}
		break;
	case "input":
		switch($task) {
			case "save":
				bibSave($mainframe,$option,$sets);
				break;
			default:
				bibInput($option,$sets);
				break;
		}
		break;
	case "categories":
		switch($task) {
			case "catNew":
				catInput($option);
				break;
			case "catSave":
				catSave($option);
				break;
			case "catDelete":
				delCat( $id ,$option);
			break;
			default:
				viewCat($option);
				break;
		}
		break;
	case "config":
		switch($task) {
			case "confSave":
				confSave($option);
				break;

			default:
				configInput($option,$sets,$tips,$names);
				break;
		}
		break;
	default:
		global $mainframe;$mainframe->redirect("index2.php?option=$option&act=view");
		break;
}

function checkin($option){
	$database = &JFactory::getDBO();
	$id = $_POST['id'];
	//check in
	$database->setQuery("update #__bib set checkedout='0' where authorid=(".$id.");");
		if (!$result = $database->query()) {
			echo $database->stderr();
			return false;
		}
	global $mainframe;$mainframe->redirect("index2.php?option=$option&act=view");
}

function confSave($option){
	$database = &JFactory::getDBO();
	$query = "SELECT variable from #__bib_config";
	$database->setQuery( $query);
	$configs = $database->loadResultArray();
	foreach($configs as $config){
		$configparam = JRequest::getVar(  $config, 'off' );
		$database->setQuery("update #__bib_config set value='$configparam' where variable='$config';");
		if (!$result = $database->query()) {
			$errors = $errors.$database->stderr();
		}
	}
	global $mainframe;$mainframe->redirect("index2.php?option=$option&act=view","Configuration Saved");

}

function configInput($option,$sets,$tips,$names){
	HTML_jombib::configInput($option,$sets,$tips,$names);
}

//edit reference

function saveEditBib($option,$sets){

	$database = &JFactory::getDBO();
	$catIds = $_POST['category'];
	$id = $_POST['id'];
	$authornum = $_POST['authornum'];
	$errors="";
	$bibtex = new Structures_BibTex();
	$minibibtex = new Structures_BibTex();
	//get old content from db.
	$query = "SELECT content from #__bib_content where id=(".$id.");";
	$database->setQuery( $query);
	$content=$database->loadResult();
	$minibibtex->addContent($content);
	$minibibtex->parse();
	$newdata = $minibibtex->data[0];

	$allfields = $database->getTableFields(array('#__bib','#__bib_auth'));
	$fields =array_keys($allfields['#__bib']);
	$authfields =array_keys($allfields['#__bib_auth']);
	$authfields = array_diff($authfields,array('id'));
	$authfields = array_diff($authfields,array('num'));
	$fields = array_diff($fields,array('authorid'));
	$fields = array_diff($fields,array('authorsnames'));
	$fields = array_diff($fields,array('shortauthnames'));
	$fields = array_diff($fields,array('checkedout'));
	foreach($fields as $field){
			$stringin=$_POST[$field];
			if(''!=$stringin){
				$newdata[$field]=$stringin;
			}else{
				if(array_key_exists($field,$newdata)){
					//old data needs to be deleted
					unset($newdata[$field]); 
					$database->setQuery("update #__bib set ".$field."=NULL where authorid=".$id);
					if (!$result = $database->query()) {
						$errors = $errors.$database->stderr();
					}
				}
			}
	}
	$newauthor=array();
	for($i=0;$i<$authornum;$i++){
		foreach($authfields as $authfield){
			$stringin=$_POST[$authfield.$i];
			$newdata['author'][$i][$authfield] = $stringin;
		}
		if($newdata['author'][$i]['last']!=''){
			$newauthor[]=$newdata['author'][$i];
		}
	}
	if(count($newauthor)>0){
		$newdata['author']=$newauthor;
	}
	$bibtex->addEntry($newdata);
	//check all fields allowed in mysql
	$fields[]='author';
	foreach ($newdata as $fieldsgiven => $valuesgiven) {
		//should we allow key??
		if((!in_array($fieldsgiven,$fields))||strcmp($fieldsgiven,'key')==0) {
			unset($newdata[$fieldsgiven]); 
		}
	}
	$authexists = 0;
	if(array_key_exists('author',$newdata)){
		$authexists = 1;
		$autharray = $newdata['author'];
		$newdata = array_diff($newdata,$autharray);
	}
	//prepare statement for inserting fields
	foreach($newdata as $key=>$data){
		$updates[]=$key."='".mysql_real_escape_string($data)."'";
	}
	if(count($updates)){
		$update = implode(",", array_values($updates));
		$database->setQuery("update #__bib set ".$update." where authorid=".$id);
		if (!$result = $database->query()) {
			$errors = $errors.$database->stderr();
		}
	}

	//prepare statement for author info
	if($authexists){
		$authnames = '';
		$shortauthnames = '';
		//delete old values 
			$database->setQuery("delete from #__bib_auth where id=".$id);
			if (!$result = $database->query()) {
				$errors = $errors.$database->stderr();
			}
		$authorcount=0;
		foreach ( $autharray as $author) {
			$authorcount++;
			foreach ($author as $afield => $avalues) {
				$author[$afield]=ereg_replace('[{}]','',$avalues);
			}
			$authnames =$authnames." ";
			if($authorcount==1){
				$shortauthnames =$shortauthnames." ";
			}
			if($sets['fullnames']=="on"){
				$authnames =$authnames.$author['first']." ";
				if($authorcount==1){
					$shortauthnames =$shortauthnames.$author['first']." ";
				}
			}	
			$authnames =$authnames.$author['last'];
			if($authorcount==1){
				$shortauthnames =$shortauthnames.$author['last'];
			}
			$values2 = implode("','", array_values($author));
			$keys2 = implode(",", array_keys($author));
			$database->setQuery("insert into #__bib_auth (id,num,".$keys2.") values (".$id.",".$authorcount.",'".$values2."')");
			if (!$result = $database->query()) {
				$errors = $errors.$database->stderr();
			}
		}
		if($authorcount>2){
			$shortauthnames = $shortauthnames." <i>et al.</i>";
		}else{
			$shortauthnames = $authnames;
		}
		$database->setQuery("update #__bib set authorsnames='".$authnames."' where authorid='".$id."';");
		if (!$result = $database->query()) {
			$errors = $errors.$database->stderr();
		}
		$database->setQuery("update #__bib set shortauthnames='".$shortauthnames."' where authorid='".$id."';");
		if (!$result = $database->query()) {
			$errors = $errors.$database->stderr();
		}
	}
	//sort out categories
	//delete old values 
	$database->setQuery("delete from #__bib_categories where id=".$id);
	if (!$result = $database->query()) {
		$errors = $errors.$database->stderr();
	}
	//prepare statements for inserting categoryids
	foreach ( $catIds as $catId ){
		$database->setQuery("insert into #__bib_categories (id,categories) values ('".$id."','".$catId."')");
		if (!$result = $database->query()) {
			$errors = $errors.$database->stderr();
		}
	}
	//prepare statement for inserting bibtex
	$database->setQuery("update #__bib_content set content='".mysql_real_escape_string ($bibtex->bibTex())."' where id=".$id);
	if (!$result = $database->query()) {
			$errors = $errors.$database->stderr();
	}
	//check in
	$database->setQuery("update #__bib set checkedout='0' where authorid=(".$id.");");
		if (!$result = $database->query()) {
			echo $database->stderr();
			return false;
		}
	global $mainframe;$mainframe->redirect("index2.php?option=$option&act=view", $errors."Reference Edited");
}

function editBib($id ,$option){
	$database = &JFactory::getDBO();
	$my = & JFactory::getUser();
	if($id==array(0)){
		$id[0] = JRequest::getVar(  'eid', 0 );
	}
	$allfields = $database->getTableFields(array('#__bib','#__bib_auth'));
	$fields =array_keys($allfields['#__bib']);
	$authfields =array_keys($allfields['#__bib_auth']);

	$query = "SELECT * from #__bib where authorid=(".$id[0].");";
	$database->setQuery( $query);
	$row=$database->loadRowList();
	$row=array_combine_emulated($fields,$row[0]);


		// fail if checked out not by 'me'
	if ($row['checkedout']!=$my->id&&$row['checkedout']!=0) {
		global $mainframe;$mainframe->redirect( "index2.php?option=$option", 'The module is currently being edited by another administrator.' );
	}
		//check out
	$row['checkedout']= $my->id;
	$database->setQuery("update #__bib set checkedout='".$my->id."' where authorid=(".$id[0].");");
		if (!$result = $database->query()) {
			echo $database->stderr();
			return false;
		}

	$query = "SELECT * from #__bib_auth where id=(".$id[0].") order by num;";
	$database->setQuery( $query);
	$authrows=$database->loadRowList();
	for($i=0;$i<count($authrows);$i++){
		$authrows[$i]=array_combine_emulated($authfields,$authrows[$i]);
	}
	$authornum = JRequest::getVar(  'authornum', count($authrows) );
	if($authornum>count($authrows)){
		for($i=count($authrows);$i<$authornum;$i++){
			$authrows[$i]=array_combine_emulated($authfields,array("","","","","",""));
		}
	}
	$query = "SELECT * from #__categories where section='com_jombib' order by id";
	$database->setQuery( $query);
	$catsobj = $database->loadObjectList();
	foreach($catsobj as $cat){
		$cats[$cat->id]=$cat->name;
	}
	//get category info
	$query = "SELECT categories from #__bib_categories where id=(".$id[0].");";
	$database->setQuery( $query);
	$catrows=$database->loadResultArray();

	$authfields = array_diff($authfields,array('id'));
	$authfields = array_diff($authfields,array('num'));
	$fields = array_diff($fields,array('authorid'));
	$fields = array_diff($fields,array('authorsnames'));
	$fields = array_diff($fields,array('shortauthnames'));
	$fields = array_diff($fields,array('checkedout'));

	HTML_jombib::editBib($row,$authrows,$option,$cats,$id[0],$fields,$authfields,$authornum,$catrows);
}

//delete category

function delCat( $id ,$option){
		$database = &JFactory::getDBO();
	foreach($id as $cid){
		//find all relevant references
		$query = "SELECT authorid from #__bib left join #__bib_categories on ,#__bib.authorid=#__bib_categories.id where #__bib_categories.categories=(".$cid.");";
		$database->setQuery( $query);
		$authorids = $database->loadResultArray();
		//delete values from cat table
		$database->setQuery("delete from #__bib_categories where categories=(".$cid.");");
		if (!$result = $database->query()) {
			echo $database->stderr();
			return false;
		}
		//clean up author and content data:
		foreach($authorids as $aid){
			//find all relevant references
			$query = "SELECT categories from #__bib_categories where id=(".$aid.");";
			$database->setQuery( $query);
			$catids = $database->loadResultArray();
			if(count($catids)==0){
				$database->setQuery("delete from #__bib_auth where id=(".$aid.");");
				if (!$result = $database->query()) {
					echo $database->stderr();
					return false;
				}
				$database->setQuery("delete from #__bib_content where id=(".$aid.");");
				if (!$result = $database->query()) {
					echo $database->stderr();
					return false;
				}
			}
		}
		//finally delete category
		$database->setQuery("delete from #__categories where id=(".$cid.");");
		if (!$result = $database->query()) {
			echo $database->stderr();
			return false;
		}
	}
	global $mainframe;$mainframe->redirect("index2.php?option=$option&act=categories", "Categories Deleted");
}

//save category

function catSave($option){
		$database = &JFactory::getDBO();
	$message="Category Added";
	$catName = $_POST['catName'];
	$catDesc = $_POST['catDesc'];
	$database->setQuery("insert into #__categories (name,description,params,section,published,title) values ('".$catName."','".$catDesc."','','com_jombib','1','".$catName."');");
	if (!$result = $database->query()) {
		$errors = $errors.$database->stderr();
	}
	global $mainframe;$mainframe->redirect("index2.php?option=$option&act=categories");
}

//new cat

function catInput($option){
	HTML_jombib::catNew($option);
}

//view categories
function viewCat($option){
	$database = &JFactory::getDBO();
	global  $mainframe, $mosConfig_list_limit;
	$limit 		= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart = intval( $mainframe->getUserStateFromRequest( "viewban{$option}limitstart", 'limitstart', 0 ) );

	// get the total number of records
	$query = "SELECT COUNT(*)"
	. "\n from #__categories where section='com_jombib'"
	;
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( JPATH_ROOT . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit );

	$query = "SELECT * from #__categories where section='com_jombib' order by id";
	$database->setQuery( $query);
	$rows = $database->loadObjectList();
	HTML_jombib::showCat($rows,$pageNav,$option);
}


//delete entries

function delBib($id,$option){
		$database = &JFactory::getDBO();
	foreach($id as $cid){
		$database->setQuery("delete from #__bib where authorid=(".$cid.");");
		if (!$result = $database->query()) {
			echo $database->stderr();
			return false;
		}
		$database->setQuery("delete from #__bib_auth where id=(".$cid.");");
		if (!$result = $database->query()) {
			echo $database->stderr();
			return false;
		}
		$database->setQuery("delete from #__bib_content where id=(".$cid.");");
		if (!$result = $database->query()) {
			echo $database->stderr();
			return false;
		}
		$database->setQuery("delete from #__bib_categories where id=(".$cid.");");
		if (!$result = $database->query()) {
			echo $database->stderr();
			return false;
		}
	}
	global $mainframe;$mainframe->redirect("index2.php?option=$option&act=view", "Items Deleted");
}

//delete all entries

function delAllBib($option){
		$database = &JFactory::getDBO();
		$database->setQuery("delete from #__bib;");
		if (!$result = $database->query()) {
			echo $database->stderr();
			return false;
		}
		$database->setQuery("delete from #__bib_auth;");
		if (!$result = $database->query()) {
			echo $database->stderr();
			return false;
		}
		$database->setQuery("delete from #__bib_content;");
		if (!$result = $database->query()) {
			echo $database->stderr();
			return false;
		}
		$database->setQuery("delete from #__bib_categories;");
		if (!$result = $database->query()) {
			echo $database->stderr();
			return false;
		}
	global $mainframe;$mainframe->redirect("index2.php?option=$option&act=view", "Items Deleted");
}

//view list

function viewBib($option,$sets) {
		$database = &JFactory::getDBO();
	global $mainframe, $mosConfig_list_limit;

	$limit 		= intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mosConfig_list_limit ) );
	$limitstart = intval( $mainframe->getUserStateFromRequest( "viewban{$option}limitstart", 'limitstart', 0 ) );

	// get the total number of records
	$query = "SELECT COUNT(*)"
	. "\n FROM #__bib"
	;
	$database->setQuery( $query );
	$total = $database->loadResult();

	require_once( JPATH_ROOT . '/administrator/includes/pageNavigation.php' );
	$pageNav = new mosPageNav( $total, $limitstart, $limit );

	$query = "SELECT authorid,shortauthnames,authorsnames,title,year,URL,eprint,checkedout from #__bib";
	$database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
	$rows = $database->loadObjectList();

	$query = "SELECT * from #__categories where section='com_jombib' order by id";
	$database->setQuery( $query);
	$catsobj = $database->loadObjectList();
	foreach($catsobj as $cat){
		$cats[$cat->id]=$cat->name;
	}
	HTML_jombib::showBib( $rows, $pageNav, $option ,$cats,$sets);
}

//input function

function bibInput($option,$sets){
		$database = &JFactory::getDBO();
	$query = "SELECT * from #__categories where section='com_jombib' order by id";
	$database->setQuery( $query);
	$cats = $database->loadObjectList();

	$allfields = $database->getTableFields(array('#__bib','#__bib_auth'));
	$fields =array_keys($allfields['#__bib']);
	$authfields =array_keys($allfields['#__bib_auth']);
	$authfields = array_diff($authfields,array('id'));
	$authfields = array_diff($authfields,array('num'));
	$fields = array_diff($fields,array('authorid'));
	$fields = array_diff($fields,array('authorsnames'));
	$fields = array_diff($fields,array('shortauthnames'));
	$fields = array_diff($fields,array('checkedout'));
	$inputtype = JRequest::getVar(  'inputtype', '' );
	$authornum = JRequest::getVar(  'authornumber', '' );
	HTML_jombib::inputForm($option,$cats,$fields,$authfields,$inputtype,$authornum,$sets);
}

function bibSave($mainframe,$option,$sets){

	//save function
		$database = &JFactory::getDBO();
	$message="Data Saved";
	$allfields = $database->getTableFields(array('#__bib','#__bib_auth'));
	$fields =array_keys($allfields['#__bib']);
	$authfields =array_keys($allfields['#__bib_auth']);

	$bibtex = new Structures_BibTex();

	
	$catIds = $_POST['category'];
	
	$inputtype=$_POST['inputtype'];
	
	
	//adding a bibtex string
	if($inputtype=="file"){
		$filename = $_FILES['userfile']['tmp_name'];
		$origfilename = $_FILES['userfile']['name'];
		if (strcasecmp(substr($origfilename,-4),'.bib')==0){
			$bibtex->loadFile($filename);
			$bibtex->parse();
		}else{
			$message="Not a .bib file";
		}
	}elseif($inputtype=="string"){
		$bibstring = $_POST['bib'];
		$bibstring = str_replace("\\","",$bibstring);
		$bibtex->addContent($bibstring);
		$bibtex->parse();
	}else{
		$authornum=$_POST['authornum'];
		//get fields
		foreach($fields as $field){
			$stringin=$_POST[$field];
			if(''!=$stringin){
				$newdata[$field]=$stringin;
			}
		}
		for($i=0;$i<$authornum;$i++){
			foreach($authfields as $authfield){
				$stringin=$_POST[$authfield.$i];
				if(''!=$stringin){
					$newdata['author'][$i][$authfield] = $stringin;
				}
			}
		}
		if(count($newdata)){
			$bibtex->addEntry($newdata);
		}else{
			$message="No file or text data";
		}
	}
	$errcod = savetomysql($bibtex->data,$fields,$catIds,$sets);
	global $mainframe;$mainframe->redirect("index2.php?option=$option&act=view", $message.$errcod);

}


//saves bibtex data in mysql
function savetomysql($bibarray,$fields,$catIds,$sets){
	$database = &JFactory::getDBO();
foreach ($bibarray as $paper) {
	$minibibtex = new Structures_BibTex();
	$minibibtex->data[0] = $paper;
	$authexists = 0;
	if(array_key_exists('author',$paper)){
		$authexists = 1;
		$autharray = $paper['author'];
		$paper = array_diff($paper,$autharray);
	}
	$errors = "";
	//check all fields allowed
	$unsavedfields=array();
	foreach ($paper as $fieldsgiven => $valuesgiven) {
		//should we allow key??
		if((!in_array($fieldsgiven,$fields))||strcmp($fieldsgiven,'key')==0) {
			$unsavedfields[]=$paper[$fieldsgiven];
			unset($paper[$fieldsgiven]); 
		}else{
			//sort out escape chars and remove {}
			$paper[$fieldsgiven]=ereg_replace('[{}]','',mysql_real_escape_string($valuesgiven));
		}
	}
	//search for urls elsewhere
	if(!array_key_exists('url',$paper)){
		$urlstring1=array();
		foreach($unsavedfields as $field){
			if(preg_match('!(http://|ftp://|https://)[a-z0-9_\.\/\?\&-\=]*!i',$field,$urlstring1) ){
				$paper['url']=$urlstring1[0];
			}elseif(preg_match('!(www\.)[a-z0-9_\.\/\?\&-\=]*!i',$field,$urlstring1) ){
				$paper['url']="http://".$urlstring1[0];
			}
		}
		$urlstring2=array();
		if(array_key_exists('note',$paper)){
			if(preg_match('!(http://|ftp://|https://)[a-z0-9_\.\/\?\&-\=]*!i',$paper['note'],$urlstring2) ){
				$paper['url']=$urlstring2[0];
			}elseif(preg_match('!(www\.)[a-z0-9_\.\/\?\&-\=]*!i',$paper['note'],$urlstring2) ){
				$paper['url']="http://".$urlstring2[0];
			}
		}
		if(array_key_exists('howpublished',$paper)){
			if(preg_match('!(http://|ftp://|https://)[a-z0-9_\.\/\?\&-\=]*!i',$paper['howpublished'],$urlstring2) ){
				$paper['url']=$urlstring2[0];
			}elseif(preg_match('!(www\.)[a-z0-9_\.\/\?\&-\=]*!i',$paper['howpublished'],$urlstring2) ){
				$paper['url']="http://".$urlstring2[0];
			}
		}
	}
	//sort out eprint
	if((!array_key_exists('eprint',$paper))&&array_key_exists('url',$paper)){
		$urlstring=$paper['url'];
		if(substr($urlstring, -3, 3)=="pdf"||substr($urlstring, -3, 3)=="PDF"){
			$paper['eprint']=$urlstring;
			unset($paper['url']); 
		}
	}
	//prepare statement for inserting fields
	$values = implode("','", array_values($paper));
	$keys = implode(",", array_keys($paper));
	$database->setQuery("insert into #__bib (".$keys.") values ('".$values."')");
	if (!$result = $database->query()) {
		$errors = $errors.$database->stderr();
	}
	//get the new authorId
	$database->setQuery("select authorid from #__bib order by authorid desc limit 1");
	$authid = $database->loadResult();
	//prepare statements for inserting categoryids
	foreach ( $catIds as $catId ){
		$database->setQuery("insert into #__bib_categories (id,categories) values ('".$authid."','".$catId."')");
		if (!$result = $database->query()) {
			$errors = $errors.$database->stderr();
		}
	}
	//prepare statement for author info
	if($authexists){
		$authnames = '';
		$shortauthnames = '';
		$authorcount=0;
		foreach ( $autharray as $author) {
			$authorcount++;
			foreach ($author as $afield => $avalues) {
				$author[$afield]=ereg_replace('[{}]','',mysql_real_escape_string($avalues));
			}
			$authnames =$authnames." ";
			if($authorcount==1){
				$shortauthnames =$shortauthnames." ";
			}
			if($sets['fullnames']=="on"){
				$authnames =$authnames.$author['first']." ";
				if($authorcount==1){
					$shortauthnames =$shortauthnames.$author['first']." ";
				}
			}	
			$authnames =$authnames.$author['last'];
			if($authorcount==1){
				$shortauthnames =$shortauthnames.$author['last'];
			}
			$values2 = implode("','", array_values($author));
			$keys2 = implode(",", array_keys($author));
			$database->setQuery("insert into #__bib_auth (id,num,".$keys2.") values (".$authid.",".$authorcount.",'".$values2."')");
			if (!$result = $database->query()) {
				$errors = $errors.$database->stderr();
			}
		}
		if($authorcount>2){
			$shortauthnames = $shortauthnames." <i>et al.</i>";
		}else{
			$shortauthnames = $authnames;
		}
		$database->setQuery("update #__bib set authorsnames='".$authnames."' where authorid='".$authid."';");
		if (!$result = $database->query()) {
			$errors = $errors.$database->stderr();
		}
		$database->setQuery("update #__bib set shortauthnames='".$shortauthnames."' where authorid='".$authid."';");
		if (!$result = $database->query()) {
			$errors = $errors.$database->stderr();
		}
	}
	
	//prepare statement for inserting bibtex
	$database->setQuery("insert into #__bib_content (id,content) values (".$authid.",'".mysql_real_escape_string ($minibibtex->bibTex())."')");
	if (!$result = $database->query()) {
			$errors = $errors.$database->stderr();
	}
}
return $errors;
}

function array_combine_emulated( $keys, $vals ) {
 $keys = array_values( (array) $keys );
 $vals = array_values( (array) $vals );
 $n = max( count( $keys ), count( $vals ) );
 $r = array();
 for( $i=0; $i<$n; $i++ ) {
  $r[ $keys[ $i ] ] = $vals[ $i ];
 }
 return $r;
}
?>