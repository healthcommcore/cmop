<?php
defined( '_JEXEC' ) or die('Restricted access');

class HTML_jombib{

	function configInput($option,$sets,$tips,$names){
		JURI::root()
		?>
		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
        <script language="JavaScript" src="<?php echo JURI::root();?>/includes/js/overlib_mini.js" type="text/javascript"></script> 

		<table class="adminheading">
        <tr>
           	<th class="config">Joomla Bibtex Configuration</th>
       	</tr>
        </table>
		<form action="index2.php" method="POST" name="adminForm">
		<table class="adminform">
		<tr>
        	<th colspan="4">General settings</th>
        </tr>
		<?php
			$k=0;
		foreach($sets as $variable=>$value){
			?>
				<tr>
					<td>
					<?php echo $names[$variable]?>
					</td>
					<td width="30">
					<input type="checkbox" name="<?php echo $variable?>" <?php if($value=="on"){echo "checked";}?>/>
					</td>
					<td width="30"><?php echo mosToolTip($tips[$variable]);?></td>
					<td width="75%">
					</td>
				</tr>
			<?php
				$k++;
		}
			?>
			<tr>
			<td colspan="2">
				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="task" value="confSave" />
				<input type="hidden" name="act" value="config" />
			</td>
		</tr>
		</table>
		</form>
			<?php
	}

	function editBib($row,$authrows,$option,$cats,$id,$fields,$authfields,$authornum,$catrows){
	?>
		<table class="adminheading">
		<tr>
			<th>
			Edit Reference
			</th>
		</tr>
		</table>
		<form action="index2.php" method="POST" name="adminForm">
		<table class="adminform">
		<tr>
			<th colspan="2">
			Edit
			</th>
		</tr>
		<tr>
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
					<TEXTAREA name="<?php echo $field?>" rows="5" cols="50"><?php echo $row[$field]?></TEXTAREA>
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
					echo $match;
					if($match==1){
			?>
				<option value="<?php echo $caid ?>" SELECTED><?php echo $caname ?></option>
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
				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="id" value="<?php echo $id; ?>" />
				<input type="hidden" name="task" value="saveEdit" />
				<input type="hidden" name="act" value="view" />
				<input type="hidden" name="authornum" value="<?php echo $authornum; ?>" />
			</td>
		</tr>
		</table>
		</form>
		<form action="index2.php" method="POST" name="adminForm2">
		<table class="adminform">
		<tr>
			<td colspan="2">
				<input type="Submit" name="Add" value="Add Author"/>
				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="eid" value="<?php echo $id; ?>" />
				<input type="hidden" name="task" value="edit" />
				<input type="hidden" name="act" value="view" />
				<input type="hidden" name="authornum" value="<?php echo $authornum + 1; ?>" />
			</td>
		</tr>
		</table>
		</form>
		<?php
	}


	function showBib( &$rows, &$pageNav, $option ,$cats,$sets) {
		global $my;

		mosCommonHTML::loadOverlib();
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th>
			Bibliography Manager
			</th>
		</tr>
		</table>

		<table class="adminlist">
		<tr>
			<th width="20">
			#
			</th>
			<th width="20">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
			</th>
			<th width="20%" align="left" nowrap="nowrap">
			Authors
			</th>
			<th align="left" nowrap="nowrap">
			Title
			</th>
			<th width="15%" align="left" nowrap="nowrap">
			Year
			</th>
			<th width="25%" align="left" nowrap="nowrap">
			URLs
			</th>
		</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i];
			$row->checked_out = $row->checkedout;
			$row->editor = $row->authorid;
			$row->checked_out_time = '';
			//mosMakeHtmlSafe($row);
			$row->id 	= $row->authorid;
			$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
			//etal check
			$authstring = $row->authorsnames;
			if($sets['etal']=="on"){
				$authstring = $row->shortauthnames;
			}
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center">
				<?php echo $pageNav->rowNumber( $i ); ?>
				</td>
				<td align="center">
				<?php echo $checked; ?>
				</td>
				<td align="left">
					<?php echo $authstring; ?>
				</td>
				<td align="left">
					<a href="index2.php?option=com_jombib&act=view&task=edit&eid=<?php echo $row->id; ?>" title="Edit Reference">
					<?php echo $row->title; ?>
					</a>
				</td>
				<td align="left">
					<?php echo $row->year; ?>
				</td>
				<td align="left">
					<?php echo $row->URL; ?>
					<?php if($row->URL!=""){echo "<br />";}?>
					<?php echo $row->eprint; ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>

		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="act" value="view" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<?php
	}

	function showCat( $rows,$pageNav, $option ) {
		global $my;

		mosCommonHTML::loadOverlib();
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th>
			Bibliography Category Manager
			</th>
		</tr>
		</table>

		<table class="adminlist">
		<tr>
			<th width="20">
			#
			</th>
			<th width="20">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
			</th>
			<th width="20%" align="left" nowrap="nowrap">
			Name
			</th>
			<th width="80%" align="left">
			Description
			</th>
		</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i];
			$row->checked_out = 0;
			mosMakeHtmlSafe($row);
			$row->id;
			$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="left">
				<?php echo $pageNav->rowNumber( $i ); ?>
				</td>
				<td align="left">
				<?php echo $checked; ?>
				</td>
				<td align="left">
					<?php echo $row->name; ?>
				</td>
				<td align="left">
					<?php echo $row->description; ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>
		<?php echo $pageNav->getListFooter(); ?>

		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="act" value="categories" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<?php
	}

	function inputForm($option,$cats,$fields,$authfields,$inputtype,$authornum,$sets) {
		?>
		<table class="adminheading">
		<tr>
			<th>
			Bibtex input:
			</th>
		</tr>
		</table>
		<?php
		if($inputtype==""){
			?>
		<form action="index2.php" method="POST" name="adminForm">
		<table class="adminform">
		<tr>
			<th colspan="2">
			Input
			</th>
		</tr>
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
				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="act" value="input" />
			</td>
		</tr>
		</table>
		<?php
		}else{
		?>
		<form action="index2.php" enctype="multipart/form-data" method="POST" name="adminForm">
		<table class="adminform">
		<tr>
			<th colspan="2">
			Input
			</th>
		</tr>
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
			<TEXTAREA name="bib" rows="5" cols="80"></TEXTAREA>
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
				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="act" value="input" />
				<input type="hidden" name="inputtype" value="<?php echo $inputtype; ?>" />
			</td>
			</form>
			<?php
			}else{
				?>
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
					<TEXTAREA name="<?php echo $field?>" rows="5" cols="50"></TEXTAREA>
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
				<input type="Submit" name="Submit" value="Upload"/>
				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="task" value="save" />
				<input type="hidden" name="act" value="input" />
				<input type="hidden" name="authornum" value="<?php echo $authornum; ?>" />
				<input type="hidden" name="inputtype" value="<?php echo $inputtype; ?>" />
			</td>
		</tr>
		</table>
		</form>
		<?php
		}
		}
	}

	//cat new input form

	function catNew($option){
		?>
		<table class="adminheading">
		<tr>
			<th>
			New Category:
			</th>
		</tr>
		</table>
		<form action="index2.php" method="POST" name="adminForm">
		<table class="adminform">
		<tr>
			<th colspan="2">
			Category
			</th>
		</tr>
		<tr>
			<td width="20%">
			Name:
			</td>
			<td width="80%">
			<input type="text" name="catName">
			</td>
		</tr>
		<tr>
			<td width="20%">
			Description:
			</td>
			<td>
			<TEXTAREA name="catDesc" rows="5" cols="80"></TEXTAREA>
			</td>
		</tr>
		<tr>
			<td width="80%">
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="catSave" />
			<input type="hidden" name="act" value="categories" />
			</td>
		</tr>
		</table>
		</form>
		<?php
	}
}
?>
