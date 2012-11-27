
/*
 * WysiwygPro 3.0.3.20080303 (c) Copyright Chris Bolt and ViziMetrics Inc. All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */

function wproFilePlugin_images () {
this.doUpdate = false;
this.populateLocalOptions = function (data, prefix) {
var form = document.dialogForm;
form.elements[prefix+'width'].value = data['width'];
form.elements[prefix+'height'].value = data['height'];
form.elements[prefix+'widthUnits'].value = '';
form.elements[prefix+'heightUnits'].value = '';
}
this._getOptions = function (prefix) {
var form = document.dialogForm;
var o = {};
var s = '';
if (form.elements[prefix+'width']) {
o['width'] = form.elements[prefix+'width'].value+form.elements[prefix+'widthUnits'].value;
}
if (form.elements[prefix+'height']) {
o['height'] = form.elements[prefix+'height'].value+form.elements[prefix+'heightUnits'].value;
}
if (form.elements[prefix+'border']) {
if (currentEditor.strict) {
if (form.elements[prefix+'border'].value!='') {
if (form.elements[prefix+'border'].value=='0') {
s += 'border-width:'+form.elements[prefix+'border'].value+'px;';
} else {
s += 'border:'+form.elements[prefix+'border'].value+'px solid #000000;';
}
}
} else {
o['border'] = form.elements[prefix+'border'].value;
}
}
if (form.elements[prefix+'screenTip']) {
o['title'] = form.elements[prefix+'screenTip'].value;
o['alt'] = form.elements[prefix+'screenTip'].value;
}
if (form.elements[prefix+'align']) {
if (currentEditor.strict) {
switch (form.elements[prefix+'align'].value) {
case 'top': case 'middle': case 'bottom':
s += 'vertical-align:'+form.elements[prefix+'align'].value+';';
break;
case 'left': case 'right':
s += 'float:'+form.elements[prefix+'align'].value+';';
break;
}
} else {
o['align'] = form.elements[prefix+'align'].value;
}
}
if (form.elements[prefix+'mtop']) {
var pxReg = /^([0-9 ]+)$/;
if (form.elements[prefix+'mtop'].value!='') {
if (pxReg.test(form.elements[prefix+'mtop'].value)) {
s += 'margin-top: '+form.elements[prefix+'mtop'].value+'px;';
} else {
s += 'margin-top: '+form.elements[prefix+'mtop'].value+';';
}
}
if (form.elements[prefix+'mbottom'].value!='') {
if (pxReg.test(form.elements[prefix+'mbottom'].value)) {
s += 'margin-bottom: '+form.elements[prefix+'mbottom'].value+'px;';
} else {
s += 'margin-bottom: '+form.elements[prefix+'mbottom'].value+';';
}
}
if (form.elements[prefix+'mright'].value!='') {
if (pxReg.test(form.elements[prefix+'mright'].value)) {
s += 'margin-right: '+form.elements[prefix+'mright'].value+'px;';
} else {
s += 'margin-right: '+form.elements[prefix+'mright'].value+';';
}
}
if (form.elements[prefix+'mleft'].value!='') {
if (pxReg.test(form.elements[prefix+'mleft'].value)) {
s += 'margin-left: '+form.elements[prefix+'mleft'].value+'px;';
} else {
s += 'margin-left: '+form.elements[prefix+'mleft'].value+';';
}
}
}
if (s) o['style'] = s;
return o;
}
this.onMediaPreview = function (prefix) {
this.updatePreview(prefix);
}
this.changePreviewSrc = function (prefix, dimensions) {
var form = document.dialogForm;
var imagepreview = document.getElementById(prefix+'imagepreview');
imagepreview.src = dialog.appendBaseToURL(form.URL.value);
this.remotePrefix = prefix;
if (!dimensions) {
imagepreview.onload = this.resetDimensionsTimeout
}
}
this.updatePreview = function (prefix) {
var form = document.dialogForm;
var imagepreview = document.getElementById(prefix+'imagepreview');
var o = this._getOptions(prefix);
var o2 = FB.getCommonMediaOptions();
for (x in o2) {
if (x=='style'&&o[x]) {
o[x] = o[x] + ';' + o2[x];
} else if (x=='class'&&o[x]) {
o[x] = o[x] + ' ' + o2[x];
} else {
o[x] = o2[x];
}
}
for (var a in o) {
if (o=='class') {
imagepreview.className = o[a];
} else if (a=='style') {
imagepreview.style.cssText = o[a];
} else {
imagepreview.setAttribute(a, o[a]);
}
}
var style = ''
if (form.elements['mediastyle']) {
style = form.elements['mediastyle'].value
currentEditor.applyStyle(style, [imagepreview]);
}
dialog.focus();
}
this.resetDimensionsTimeout = function () {
setTimeout("FB.embedPlugins['images'].resetDimensions()", 200);
}
this.resetDimensions = function() {
var prefix = this.remotePrefix;
var imagepreview = document.getElementById(prefix+'imagepreview');
var form = document.dialogForm
if (imagepreview) {
imagepreview.removeAttribute('width')
imagepreview.removeAttribute('height')
if (imagepreview.width&&imagepreview.height) {
form.elements[prefix+'width'].value = imagepreview.width
form.elements[prefix+'height'].value = imagepreview.height
form.elements[prefix+'widthUnits'].value = '';
form.elements[prefix+'heightUnits'].value = '';
if (document.getElementById(prefix+'constrain').checked) {
FB.setConstrain(prefix+'width', prefix+'height');
}
} else {
setTimeout("FB.embedPlugins['images'].resetDimensions()", 200);
}
} else {
setTimeout("FB.embedPlugins['images'].resetDimensions()", 200);
}
}
this.insertLocal = function(prefix) {
if (!document.dialogForm.URL.value) return;
var form = document.dialogForm;
var o = this._getOptions(prefix);
var s = '';
if (form.elements[prefix+'style']) {
s = form.elements[prefix+'style'].value
}
currentEditor.insertImage(form.URL.value, o, s);
}
this.onArriveRemote = function (prefix) {
document.dialogForm.URL.onchange = function() { FB.embedPlugins["images"].changePreviewSrc(prefix); }
document.getElementById('previewButton').style.display = '';
}
this.onLeaveRemote = function (prefix) {
document.dialogForm.URL.onchange = function() { }
document.getElementById('previewButton').style.display = 'none';
}
this.insertRemote = function (prefix) {
if (!document.dialogForm.URL.value) return;
var form = document.dialogForm;
var o = this._getOptions(prefix);
var o2 = FB.getCommonMediaOptions();
for (x in o2) {
if (x=='style'&&o[x]) {
o[x] = o[x] + ';' + o2[x];
} else if (x=='class'&&o[x]) {
o[x] = o[x] + ' ' + o2[x];
} else {
o[x] = o2[x];
}
}
var s = '';
if (form.elements['mediastyle']) {
s = form.elements['mediastyle'].value
}
currentEditor.insertImage(form.URL.value, o, s);
}
this.canPopulate = function () {
var range = currentEditor.selAPI.getRange();
if (range.type=='control') {
if (range.nodes[0].tagName=='IMG' && !range.nodes[0].className.match(/wproFilePlugin/i)) {
return true;
}
}
return false
}

this.populateProperties = function (prefix) {
var range = currentEditor.selAPI.getRange();
var img = range.nodes[0]
var form = document.dialogForm;
this.doUpdate = true;
if (form.elements[prefix+'width']) {
form.elements[prefix+'width'].value = String(img.getAttribute('width')).replace(/[^0-9]/g, '');
if (String(img.getAttribute('width')).match('%')) {
form.elements[prefix+'widthUnits'].value = '%';
} else {
form.elements[prefix+'widthUnits'].value = '';
}
}
if (form.elements[prefix+'height']) {
form.elements[prefix+'height'].value = String(img.getAttribute('height')).replace(/[^0-9]/g, '');
if (String(img.getAttribute('height')).match('%')) {
form.elements[prefix+'heightUnits'].value = '%';
} else {
form.elements[prefix+'heightUnits'].value = '';
}
}
FB.setConstrain(prefix+'width', prefix+'height');
if (img.getAttribute('_wpro_src')) {
var url = dialog.urlFormatting(img.getAttribute('_wpro_src'));
} else {
var url = dialog.urlFormatting(img.getAttribute('src', 2));
}
form.URL.value = url;
this.changePreviewSrc(prefix, true);
this.updatePreview(prefix);
}
}