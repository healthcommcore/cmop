<?php if (!defined('IN_WPRO')) exit; 
require(WPRO_DIR.'conf/defaultValues/wproCore_fileBrowser.inc.php');
?>



<fieldset class="singleLine">
<legend><?php echo $langEngine->get('wproCore_fileBrowser', 'properties')?></legend>
<?php 

$t = $this->createUI2ColTable();
$t->width = 'small';

	$s = $this->createHTMLSelect();
	$s->attributes = array('name'=>$prefix.'widthUnits');
	$s->options = array(''=>$langEngine->get('core', 'pixels'),'%'=>$langEngine->get('core', 'percent'));
	$s->selected=$defaultValues['flashWidthUnits'];
$t->addRow($this->underlineAccessKey($langEngine->get('core', 'width'), 'w'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'name' => $prefix.'width',
	'value' => $defaultValues['flashWidth'],
)).$s->fetch(), $prefix.'width');

	$s = $this->createHTMLSelect();
	$s->attributes = array('name'=>$prefix.'heightUnits');
	$s->options = array(''=>$langEngine->get('core', 'pixels'),'%'=>$langEngine->get('core', 'percent'));
	$s->selected=$defaultValues['flashHeightUnits'];

$t->addRow($this->underlineAccessKey($langEngine->get('core', 'height'), 'h'), 
$this->HTMLInput(array(
	'type' => 'text',
	'size' => '3',
	'name' => $prefix.'height',
	'value' => $defaultValues['flashHeight'],
)).$s->fetch(), $prefix.'height');

//$t->addRow('', '<input type="checkbox" name="'.$prefix.'constrain" value="1" /> '.$langEngine->get('wproCore_fileBrowser', 'constainProportions'));

$UI = $this->createHTMLSelect();
$UI->attributes = array('name'=>$prefix.'scale','accesskey'=>'s');
$UI->selected = $defaultValues['flashScale'];
$UI->options = array(
'showall' =>$langEngine->get('core', 'showall'),
'noborder' => $langEngine->get('wproCore_fileBrowser', 'noborder'),
'exactfit' => $langEngine->get('wproCore_fileBrowser', 'exactfit'),
'noscale' => $langEngine->get('wproCore_fileBrowser', 'noscale'),
);
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'scale'), 's'), $UI->fetch(), 'scale');

$c = $this->createUIColorPicker();
$c->name = $prefix.'bgcolor';
$c->showInput = true;
$c->color = $defaultValues['flashBGColor'];
$c->accessKey = 'c';
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'backgroundColor'), 'c'), $c->fetch(), 'backgroundColor');

$UI = $this->createHTMLSelect();
$UI->attributes = array('name'=>$prefix.'wmode','accesskey'=>'w');
$UI->selected = $defaultValues['flashWMode'];
$UI->options = array(
'' =>$langEngine->get('core', 'default'),
'transparent' => $langEngine->get('wproCore_fileBrowser', 'transparent'),
);
$t->addRow($this->underlineAccessKey($langEngine->get('wproCore_fileBrowser', 'windowMode'), 'w'), $UI->fetch(), 'scale');


$t->display();

?>
</fieldset>

<!--<fieldset class="singleLine">
<legend><?php echo $langEngine->get('core', 'appearance')?></legend>
<?php
$strStyleOverrides = $EDITOR->featureIsEnabled('dialogappearanceoptions') ? $langEngine->get('core', 'styleOverrides') : '';	
$t = $this->createUI2ColTable();
$t->width = 'small';
$UI = $this->createHTMLSelect();
$UI->attributes = array('name'=>$prefix.'style','accesskey'=>'s');
$UI->options = array_merge(array(''=>$langEngine->get('core', 'default')), $EDITOR->imageStyles);
$UI->selected = $defaultValues['imageStyle'];
$t->addRow($this->underlineAccessKey($langEngine->get('core', 'style'), 's'), $UI->fetch().'<br />'.$strStyleOverrides, 'style');
$t->display();
?>
</fieldset>-->