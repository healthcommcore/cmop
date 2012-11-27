<?php
defined( '_JEXEC' ) or die('Restricted access');
require_once JPATH_ROOT .'/includes/PEAR/PEAR.php';
require_once 'BibTex.php';


/**
* @package Joomla bibtex
*/
class HTML_jombib {

	
	function displayBibDownload(){
		?>
		<table class="contentpaneopen">
		<tr>
			<td class="contentheading" width="100%">
				.bib File Download
			</td>
		</tr>
		</table>
		<table>
			<tr>
			<td>
			<a href='<?php echo JURI::root()."/components/com_jombib/download.bib";?>'>Download File</a>(Right-click and "Save Target As..")
			</td>
			</tr>
			</table>
		<?php
	}

	function inputForm($cats,$fields,$authfields,$inputtype,$authornum,$sets,$catId){
		require_once( JPATH_ROOT . '/includes/HTML_toolbar.php' );
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				form.task.value="";
			}

				form.submit();
		}
		</script>
		<?php
		if($inputtype==""){
			?>
		<form action="index.php?option=com_jombib" method="post" name="adminForm" id="adminForm">
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td class="contentheading">
			Add Reference
			</td>
		</tr>
		</table>
		<table class="adminform">
		<tr>
			<td width="20%">
			Input Method:
			</td>
			<td width="80%">
			<select name="inputtype">
				<option value="file">Bibtex File</option>
				<option value="string">Paste Bibtex String</option>
		<?php
		if($sets['manualinput']=="on"){
		?>
				<option value="fields">Manually by Fields</option>
		<?php
			}
		?>
			</select>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="Submit" name="Submit" value="Select"/>
				<input type="hidden" name="task" value="add" />
			</td>
		</tr>
		</table>
		</form>
		<?php
		}else{
		?>
		<form action="index.php?option=com_jombib" enctype="multipart/form-data" method="POST" name="adminForm">
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td class="contentheading">
			Add Reference
			</td>
			<td width="10%">
			<?php
			mosToolBar::startTable();
			mosToolBar::spacer();
			mosToolBar::save();
			mosToolBar::cancel();
			mosToolBar::endtable();
			?>
			</td>
		</tr>
		</table>
		<table class="adminform">
		<?php
		if($inputtype=="file"){
		?>
		<tr>
			<td width="20%">
			Bibtex File:
			</td>
			<td width="80%">
			<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
			<input class="inputbox" name="userfile" type="file" />
			</td>
		</tr>
		<?php
		}elseif($inputtype=="string"){
		?>
		<tr>
			<td>
			Bibtex String
			</td>
			<td align="left">
			<TEXTAREA name="bib" rows="5" cols="60"></TEXTAREA>
			</td>
		</tr>
		<?php
		}elseif($inputtype=="fields"){
		?>
		<tr>
			<?php
			if($authornum==""){
			?>
			<td>
			Number of authors
			</td>
			<td align="left">
				<select name="authornumber">
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
				<option value="6">6</option>
				<option value="7">7</option>
				<option value="8">8</option>
				<option value="9">9</option>
				</select>
				<input type="Submit" name="Submit" value="Select"/>
				<input type="hidden" name="task" value="add" />
				<input type="hidden" name="inputtype" value="<?php echo $inputtype; ?>" />
			</td>
			</form>
			<?php
			}else{
				?>
			<td>
			Input Fields
			</td>
			<td align="left">
			<table>
			<?php
				$k=0;
				foreach($fields as $field){
					if($field!="abstract"){
						if($k==0){echo "<tr>";}
			?>
				<td>
				<?php echo $field?>
				</td>
				<td>
				<input type="text" name="<?php echo $field?>"/>
				</td>
			<?php
						if($k==1){echo "</tr>";}
						$k=1-$k;
					}else{
			?>
					<tr>
						<td>
						<?php echo $field?>
						</td>
						<td colspan="3">
							<TEXTAREA name="<?php echo $field?>" rows="5" cols="42"></TEXTAREA>
						</td>
					</tr>
			<?php
					}
				}
				for($i=0;$i<(int)$authornum;$i++){
					?>
				<tr>
					<td>Author No. <?php echo $i+1 ?></td>
				</tr>
				<?php
					foreach($authfields as $authfield){
				?>
				<tr>
					<td>
					<?php echo $authfield?>
					</td>
					<td>
					<input type="text" name="<?php echo $authfield.$i?>"/>
					</td>
				</tr>
				<?php
					}
				}
			}
			?>
			<tr>
			</tr>
			</table>
			</td>
		</tr>
		<?php
		}
		if($authornum!=""||$inputtype=="file"||$inputtype=="string"){
		?>
		<tr>
			<td>
			Category
			</td>
			<td>
			<select name="category[]" multiple>
			<?php
				for ($i=0, $n=count( $cats ); $i < $n; $i++) {
				$cat = &$cats[$i];
				if($i==0){
			?>
				<option value="<?php echo $cat->id ?>" SELECTED><?php echo $cat->name ?></option>
			<?php
				}else{
				?>
				<option value="<?php echo $cat->id ?>"><?php echo $cat->name ?></option>
			<?php
				}
				}
			?>
			</select>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="hidden" name="task" value="save" />
				<input type="hidden" name="authornum" value="<?php echo $authornum; ?>" />
				<input type="hidden" name="inputtype" value="<?php echo $inputtype; ?>" />
				<input type="hidden" name="catid" value="<?php echo $catId; ?>" />
			</td>
		</tr>
		</table>
		</form>
		<?php
		}
		}
	}
	function keyExistsOrIsNotEmpty($key,$array){
		if(array_key_exists($key,$array)){
			if($array[$key]!=""){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	function formatReference($row,$authrows,$link="none"){
		//authors:
		$authstring = "";
		if (HTML_jombib::keyExistsOrIsNotEmpty('authorsnames',$row)){
			for($i=0;$i<count($authrows);$i++){
				if($i!=0){
					if($i==count($authrows)-1){
						$authstring = $authstring." and ";
					}else{
						$authstring = $authstring.", ";
					}
				}
				if(HTML_jombib::keyExistsOrIsNotEmpty('von',$authrows[$i])){
					$authstring = $authstring." ".$authrows[$i]['von'];
				}
				if(HTML_jombib::keyExistsOrIsNotEmpty('last',$authrows[$i])){
					$authstring = $authstring." ".$authrows[$i]['last'];
				}
				if(HTML_jombib::keyExistsOrIsNotEmpty('jr',$authrows[$i])){
					$authstring = $authstring." ".$authrows[$i]['jr'];
				}
				if(HTML_jombib::keyExistsOrIsNotEmpty('first',$authrows[$i])){
					$authstring = $authstring.", ".$authrows[$i]['first'];
				}
			}
		}elseif (HTML_jombib::keyExistsOrIsNotEmpty('editor',$row)){
			$authstring = $row['editor'];
		}
		echo $authstring;
		if (HTML_jombib::keyExistsOrIsNotEmpty('year',$row)){
			echo " (".$row['year'].")";
		}
		if (HTML_jombib::keyExistsOrIsNotEmpty('title',$row)){
			if($link=="none"){
				echo ", \"".$row['title']."\"";
			}else{
				echo ", \"<a href='$link' title='View Reference Details'>".$row['title']."</a>\"";
			}
		}
		if (HTML_jombib::keyExistsOrIsNotEmpty('journal',$row)){
			echo ", <i>".$row['journal']."</i>";
		}elseif (HTML_jombib::keyExistsOrIsNotEmpty('booktitle',$row)){
			echo ", <i>".$row['booktitle']."</i>";
		}
		if (HTML_jombib::keyExistsOrIsNotEmpty('chapter',$row)){
			echo ", ".$row['chapter'];
		}
		if (HTML_jombib::keyExistsOrIsNotEmpty('series',$row)){
			echo ", ".$row['series'];
		}
		if (HTML_jombib::keyExistsOrIsNotEmpty('volume',$row)){
			echo ", <b>".$row['volume']."</b>";
		}
		if (HTML_jombib::keyExistsOrIsNotEmpty('number',$row)){
			echo ", <b>".$row['number']."</b>";
		}
		if (HTML_jombib::keyExistsOrIsNotEmpty('pages',$row)){
			echo ": ".$row['pages'];
		}
		if (HTML_jombib::keyExistsOrIsNotEmpty('organization',$row)){
			echo ", ".$row['organization'];
		}
		echo ".";
	}


	function viewBib($row,$authrows,$cats,$id,$fields,$authfields,$authornum,$sets,$catId,$return){
		$my = & JFactory::getUser();
		?>
		<form action="index.php?option=com_jombib" enctype="multipart/form-data" method="POST" name="adminForm">
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td class="contentheading">
			Reference Details
			<?php
			if ($my->gid > 0 && $sets['edit']=="on") {
			?>
				<a href="index.php?option=com_jombib&amp;task=editbib&amp;catid=$catId&amp;id=<?php echo $id?>" title="Edit reference"><img src="<?php echo JURI::root().'/images/M_images/edit.png'?>" alt="Edit reference" align="middle" border="0" /></a>
		<?php
		}
		?>
			</td>
		</tr>
		</table>
		<input type="hidden" name="task" value="editbib" />
		<input type="hidden" name="id" value="$id" />
		</form>
		<table class="adminform">
		<tr>
		<td>
		<?php
		HTML_jombib::formatReference($row,$authrows);
		if ($row['abstract']!=""){
         echo "<br />";
         echo "<br />";
         echo "<b>Abstract:</b>";
         echo "<br />";
         echo $row['abstract'];
      }   
      if ($row['keywords']!=""){
         echo "<br />";
         echo "<br />";
         echo "<b>Keywords:</b>";
         echo "<br />";
         echo $row['keywords'];
      }   
      if ($row['note']!=""){
         echo "<br />";
         echo "<br />";
         echo "<b>Notes:</b>";
         echo "<br />";
         echo $row['note'];
      }
      if ($row['annote']!=""){
         echo "<br />";
         echo "<br />";
         echo "<b>Annotations:</b>";
         echo "<br />";
         echo $row['annote'];
      }
		if ($row['url']!=""){
		?>
		<br />
		<br />
		<a href="<?php echo $row['url']?>" title="Webpage Link">
						<img src="<?php echo JURI::root().'/images/M_images/weblink.png'?>" alt="Webpage Link" width="12" height="12" align="middle" border="0" />&nbsp Webpage Link
					</a>
		<?php
		}
		if ($row['eprint']!=""){
		?>
		<br />
		<br />
		<a href="<?php echo $row['eprint']?>" title="Electronic Paper Link">
						<img src="<?php echo JURI::root().'/images/M_images/pdf_button.png'?>" alt="Electronic Paper Link" width="12" height="12" align="middle" border="0" />&nbsp Electronic Paper Link
					</a>
		<?php
		}
		?>
		</td>
		</tr>
		<tr>
		<td>
		<div class="back_button">
				<a href="<?php echo $return?>">
					[ Back ]</a>
			</div>
		</td>
		</tr>
		</table>
		<?php
	}

	function editBib($row,$authrows,$cats,$id,$fields,$authfields,$authornum,$catId,$catrows){
		require_once( JPATH_ROOT . '/includes/HTML_toolbar.php' );
	?>
				<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				form.task.value="cancel";
			}

			// do field validation
				form.submit();
			
		}
		</script>

		<form action="index.php?option=com_jombib" method="post" name="adminForm" id="adminForm">
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td class="contentheading">
			Edit Reference
			</td>
			<td width="10%">
			<?php
			mosToolBar::startTable();
			mosToolBar::spacer();
			mosToolBar::save();
			mosToolBar::cancel();
			mosToolBar::endtable();
			?>
			</td>
		</tr>
		</table>
		<table class="adminform">
		<td valign="top">
			Input Fields
			</td>
			<td align="left">
			<table>
			<?php
				$k=0;
				foreach($fields as $field){
					if($field!="abstract"){
					if($k==0){echo "<tr>";}
			?>
				<td>
				<?php echo $field?>
				</td>
				<td>
				<input type="text" name="<?php echo $field?>" value="<?php echo $row[$field]?>"/>
				</td>
			<?php
					if($k==1){echo "</tr>";}
					$k=1-$k;
					}else{
			?>
			<tr>
				<td>
				<?php echo $field?>
				</td>
				<td colspan="3">
					<TEXTAREA name="<?php echo $field?>" rows="5" cols="42"><?php echo $row[$field]?></TEXTAREA>
				</td>
			</tr>
			<?php
					}
				}
				for($i=0;$i<count($authrows);$i++){
					?>
				<tr>
					<td>Author No. <?php echo $i+1 ?></td>
				</tr>
				<?php
					foreach($authfields as $authfield){
				?>
				<tr>
					<td>
					<?php echo $authfield?>
					</td>
					<td>
					<input type="text" name="<?php echo $authfield.$i?>" value="<?php echo $authrows[$i][$authfield]?>"/>
					</td>
				</tr>
				<?php
					}
				}
			?>
			<tr>
			</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td>
			Category
			</td>
			<td>
			<select name="category[]" multiple>

			<?php 
				foreach ($cats as $caid=>$caname){
					$match=0;
					foreach ($catrows as $catrow){
						if($caid==$catrow){
							$match=1;
						}
					}
					if($match==1){
			?>
				<option value="<?php echo $caid ?>" selected><?php echo $caname ?></option>
			<?php
					}else{
			?>
				<option value="<?php echo $caid ?>"><?php echo $caname ?></option>
			<?php
					}
				}
			?>
			</select>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="hidden" name="eid" value="<?php echo $id; ?>" />
				<input type="hidden" name="task" value="update" />
				<input type="hidden" name="catid" value="<?php echo $catId; ?>" />
				<input type="hidden" name="authornum" value="<?php echo $authornum; ?>" />
			</td>
		</tr>
		</table>
		</form>
		<form action="index.php?option=com_jombib" method="POST" name="adminForm2">
		<table class="adminform">
		<tr>
			<td colspan="2">
				<input type="Submit" name="Add" value="Add Author"/>
				<input type="hidden" name="id" value="<?php echo $id; ?>" />
				<input type="hidden" name="task" value="editbib" />
				<input type="hidden" name="catid" value="<?php echo $catId; ?>" />
				<input type="hidden" name="authornum" value="<?php echo $authornum + 1; ?>" />
			</td>
		</tr>
		</table>
		</form>
		<?php
	}


	function displaylist( $bibtex ,$lists,$pageNav,$catName,$sets,$catId) {
		$data = $bibtex->data;
		global $Itemid, $hide_js;
		?>
			<div class="componentheading">
			Bibliography<?php
			if($catName!=""){
				echo " - ".$catName;
			}
			?>
			</div>
			<?php

			HTML_jombib::showTable($data,$lists,$pageNav,$sets,$catId);
	}
	

	/**
	* Display Table of items
	*/
	function showTable($data ,$lists,$pageNav,$sets,$catId) {
		$my = & JFactory::getUser();
		$link = "index.php?option=com_jombib";
		?>

		<form method="post" name="adminForm">
				<table>
					<tr>
							<td>
								Author
								<input type="text" name="afilter" value="<?php echo $lists['afilter'];?>" class="inputbox" onchange="document.adminForm.submit();" />
							</td>
							<td>
								Title
								<input type="text" name="filter" value="<?php echo $lists['filter'];?>" class="inputbox" onchange="document.adminForm.submit();" />
							</td>
							<td>
								<input type="submit" value="Filter"/>
							</td>
							<td>
								<?php
								echo '&nbsp;&nbsp;&nbsp;Order&nbsp;';
								echo $lists['order'];
								?>
							</td>
							<td nowrap="nowrap">
								<?php
								$order = '';
								if ( $lists['order_value'] ) {
									$order = '&amp;order='. $lists['order_value'];
								}
								$filter = '';
								if ( $lists['filter'] ) {
									$filter = '&amp;filter='. $lists['filter'];
								}
								$afilter = '';
								if ( $lists['afilter'] ) {
									$afilter = '&amp;afilter='. $lists['afilter'];
								}

								$limitlink = "index.php?option=com_jombib&amp;catid=$catId". $order . $filter . $afilter;

								echo '&nbsp;&nbsp;&nbsp;Display #&nbsp;';
								echo $pageNav->getLimitBox( $limitlink );
								?>
							</td>
					</tr>
				</table>
		<table width="100%" border="0" cellpadding='0' <?php if($sets['formatted']=="off"){echo "cellspacing='0'";}else{echo "cellspacing='10'";}?>>
			<?php
				//if option selected
				if($sets['topbuttons']=="on"){
			?>
			<tr>
						<td align="center" colspan="6" class="sectiontablefooter">
						<?php
						$order = '';
						if ( $lists['order_value'] ) {
							$order = '&amp;order='. $lists['order_value'];
						}
						$filter = '';
						if ( $lists['filter'] ) {
							$filter = '&amp;filter='. $lists['filter'];
						}
						$afilter = '';
						if ( $lists['afilter'] ) {
							$afilter = '&amp;afilter='. $lists['afilter'];
						}

						$link = "index.php?option=com_jombib&amp;catid=$catId". $order . $filter . $afilter;
						echo $pageNav->writePagesLinks( $link );
						?>
						</td>
					</tr>
					<tr>
						<td colspan="6" align="right">
						<?php echo $pageNav->writePagesCounter(); ?>
						</td>
					</tr>
			<?php
					}
						//if we want to show a full table:
						if($sets['formatted']=="off"){
			?>
			<tr>
					<td class="sectiontableheader" align="left" width="10%">
						<?php echo "Authors"; ?>
					</td>
					<td class="sectiontableheader" align="left" width="35%">
						<?php echo "Title"; ?>
					</td>
					<td class="sectiontableheader" align="left" width="15%">
						<?php echo "Journal"; ?>
					</td>
					<td align="center" align="left" class="sectiontableheader" width="5%">
						<?php echo "Year"; ?>
					</td>
					<td align="center" align="left" class="sectiontableheader" width="5%">
						<?php echo "Type"; ?>
					</td>
					<td align="center" align="left" class="sectiontableheader" width="1%">
						<?php echo "Links"; ?>
					</td>
			</tr>
			<?php
						}else{
			?>
			<tr>
					<td colspan="6" class="sectiontableheader" width="10%">
						<?php echo "References"; ?>
					</td>
			</tr>
			<?php
			}

		$k = 0;
		foreach ( $data as $row ) {
			//if we want to show a full table:
			if($sets['formatted']=="off"){
				?>
				<tr class="sectiontableentry<?php echo ($k+1)?>" >
					<td>
					<?php 
						if (array_key_exists('authorsnames', $row)){
							$authstring = $row['authorsnames'];
							if($sets['etal']=="on"){
								$authstring = $row['shortauthnames'];
							}
							?>
							<?php echo HTML_jombib::truncate($authstring,12,$sets) ?>
							<?php 
					}elseif (array_key_exists('editor', $row)){
							?>
							<?php echo HTML_jombib::truncate($row['editor'],12,$sets) ?>
							<?php 
					}else{
						echo "--";
						}
						?>
					</td>
					<td>
					<?php 
						if (array_key_exists('title', $row)){
						?>
						<a href="index.php?option=com_jombib&amp;task=showbib&amp;id=<?php echo $row['authorid']?>&amp;return=<?php echo urlencode($limitlink)?>" title="View Details">
							<?php echo HTML_jombib::truncate($row['title'],45,$sets); ?>
						</a>
						<?php 
						}else{
							?>
						<a href="index.php?option=com_jombib&amp;task=showbib&amp;id=<?php echo $row['authorid']?>&amp;return=<?php echo urlencode($limitlink)?>" title="View Details">
							--
						</a>
						<?php
						}
						?>
					</td>
					<td align="left">
					<?php 
						if (array_key_exists('journal', $row)){
						?>
						<?php echo HTML_jombib::truncate($row['journal'],20,$sets); ?>
						<?php 
					}elseif (array_key_exists('booktitle', $row)){
						?>
						<?php echo HTML_jombib::truncate($row['booktitle'],20,$sets); ?>
						<?php 
					}elseif (array_key_exists('number', $row)){
						?>
						<?php echo HTML_jombib::truncate($row['number'],20,$sets); ?>
						<?php 
					}elseif (array_key_exists('institution', $row)){
						?>
						<?php echo HTML_jombib::truncate($row['institution'],20,$sets); ?>
						<?php 
					}elseif (array_key_exists('series', $row)){
						?>
						<?php echo HTML_jombib::truncate($row['series'],20,$sets); ?>
						<?php 
					}elseif (array_key_exists('publisher', $row)){
						?>
						<?php echo HTML_jombib::truncate($row['publisher'],20,$sets); ?>
						<?php 
					}else{
						echo "--";
						}
						?>
					</td>
					<td align="left">
					<?php 
						if (array_key_exists('year', $row)){
						?>
						<?php echo $row['year']; ?>
						<?php 
					}else{
						echo "--";
						}
						?>
					</td>
					<td align="center">
					<?php 
						if (array_key_exists('type', $row)){
						?>
						<?php echo $row['type']; ?>
						<?php 
					}else{
						echo "--";
						}
						?>
					</td>
					<td align="center" >
						<?php
						if (array_key_exists('url', $row)){
						?>
						<a href="<?php echo $row['url']?>">
							<img src="<?php echo JURI::root().'/images/M_images/weblink.png'?>" alt="Webpage Link" name="Webpage Link" width="<?php if($sets['smallicons']=="on"){echo "8";}else{echo "12";}?>" height="<?php if($sets['smallicons']=="on"){echo "8";}else{echo "12";}?>" align="middle" border="0" />
						</a>
						<?php 
						}
						?>
						<?php
						if (array_key_exists('eprint', $row)){
						?>
						<a href="<?php echo $row['eprint']?>">
							<img src="<?php echo JURI::root().'/images/M_images/pdf_button.png'?>" alt="Electronic Paper Link" name="Electronic Paper Link" width="<?php if($sets['smallicons']=="on"){echo "8";}else{echo "12";}?>" height="<?php if($sets['smallicons']=="on"){echo "8";}else{echo "12";}?>" align="middle" border="0" />
						</a>
						<?php 
						}
						?>	
					</td>
				</tr>


			<?php
			}else{
				if(!array_key_exists('author', $row)){
					$row['author']=array();
				}
				?>
				<tr class="sectiontableentry<?php echo ($k+1)?>" >
					<td colspan="6">
						<?php echo HTML_jombib::formatReference($row,$row['author'],"index.php?option=com_jombib&amp;task=showbib&amp;id=".$row['authorid']);?>
						
					</td>
				</tr>
			<?php
			}
				$k = 1 - $k;
			?>

			<?php
		}
		?>
					<tr>
				<td colspan="6">&nbsp;</td>
			</tr>
			<tr>
				<td align="center" colspan="6" class="sectiontablefooter">
				<?php
				$order = '';
				if ( $lists['order_value'] ) {
					$order = '&amp;order='. $lists['order_value'];
				}
				$filter = '';
				if ( $lists['filter'] ) {
					$filter = '&amp;filter='. $lists['filter'];
				}
				$afilter = '';
				if ( $lists['afilter'] ) {
					$afilter = '&amp;afilter='. $lists['afilter'];
				}

				$link = "index.php?option=com_jombib&amp;catid=$catId". $order . $filter . $afilter;
				echo $pageNav->writePagesLinks( $link );
				?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="right">
				<?php echo $pageNav->writePagesCounter(); ?>
				</td>
			</tr>
			<tr>
			<td width="80%" colspan="3"  align="left">
				<?php
					if ($sets['download']=="on") {
				?>
				<a href="index.php?option=com_jombib&amp;task=showallbib&amp;filter=<?php echo $lists['filter']?>&amp;afilter=<?php echo $lists['afilter']?>&amp;catid=<?php echo $catId?>&amp;order=<?php echo $lists['order_value']?>">Download bibtex string for all <?php echo $pageNav->total ?> results</a>
				<?php
				}
				?>
				</td>
			<?php
					if ($my->gid > 0 && $sets['add']=="on") {
			?>
			<td width="20%" colspan="3" align="right">
				<a href="index.php?option=com_jombib&amp;task=add" title="Add new reference"><img src="<?php echo JURI::root().'/images/M_images/new.png'?>" alt="Add" align="middle" border="0" />Add..</a>
				</td>

			<?php
				}
			?>
			</tr>
		</table>
		<input type="hidden" name="option" value="com_jombib" />
		<input type="hidden" name="catid" value="$catId" />
		</form>
		<?php
	}

	function truncate($stringin,$n,$sets){
		//change this with options
		if($sets['truncate']=="on"){
			$stringout=substr($stringin,0,$n);
			if(strlen($stringin)>$n){
				$stringout=$stringout."...";
			}
		}else{
			$stringout=$stringin;
		}
		return $stringout;
	}

}
?>
