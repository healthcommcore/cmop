<?php


defined( '_JEXEC' ) or die('Restricted access');
error_reporting(E_ALL);

require_once JPATH_ROOT .'/includes/PEAR/PEAR.php';
require_once 'BibTex.php';
require_once( JApplicationHelper::getPath('front_html') );
$limit 		= intval( JRequest::getVar( 'limit' , 25) );
$limitstart = intval( JRequest::getVar( 'limitstart' , 0 ) );
//$limit = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
//$limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );
$aid = JRequest::getVar( 'id', 0 );


//get category id from menu params
jimport('joomla.application.menu');
$Itemid = intval( JRequest::getVar( 'Itemid' , 0) );
    $menu = & JMenu::getInstance('site');
    $params = & $menu->getParams( $Itemid );

$catId = intval( JRequest::getVar( 'catid' , 0) );
if($catId==0){
	$catId = $params->get( "catid", 0);
}


	$database = &JFactory::getDBO();

//retrieve config settings
$query = "SELECT * from #__bib_config;";
$database->setQuery( $query);
$set=$database->loadRowList();
foreach($set as $row){
	$sets[$row[0]]=$row[1];
}


switch ($task) {

	case 'showbib':
		$return 		= JRequest::getVar( 'return', "index.php?option=com_jombib" );
		showBib($aid,$sets,$catId,$return);

		break;

	case 'editbib':
	$my = & JFactory::getUser();
		if(! ($acl->acl_check('administration','edit','users',$my->usertype, 'components', 'all' ) | $acl->acl_check('administration','edit','users',$my->usertype, 'components', 'com_jombib' ))) {
		$mainframe->redirect( 'index.php' , _NOT_AUTH );
		}
		editBib($aid,$sets,$catId);

		break;

	case 'add':
	$my = & JFactory::getUser();
		if(! ($acl->acl_check('administration','edit','users',$my->usertype, 'components', 'all' ) | $acl->acl_check('administration','edit','users',$my->usertype, 'components', 'com_jombib' ))) {
		$mainframe->redirect( 'index.php' , _NOT_AUTH );
		}

		addBib($sets,$catId);

		break;

	case 'update':
	$my = & JFactory::getUser();
		if(! ($acl->acl_check('administration','edit','users',$my->usertype, 'components', 'all' ) | $acl->acl_check('administration','edit','users',$my->usertype, 'components', 'com_jombib' ))) {
		$mainframe->redirect( 'index.php' , _NOT_AUTH );
		}
		updateBib($sets,$catId);

		break;

	case 'save':
	$my = & JFactory::getUser();
		if(! ($acl->acl_check('administration','edit','users',$my->usertype, 'components', 'all' ) | $acl->acl_check('administration','edit','users',$my->usertype, 'components', 'com_jombib' ))) {
		$mainframe->redirect( 'index.php' , _NOT_AUTH );
		}
		saveBib($sets,$catId);

		break;

	case 'cancel':
		checkin($catId);

		break;

	case 'showallbib':
		echo $selected 	= strval( JRequest::getVar( 'order', '' ) );
	//$selected= $mainframe->getUserStateFromRequest( 'order','order','','string');
		echo $filter 	= stripslashes( strval( JRequest::getVar( 'filter', '' ) ) );
		echo $afilter 	= stripslashes( strval( JRequest::getVar( 'afilter', '' ) ) );
		//$filter= $mainframe->getUserStateFromRequest( 'filter','filter','','string');
		//$afilter= $mainframe->getUserStateFromRequest( 'afilter','afilter','','string');
		showAllBib($database,$mainframe,$selected, $filter,$afilter,$limit,$limitstart,$catId);

		break;

	default:
		echo $selected 	= strval( JRequest::getVar( 'order', '' ) );
	//$selected= $mainframe->getUserStateFromRequest( 'order','order','','string');
		echo $filter 	= stripslashes( strval( JRequest::getVar( 'filter', '' ) ) );
		echo $afilter 	= stripslashes( strval( JRequest::getVar( 'afilter', '' ) ) );
		//$filter= $mainframe->getUserStateFromRequest( 'filter','filter','','string');
		//$afilter= $mainframe->getUserStateFromRequest( 'afilter','afilter','','string');
		loadData($database,$mainframe,$selected, $filter,$afilter,$limit,$limitstart,$catId,$sets);
		break;
}

function checkin($catId){
		$database = &JFactory::getDBO();
	$id = $_POST['eid'];
	//check in
	$database->setQuery("update #__bib set checkedout='0' where authorid=(".$id.");");
		if (!$result = $database->query()) {
			echo $database->stderr();
			return false;
		}
	$mainframe->redirect("index.php?option=com_jombib&amp;task=showbib&amp;id=$id&amp;catid=$catId");
}
//wip
function addBib($sets,$catId){
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

	HTML_jombib::inputForm($cats,$fields,$authfields,$inputtype,$authornum,$sets,$catId);
}

//wip
function saveBib($sets,$catId){

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
	$mainframe->redirect("index.php?option=com_jombib&amp;catid=%catId", $message.$errcod);
}
//wip
function updateBib($sets,$catId){

	$database = &JFactory::getDBO();
	$id = $_POST['eid'];
	$newCatIds = $_POST['category'];
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
		}else{
			//sort out escape chars
			$newdata[$fieldsgiven]=$valuesgiven;
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
	//sort out categories
	//delete old values 
	$database->setQuery("delete from #__bib_categories where id=".$id);
	if (!$result = $database->query()) {
		$errors = $errors.$database->stderr();
	}
	foreach ( $newCatIds as $catId ){
		$database->setQuery("insert into #__bib_categories (id,categories) values ('".$id."','".$catId."')");
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
			//add new ones
			$database->setQuery("insert into #__bib_auth (id,num,".$keys2.") values (".$id.",".$authorcount.",'".$values2."')");
			if (!$result = $database->query()) {
				$errors = $errors.$database->stderr();
			}
		}
		if($authorcount>2){
			$shortauthnames = $shortauthnames." et.al";
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

	//prepare statement for inserting bibtex
	$database->setQuery("update #__bib_content set content='".mysql_real_escape_string($bibtex->bibTex())."' where id=".$id);
	if (!$result = $database->query()) {
			$errors = $errors.$database->stderr();
	}
	//check in
	$database->setQuery("update #__bib set checkedout='0' where authorid=(".$id.");");
		if (!$result = $database->query()) {
			echo $database->stderr();
			return false;
		}
	$mainframe->redirect("index.php?option=com_jombib&task=showbib&id=$id&catid=$catId", $errors."Reference Edited");
}

function showAllBib($database,$mainframe,$selected, $filter,$afilter,$limit,$limitstart,$catId){
	$database = &JFactory::getDBO();
	if ( $selected ) {
		$orderby 				= $selected;
	} else {
		$orderby 				= 'rdate';
	}
	$orderby = _orderby_sec( $orderby );
	if($catId>0){
		$andfilter="where #__bib_categories.categories='".$catId."'";
	}else{
		$andfilter="where 1>0";
	}
	if($afilter!=""){
		$andfilter=$andfilter." AND LOWER(authorsnames) LIKE '%".$afilter."%'";
	}
	if($filter!=""){
		$andfilter=$andfilter." AND LOWER(title) LIKE '%".$filter."%'";
	}
	if((strcmp($orderby,'#__bib_auth.last')==0)||(strcmp($orderby,'#__bib_auth.last DESC')==0)){
		$orderby="authorsnames";
	}
	$database->setQuery("SELECT authorid from #__bib left join #__bib_categories on #__bib.authorid=#__bib_categories.id ".$andfilter." order by ".$orderby);
	$result = $database->loadResultArray();
	$fp = fopen("components/com_jombib/download.bib", "w") or die("can't open file"); 
	foreach($result as $contid){
		$database->setQuery("SELECT content from #__bib_content where id='".$contid."';");
		$bibstringall=$database->loadResult();
		fwrite($fp, $bibstringall);
	}
	fclose($fp);
	HTML_jombib::displayBibDownload();
}

function showBib($aid,$sets,$catId,$return){
	$database = &JFactory::getDBO();
	$my = & JFactory::getUser();

		$allfields = $database->getTableFields(array('#__bib','#__bib_auth'));
		$fields =array_keys($allfields['#__bib']);
		$authfields =array_keys($allfields['#__bib_auth']);

		$query = "SELECT * from #__bib where authorid=(".$aid.");";
		$database->setQuery( $query);
		$row=$database->loadRowList();
		$row=array_combine_emulated($fields,$row[0]);

		$query = "SELECT * from #__bib_auth where id=(".$aid.") order by num;";
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
		$authfields = array_diff($authfields,array('id'));
		$authfields = array_diff($authfields,array('num'));
		$fields = array_diff($fields,array('authorid'));
		$fields = array_diff($fields,array('authorsnames'));
		$fields = array_diff($fields,array('shortauthnames'));
		$fields = array_diff($fields,array('checkedout'));
		HTML_jombib::viewBib($row,$authrows,$cats,$aid,$fields,$authfields,$authornum,$sets,$catId,$return);
		return;
}

function editBib($aid,$sets,$catId){
	$database = &JFactory::getDBO();
	$my = & JFactory::getUser();

		$allfields = $database->getTableFields(array('#__bib','#__bib_auth'));
		$fields =array_keys($allfields['#__bib']);
		$authfields =array_keys($allfields['#__bib_auth']);

		$query = "SELECT * from #__bib where authorid=(".$aid.");";
		$database->setQuery( $query);
		$row=$database->loadRowList();
		$row=array_combine_emulated($fields,$row[0]);

					// fail if checked out not by 'me'
		if ($row['checkedout']!=$my->id&&$row['checkedout']!=0) {
			$mainframe->redirect( "index.php?option=com_jombib&catid=$catId", 'The module is currently being edited by another administrator.' );
		}
			//check out
		$row['checkedout']= $my->id;
		$database->setQuery("update #__bib set checkedout='".$my->id."' where authorid=(".$aid.");");
		if (!$result = $database->query()) {
			echo $database->stderr();
			return false;
		}

		$query = "SELECT * from #__bib_auth where id=(".$aid.") order by num;";
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
		$query = "SELECT categories from #__bib_categories where id=(".$aid.");";
		$database->setQuery( $query);
		$catrows=$database->loadResultArray();
		$authfields = array_diff($authfields,array('id'));
		$authfields = array_diff($authfields,array('num'));
		$fields = array_diff($fields,array('authorid'));
		$fields = array_diff($fields,array('authorsnames'));
		$fields = array_diff($fields,array('shortauthnames'));
		$fields = array_diff($fields,array('checkedout'));
		HTML_jombib::editBib($row,$authrows,$cats,$aid,$fields,$authfields,$authornum,$catId,$catrows);
		return;
}

function loadData($database,$mainframe,$selected, $filter,$afilter,$limit,$limitstart,$catId,$sets){
	global $Itemid, $mosConfig_list_limit;
//get names of fields

$allfields = $database->getTableFields(array('#__bib','#__bib_auth'));
$fields =array_keys($allfields['#__bib']);
$authfields =array_keys($allfields['#__bib_auth']);

//get total amount
if($catId>0){
	$andfilter="where #__bib_categories.categories='".$catId."'";
}else{
	$andfilter="where 1>0";
}
if($afilter!=""){
	$andfilter=$andfilter." AND LOWER(authorsnames) LIKE '%".$afilter."%'";
}
if($filter!=""){
	$andfilter=$andfilter." AND LOWER(title) LIKE '%".$filter."%'";
}
if($catId>0){
	$database->setQuery("SELECT COUNT(authorid) from #__bib left join #__bib_categories on #__bib.authorid=#__bib_categories.id ".$andfilter);
}else{
	$database->setQuery("SELECT COUNT(authorid) from #__bib ".$andfilter);
}

$total = $database->loadResult();

//get category name

$database->setQuery("SELECT name from #__categories where id='".$catId."';");
$catName = $database->loadResult();

//Printing the result

	$lists['order_value'] = '';
	if ( $selected ) {
		$orderby 				= $selected;
		$lists['order_value'] 	= $selected;
		//if ordering by author, extend the total number to take into account repeated references
		if((strcmp($orderby,'author')==0)||(strcmp($orderby,'rauthor')==0)){
			$database->setQuery("SELECT COUNT(#__bib.authorid) from #__bib left join #__bib_auth on #__bib.authorid=#__bib_auth.id left join jos_bib_categories on jos_bib.authorid=jos_bib_categories.id ".$andfilter);
			$total = $database->loadResult();
		}
	} else {
		$orderby 				= 'rdate';
		$selected 				= $orderby;
	}
	
	require_once( JPATH_ROOT . '/includes/pageNavigation.php' );
	$limitstart = $limitstart ? $limitstart : 0;
	$pageNav = new mosPageNav( $total, $limitstart, $limit );

	// Ordering control
	$orderby = _orderby_sec( $orderby );

	$order[] = mosHTML::makeOption( 'ryear', 'Year desc' );
	$order[] = mosHTML::makeOption( 'year', 'Year asc' );
	$order[] = mosHTML::makeOption( 'title', 'Title desc' );
	$order[] = mosHTML::makeOption( 'rtitle', 'Title asc' );
	$order[] = mosHTML::makeOption( 'author', 'Author asc' );
	$order[] = mosHTML::makeOption( 'rauthor', 'Author desc' );
	$order[] = mosHTML::makeOption( 'journal', 'Journal asc' );
	$order[] = mosHTML::makeOption( 'rjournal', 'Journal desc' );
	$order[] = mosHTML::makeOption( 'type', 'Type' );
	$lists['order'] = mosHTML::selectList( $order, 'order', 'class="inputbox" size="1"  onchange="document.adminForm.submit();"', 'value', 'text', $selected );

	//$lists['task'] 			= 'category';
	$lists['filter'] 		= $filter;
	$lists['afilter']		= $afilter;

	$bibtex = new Structures_BibTex();
	$bibtex->data = retrievefrommysql($fields,$authfields,$database,$limit,$limitstart,$orderby,$filter,$afilter,$catId);

	HTML_jombib::displaylist( $bibtex ,$lists,$pageNav,$catName,$sets,$catId);

}



function retrievefrommysql($fields,$authfields,$database,$limit,$limitstart,$orderby,$filter,$afilter,$catId){
// get bibtex data from db
//extract all from db
//get total amount
if($catId>0){
	$andfilter="where #__bib_categories.categories='".$catId."'";
}else{
	$andfilter="where 1>0";
}
if($afilter!=""){
	$andfilter=$andfilter." AND LOWER(authorsnames) LIKE '%".$afilter."%'";
}
if($filter!=""){
	$andfilter=$andfilter." AND LOWER(title) LIKE '%".$filter."%'";
}
if($catId>0){
	if((strcmp($orderby,'#__bib_auth.last')==0)||(strcmp($orderby,'#__bib_auth.last DESC')==0)){
		$database->setQuery("select #__bib.*,#__bib_auth.last from #__bib left join #__bib_auth on #__bib.authorid=#__bib_auth.id left join jos_bib_categories on jos_bib.authorid=jos_bib_categories.id ".$andfilter." order by ".$orderby, $limitstart, $limit );
		$fields[] = 'last';
	}else{
		$database->setQuery("SELECT #__bib.* from #__bib left join #__bib_categories on #__bib.authorid=#__bib_categories.id ".$andfilter." order by ".$orderby, $limitstart, $limit );
	}
}else{
	if((strcmp($orderby,'#__bib_auth.last')==0)||(strcmp($orderby,'#__bib_auth.last DESC')==0)){
		$database->setQuery("select #__bib.*,#__bib_auth.last from #__bib left join #__bib_auth on #__bib.authorid=#__bib_auth.id ".$andfilter." order by ".$orderby, $limitstart, $limit );
		$fields[] = 'last';
	}else{
		$database->setQuery("SELECT * from #__bib ".$andfilter." order by ".$orderby, $limitstart, $limit );
	}
}
$result = $database->loadRowList();
$newdata = array();//new bibtex array data
$index=0;
//for each row in db
foreach($result as $row) {
	//for each field
	foreach ($row as $resfield => $resvalue){
		//if authorid field
		if (strcmp($fields[$resfield],'authorid')==0) {
			//extact author data
			$newdata[$index]['authorid']=$resvalue;
			$database->setQuery("SELECT * from #__bib_auth where id=".$resvalue." order by num;");
			$authresult = $database->loadRowList();	
			$authindex=0;
			foreach($authresult as $authrow) {
				foreach ($authrow as $authresfield => $authresvalue){
					//dont include id field
					if (strcmp($authfields[$authresfield],'id')!=0){
						//create array data
						$newdata[$index]['author'][$authindex][$authfields[$authresfield]] = $authresvalue;
					}
				}
				$authindex++;
			}
		}else {
			//create array data
			if(!is_null($resvalue)){
				$newdata[$index][$fields[$resfield]] = $resvalue;
			}
		}
	}
	if((strcmp($orderby,'#__bib_auth.last')==0)||(strcmp($orderby,'#__bib_auth.last DESC')==0)){
		$newdata[$index]['authorsnames'] = $newdata[$index]['last'];
		$newdata[$index]['shortauthnames'] = $newdata[$index]['last'];
	}
	$index++;
}
return $newdata;
}

function _orderby_sec( $orderby ) {
	switch ( $orderby ) {
		case 'year':
			$orderby = 'year';
			break;

		case 'ryear':
			$orderby = 'year DESC';
			break;

		case 'title':
			$orderby = 'title';
			break;

		case 'rtitle':
			$orderby = 'title DESC';
			break;

		case 'journal':
			$orderby = 'journal';
			break;

		case 'rjournal':
			$orderby = 'journal DESC';
			break;

		case 'author':
			$orderby = '#__bib_auth.last';
			break;

		case 'rauthor':
			$orderby = '#__bib_auth.last DESC';
			break;

		case 'type':
			$orderby = 'type';
			break;


		default:
			$orderby = 'year DESC';
			break;
	}

	return $orderby;
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
	$database->setQuery("insert into #__bib_content (id,content) values (".$authid.",'".mysql_real_escape_string($minibibtex->bibTex())."')");
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
