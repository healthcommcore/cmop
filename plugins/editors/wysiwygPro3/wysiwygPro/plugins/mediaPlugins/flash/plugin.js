
/*
 * WysiwygPro 3.0.3.20080303 (c) Copyright Chris Bolt and ViziMetrics Inc. All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */

function wproFilePlugin_flash () {
this.version = '6';
this.populateLocalOptions = function (data, prefix) {
var form = document.dialogForm;
if (data['width']) {
form.elements[prefix+'width'].value = data['width'];
form.elements[prefix+'widthUnits'].value = '';
}
if (data['height']) {
form.elements[prefix+'height'].value = data['height'];
form.elements[prefix+'heightUnits'].value = '';
}
if (data['version']) {
this.version = data['version'];
}
}
this._getOptions = function (prefix, o) {
var form = document.dialogForm;
if (!o) o = {}
if (!o['object']) o['object'] = {};
if (!o['embed']) o['embed'] = {};
if (!o['param']) o['param'] = {};
if (form.elements[prefix+'width']) {
o['embed']['width'] = form.elements[prefix+'width'].value+form.elements[prefix+'widthUnits'].value;
o['object']['width'] = form.elements[prefix+'width'].value+form.elements[prefix+'widthUnits'].value;
}
if (form.elements[prefix+'height']) {
o['object']['height'] = form.elements[prefix+'height'].value+form.elements[prefix+'heightUnits'].value;
o['embed']['height'] = form.elements[prefix+'height'].value+form.elements[prefix+'heightUnits'].value;
}
if (form.elements[prefix+'scale']) {
o['param']['scale'] = form.elements[prefix+'scale'].value;
o['embed']['scale'] = form.elements[prefix+'scale'].value;
}
if (form.elements[prefix+'bgcolor']) {
o['param']['bgcolor'] = form.elements[prefix+'bgcolor'].value;
o['embed']['bgcolor'] = form.elements[prefix+'bgcolor'].value;
}
if (form.elements[prefix+'wmode']) {
o['param']['wmode'] = form.elements[prefix+'wmode'].value;
o['embed']['wmode'] = form.elements[prefix+'wmode'].value;
}
return o;
}
this.insertLocal = function(prefix, data) {
if (!document.dialogForm.URL.value) return;
if (!data) data = {};
var form = document.dialogForm;
var o = this._getOptions(prefix, data);
o['object']['classid']="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" ;
o['object']['codebase']="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version="+this.version+",0,0,0";
o['param']['movie'] =form.URL.value;
o['param']['quality'] = 'high';
o['embed']['quality'] = 'high';
o['embed']['src'] = form.URL.value;
o['embed']['pluginspage'] = "http://www.macromedia.com/go/getflashplayer";
o['embed']['type'] = "application/x-shockwave-flash";
var s = '';
if (form.elements[prefix+'style']) {
s = form.elements[prefix+'style'].value
}
FB.insertMedia('flash', o, s);
}
this.insertRemote = function (prefix) {
var data
if (FB.propertiesPlugin == 'flash' && FB.mediaProperties) {
data = FB.mediaProperties;
}
this.insertLocal(prefix, data);
}
this.canPopulate = function () {
var arr = FB.getMediaProperties();
if (arr['object']) {
if (arr['object']['classid']) {
if (arr['object']['classid'].toUpperCase() == "CLSID:D27CDB6E-AE6D-11CF-96B8-444553540000") {
return true;
}
}
}
return false
}
this.populateProperties = function (prefix) {
var form = document.dialogForm;
var o = FB.getMediaProperties();
if (form.elements[prefix+'width']&&o['object']['width']) {
form.elements[prefix+'width'].value = String(o['object']['width']).replace(/[^0-9]/g, '');
if (String(o['object']['width']).match('%')) {
form.elements[prefix+'widthUnits'].value = '%';
} else {
form.elements[prefix+'widthUnits'].value = '';
}
}
if (form.elements[prefix+'height']&&o['object']['height']) {
form.elements[prefix+'height'].value = String(o['object']['height']).replace(/[^0-9]/g, '');
if (String(o['object']['height']).match('%')) {
form.elements[prefix+'heightUnits'].value = '%';
} else {
form.elements[prefix+'heightUnits'].value = '';
}
}
if (form.elements[prefix+'scale']&&o['param']['scale']) {
form.elements[prefix+'scale'].value = o['param']['scale'];
}
if (form.elements[prefix+'bgcolor']&&o['param']['bgcolor']) {
form.elements[prefix+'bgcolor'].setColor( o['param']['bgcolor'] );
}
if (form.elements[prefix+'wmode']&&o['param']['wmode']) {
form.elements[prefix+'wmode'].value = o['param']['wmode'];
}
if (o['param']['movie']) {
form.URL.value = dialog.urlFormatting(o['param']['movie']);
}
return o;
}
}