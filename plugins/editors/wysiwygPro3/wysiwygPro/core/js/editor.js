
/*
 * WysiwygPro 3.0.3.20080303 (c) Copyright Chris Bolt and ViziMetrics Inc. All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */

var wp_current_obj = null;
function wp_getEditorTextareaByName(name) {
var editors = document.getElementsByTagName("TEXTAREA")
for (var i=0; i<editors.length; i++) {
if (editors[i].className == "wproHTML" && editors[i].id == name) {
return editors[i];
}
}
}
function wproObj () {
this.editors = [];
this.currentColorPicker = null;
this.currentEditor=null;
this.debugMode = true;
this.inline_tags = /^(a|abbr|acronym|b|bdo|big|br|cite|code|dfn|em|font|i|img|kbd|label|nobr|q|s|samp|select|small|span|strike|strong|sub|sup|textarea|tt|u|var)$/i;
this.supported_blocks = /^(h1|h2|h3|h4|h5|h6|p|div|address|pre)$/i;
}
wproObj.prototype._setBrowserTypeStrings = wp_setBrowserTypeStrings;
wproObj.prototype.callCommand = function (doc, command, gui, value) {
if (value==undefined) {
value = null;
}
if (gui==undefined) {
gui = false;
}
if (!doc || !command) {
return false;
}
if (command.toLowerCase() == "formatblock" && WPro.isIE && value.match(/^[a-z0-9]*$/i)) {
value = '<'+value+'>';
}
if (!WPro.isIE&&(command.toLowerCase()!='usecss'&&command.toLowerCase()!='stylewithcss'&&command.toLowerCase()!='hilitecolor')) {
eval('doc.e'+'xec'+'Com'+'ma'+'nd("usecss", false, true)');
eval('doc.e'+'xec'+'Com'+'ma'+'nd("styleWithCss", false, false)');
}
eval('doc.e'+'xec'+'Com'+'ma'+'nd(command, gui, value)');
}
wproObj.prototype._setFormSubmitFunction = function(form) {
if (form.submit&&!form.wproOldFormSubmit) {
form.wproOldFormSubmit = form.submit
form.submit = wproNewFormSubmit;
}
}
function wproNewFormSubmit () {
WPro._updateAll('prepareSubmission');
this.wproOldFormSubmit();
}
wproObj.prototype.editorLink = function(url) {
return wproEditorLink(url, this.URL, this.route);
}
wproObj.prototype.preventDefault = wproPreventDefault;
wproObj.prototype.eval = function (str) {
return eval(str);
}
wproObj.prototype.urlEncode = wproUrlEncode;
wproObj.prototype.urlDecode = wproUrlDecode;
wproObj.prototype.addSlashes = wproAddSlashes
wproObj.prototype.htmlSpecialChars = wproHtmlSpecialChars
wproObj.prototype.htmlSpecialCharsDecode =  wproHtmlSpecialCharsDecode;
wproObj.prototype.setInnerHTML = function (node, str) {
if (str.match(/^\s*<script/i)) {
var add = '';
if (WPro.isIE) {
add = '<span>&nbsp;</span>';
} else {
add = '<span></span>';
}
str = add+str;
node.innerHTML = str;
if (WPro.isIE) {
node.removeChild(node.firstChild);
}
} else {
node.innerHTML = str;
}
}
wproObj.prototype.addAttributes = function (node, attributes, oldNode, ignore_unique) {
var l = attributes.length
for (var j = 0; j < l; j++) {
if (attributes[j].nodeName=='id' && ignore_unique) {
continue;
} else if (attributes[j].specified && attributes[j].nodeName!='class' && attributes[j].nodeName!='style') {
node.setAttribute(attributes[j].nodeName, attributes[j].nodeValue, 0)
} else if (attributes[j].nodeName=='class') {
node.className = attributes[j].nodeValue
} else if (attributes[j].nodeName=='style') {
if (oldNode) {
var cssText = oldNode.style.cssText.toString();
} else {
var cssText = attributes[j].nodeValue.toString();
}
cssText = cssText.replace(/url\([\s\S]*?\)/gi, function(x){return '[WP'+escape(x)+'WP]';});
cssText = cssText.replace(/"[\s\S]*?"/g, function(x){return '[WP'+escape(x)+'WP]';});
cssText = cssText.replace(/'[\s\S]*?'/g, function(x){return '[WP'+escape(x)+'WP]';});
var styles = cssText.match(/([A-Za-z\-]*:[^;]*)/gi);
if (styles) {
var n = styles.length;
var arr = {};
for (var i=0; i<n; i++) {
s = styles[i].split(/\s*:\s*/);
if (s[0] && s[1]) {
s[0] = s[0].toLowerCase().replace(/\-[a-z]/gi, function (x) {return x.toUpperCase().replace(/-/, ''); } );
if (s[0]=='float') s[0] = 'cssFloat';
s[1] = s[1].replace(/;\s*$/, '');
arr[s[0]] = s[1];
}
}
var n = arr.length;
for ( x in arr) {
arr[x] = arr[x].replace(/\[WP[\s\S]*?WP\]/g, function(x){return unescape(x).replace(/\[WP/g, '').replace(/WP\]/g, '');});
eval("node.style."+x+"='"+this.addSlashes(arr[x])+"'");
}
}
}
}
}
wproObj.prototype.stripAttributes = function (node, keep, kill) {
var attributes = node.attributes;
var list = [];
var n = attributes.length
for (var i = 0; i < n; i++) {
if (attributes[i].specified) {
if (keep) {
if (keep.test(attributes[i].nodeName)) {
continue;
}
}
if (kill) {
if (kill.test(attributes[i].nodeName)) {
list.push(attributes[i].nodeName);
}
} else {
list.push(attributes[i].nodeName);
}
}
}
if (kill) {
if (kill.test('style')) {
node.style.cssText = '';
}
if (kill.test('class')) {
node.className = '';
}
} else {
if (keep) {
if (!keep.test('style')) {
node.style.cssText = '';
}
if (!keep.test('class')) {
node.className = '';
}
} else {
node.style.cssText = '';
node.className = '';
}
}
var n = list.length
for (var i = 0; i < n; i++) {
node.removeAttribute(list[i]);
}
}
wproObj.prototype.getNodeAttributesString = wproGetNodeAttributesString
wproObj.prototype.getNodeText = function (node) {
if (this.isIE) {
var str = node.innerText;
} else {
var str = node.innerHTML.replace(/<(br|div|p)>/gi, "\n").replace(/<[^>]+>/g,"");
str = WPro.htmlSpecialCharsDecode(str);
}
return str;
}
wproObj.prototype.quoteMeta = wproQuoteMeta
wproObj.prototype.getBlockParent = function (node) {
if (node.tagName) {
if (!WPro.inline_tags.test(node.tagName)) {
return node;
}
}
while (node.parentNode && (node.nodeType != 1 || WPro.inline_tags.test(node.tagName))) {
node = node.parentNode;
}
return node
}
wproObj.prototype.getParentNodeByTagName = function (node, tag) {
tag = tag.toUpperCase();
if (node.nodeType != 1) {
node = node.parentNode
}
while(node.tagName!=tag && node.tagName!="BODY" && node.parentNode) {
node = node.parentNode
}
if (node.tagName == tag) {
return node;
} else {
return false;
}
}
wproObj.prototype.getParent = function (tag) {
var thisTag = tag
while(thisTag.nodeType != 1) {
if (!thisTag.parentNode) {
break;
}
thisTag = thisTag.parentNode
}
if (thisTag.tagName) {
return thisTag
} else {
return false
}
}
wproObj.prototype.getElementPosition = function (elem) {
var offsetLeft = 0;
var offsetTop = 0;
if (elem.offsetParent) {
while (elem.offsetParent) {
offsetLeft += elem.offsetLeft
offsetTop += elem.offsetTop
elem = elem.offsetParent;
}
}
offsetLeft += document.getElementsByTagName('body').item(0).offsetLeft;
offsetTop += document.getElementsByTagName('body').item(0).offsetTop;
return {left:offsetLeft, top:offsetTop};
}
wproObj.prototype.getComputedStyle = function (doc, element, cssRule) {
var value = false;
if (element.tagName) {
if( document.defaultView && document.defaultView.getComputedStyle ) {
var value = doc.defaultView.getComputedStyle( element, '' ).getPropertyValue(cssRule.replace(/[A-Z]/g, function(x){return "-" + x.toLowerCase();}));
} else if ( element.currentStyle ) {
var value = element.currentStyle[ cssRule ];
}
}
return value;
}
wproObj.prototype.removeNode = function (node) {
if (node.parentNode && node.tagName != 'BODY') {
var p = node.parentNode
var cn = node.childNodes;
for (var i=cn.length-1; i>=0; i--) {
p.insertBefore(cn[i].cloneNode(true), node.nextSibling);
}
node.parentNode.removeChild(node);
}
}
wproObj.prototype.hasContent = function (node) {
if (!node) return false;
if (node.firstChild) {
var istChild = node.firstChild;
while (istChild) {
if (/^(base|script|meta|link|input|hr|spacer|img|bgsound|embed|param|area|applet|object|basefont|style|title|comment|textarea|iframe)$/i.test(istChild.tagName)) {
return true;
} else if (istChild.nodeType == 3 && !/^( |\xA0)*$/.test(istChild.nodeValue)) {
return true;
} else if (WPro.hasContent(istChild)) {
return true;
}
istChild = istChild.nextSibling;
}
}
return false
}
wproObj.prototype.tabDown = function (src) {
if (src.className != 'wproTButtonUp')
src.className = 'wproTButtonMouseDown';
}
wproObj.prototype.unescapeServerTags = function (html) {
html = html.replace(/ {0,1}\[WPSCODE_goback1_/gi, "[WPSCODE");
html = html.replace(/_goforward1_WPSCODE((\]|%5D)=""|=""|(\]|%5D)|) {0,1}/gi, "WPSCODE]");
html = html.replace(/\[WPSCODE([\s\S]*?)WPSCODE(\]=""|=""|\]|)/gi, function (x) {
c =  x.replace(/\[WPSCODE([\s\S]*?)WPSCODE[\s\S]*/i, '$1');
return unescape(c).replace(/_wproup_[a-z]/gi, function (x) {return x.substr(x.length-1).toUpperCase();}).replace(/wproslash/g, '/');
});
html = html.replace(/<!--\[WPCOMMENT([\s\S]*?)WPCOMMENT\]-->/gi, "$1");
return html;
}
wproObj.prototype.escapeServerTags = function (html) {
while (html.match(/(<[^>]*)(<(\%|\?[^x])[\s\S]*?(\%|\?)>)/gi) ) {
html = html.replace(/(<[^>]*)(<(\%|\?[^x])[\s\S]*?(\%|\?)>)/gi, function(x){
var b = x.replace(/(^<[^>]*)<[\s\S]*/i, '$1');
var a = x.replace(/^<[^>]*</i, '<');
var r = b + '[WPSCODE'+escape(a.replace(/\//g, 'wproslash').replace(/([A-Z])/g, "_wproup_$1"))+'WPSCODE]';
return r;
});
}
html = html.replace(/<([a-z0-9]+)\[WPSCODE/gi, "<$1 [WPSCODE_goback1_");
while (html.match(/((^|>)[^<]*)(<(\%|\?[^x])[\s\S]*?(\%|\?)>)/gi) ) {
html = html.replace(/((^|>)[^<]*)(<(\%|\?[^x])[\s\S]*?(\%|\?)>)/gi, "$1<!--[WPCOMMENT$3WPCOMMENT]-->");
}
return html;
}
wproObj.prototype.unescapeScriptTags = function (html) {
return this.unescapeTags(html, 'script');
}
wproObj.prototype.escapeScriptTags = function (html) {
return this.escapeTags(html, 'script');
}
wproObj.prototype.unescapeTags = function (html, tagName) {
var str = "html.replace(/(<"+tagName+"[^>]*><!--\\[WPJCODE[\\s\\S]*?WPJCODE\\]--><\\/"+tagName+">)/gi, function (x) {";
str += "	var c = x.replace(/<"+tagName+"[^>]*><!--\\[WPJCODE([\\s\\S]*?)WPJCODE\\]--><\\/"+tagName+">/gi, '$1');";
str += "	var r = unescape(c);";
str += "	return x.replace(/(<"+tagName+"[^>]*>)[\\s\\S]*?(<\\/"+tagName+">)/gi, '$1'+r+'$2');";
str += "});";
return eval (str);
}
wproObj.prototype.escapeTags = function (html, tagName) {
var str = "html.replace(/(<"+tagName+"[^>]*>[\\s\\S]*?<\\/"+tagName+">)/gi, function(x){";
str += 	"var c = x.replace(/<"+tagName+"[^>]*>([\\s\\S]*?)<\\/"+tagName+">/gi, '$1');"
str += 	"var r = '<!--[WPJCODE'+escape(c)+'WPJCODE]-->';"
str += 	"return x.replace(/(<"+tagName+"[^>]*>)[\\s\\S]*?(<\\/"+tagName+">)/gi, '$1'+r+'$2');"
str += "});"
return eval(str);
}
wproObj.prototype.closeTags = function (html) {
var pOpen = false;
var inTag = false
var cantSpan = /^(p|h1|h2|h3|h4|h5|h6|div|ul|ol|dl)$/i;
var mustClose = /^(p|li|dd|dt|option)$/i;
var currentChar = ""
var tagsArray = new Array()
var currentTag = ""
var currentClose = ""
var tagLevel = 0
var openedAt = 0;
for (var j=0;j<html.length;j++) {
currentChar = html.charAt(j)
if (currentChar == "<") { inTag = true; openedAt = j }
if (!inTag) {
} else {
currentTag += currentChar
}
if (currentChar == ">") {
inTag = false
if (currentTag.indexOf("<") != -1 && currentTag.indexOf("/>") == -1 && currentTag.indexOf("</") == -1) {
if (currentTag.indexOf(" ") != -1) {
currentTag = currentTag.substr(1,currentTag.indexOf(" ")-1)
} else {
currentTag = currentTag.substr(1,currentTag.length-2)
}
if ( mustClose.test(currentTag) && tagsArray[tagLevel-1] == currentTag ) {
var before = html.substr(0, openedAt)
var after = html.substr(openedAt)
html = before + '</'+currentTag+'>' + after;
j += ('</'+currentTag+'>').length;
} else if (tagsArray[tagLevel-1] == 'p' && cantSpan.test(currentTag)) {
var before = html.substr(0, openedAt)
var after = html.substr(openedAt)
html = before + '</p>' + after;
j += ('</'+currentTag+'>').length;
tagLevel--
} else {
tagsArray[tagLevel] = currentTag
tagLevel++
}
} else if (currentTag.indexOf("</") != -1) {
c = currentTag.replace(/[<\/>]/gi, '')
if (tagsArray[tagLevel-1] != c && c == 'p') {
var before = html.substr(0, openedAt)
var after = html.substr(j+1)
html = before + after;
j = before.length-1;
} else {
tagLevel--
}
}
currentTag = ""
}
}
return html
}
wproObj.prototype.escapeCharacters = function (str, range, mapping, winOnly) {
var t = {}
t[130] = '&#8218;';
t[131] = '&#402;';
t[132] = '&#8222;';
t[133] = '&#8230;';
t[134] = '&#8224;';
t[135] = '&#8225;';
t[136] = '&#710;';
t[137] = '&#8240;';
t[138] = '&#352;';
t[139] = '&#8249;';
t[140] = '&#338;';
t[145] = '&#8216;';
t[146] = '&#217;';
t[147] = '&#8220;';
t[148] = '&#8221;';
t[149] = '&#8226;';
t[150] = '&#8211;';
t[151] = '&#8212;';
t[152] = '&#732;';
t[153] = '&#8482;';
t[154] = '&#353;';
t[155] = '&#8250;';
t[156] = '&#339;';
t[159] = '&#376;';
t[160] = '&nbsp;';
var arr = [];
var n = str.length;
for (var j=0; j<n; j++) {
var charCode = str.charCodeAt(j);
if (t[charCode]) {
arr.push(charCode);
}
}
var n = arr.length;
for (var j=0; j<n; j++) {
str = str.replace(String.fromCharCode(arr[j]), (t[arr[j]]?t[arr[j]]:"&#"+arr[j]+";"))
}
if (!winOnly) {
if (!mapping) {
mapping = [];
}
var arr = [];
var n = str.length;
if (range) {
for (var j=0; j<n; j++) {
var charCode = str.charCodeAt(j);
if (eval(range)) {
arr.push(charCode);
}
}
} else {
for (var j=0; j<n; j++) {
var charCode = str.charCodeAt(j);
if (charCode > 127) {
arr.push(charCode);
}
}
}
var n = arr.length;
for (var j=0; j<n; j++) {
str = str.replace(String.fromCharCode(arr[j]), (mapping[arr[j]]?mapping[arr[j]]:"&#"+arr[j]+";"))
}
}
return str;
}
wproObj.prototype.removeStyleAttribute = function(node, style) {
var style = style.replace(/([A-Z])/g, function (x) { return '-'+x.toLowerCase();});
var cssText = node.style.cssText.toString()
cssText = this.styleFormatting(cssText);
cssText = cssText.replace(/url\([\s\S]*?\)/gi, function(x){return '[WP'+escape(x)+'WP]';});
cssText = cssText.replace(/"[\s\S]*?"/g, function(x){return '[WP'+escape(x)+'WP]';});
cssText = cssText.replace(/'[\s\S]*?'/g, function(x){return '[WP'+escape(x)+'WP]';});
var styles = cssText.match(/([A-Za-z\-]*:[^;]*)/gi);
if (styles) {
var n = styles.length;
var arr = {};
for (var i=0; i<n; i++) {
s = styles[i].split(/\s*:\s*/);
if (s[0] && s[1]) {
s[0] = s[0].toLowerCase()
s[1] = s[1].replace(/;\s*$/, '');
arr[s[0]] = s[1];
}
}
var arr2 = [];
var n = arr.length;
for ( x in arr) {
if (x != style) {
arr2.push(x+':'+arr[x]);
}
}
node.style.cssText = arr2.join(';').replace(/\[WP[\s\S]*?WP\]/g, function(x){return unescape(x).replace(/\[WP/g, '').replace(/WP\]/g, '');});
}
}
wproObj.prototype.rgbToHex=wproRgbToHex
wproObj.prototype.toHex=wproToHex
wproObj.prototype.hexToR = wproHexToR
wproObj.prototype.hexToG = wproHexToG
wproObj.prototype.hexToB = wproHexToB
wproObj.prototype.cutHex = wproCutHex
wproObj.prototype.hexToRGB = wproHexToRGB
wproObj.prototype.styleFormatting = wproStyleFormatting
wproObj.prototype._shorthandStyles = wp_shorthandStyles
wproObj.prototype._compressBoxStyles = wp_compressBoxStyles
wproObj.prototype._updateAll = function (func) {
var num = this.editors.length;
for (var i=0; i<num; i++) {
if (typeof(this.editors[i]) != 'undefined') {
if (this.editors[i]) {
if (this.editors[i]._loaded) {
eval('try{WPro.editors['+i+'].'+func+'()}catch(e){}');
}
}
}
}
}
wproObj.prototype.updateAll = wproObj.prototype._updateAll
wproObj.prototype.updateAllHTML = function () {this._updateAll('updateValue');}
wproObj.prototype.updateAllValue = function () {this._updateAll('updateValue');}
wproObj.prototype.updateAllDesign = function () {this._updateAll('updateDesign');}
wproObj.prototype.updateAllPreview = function () {this._updateAll('updatePreview');}
wproObj.prototype.timer = new wproTimer();
wproObj.prototype.events = new wproEvents();
wproObj.prototype._unload=function(){
wproCloseOpenDialogs();
this._updateAll('_unload');
this.timer.clearAllTimers();
this.events.removeAllEvents();
this.timer=null;
this.events=null;
this.editors=null;
this.currentEditor=null;
}
wproObj.prototype.deleteEditor = function (editorName) {
if (typeof(this.editors[editorName]) != 'undefined') {
var editor = this.editors[editorName];
if (editor.textarea.form) {
WPro.events.removeEvent(editor.textarea.form, 'submit', WPro.eval('WPro.'+editor._internalId+'.prepareSubmission'));
}
editor.container.parentNode.removeChild(editor.container);
var num = this.editors.length;
for (var i=0; i<num; i++) {
if (typeof(this.editors[i]) != 'undefined') {
if (this.editors[i]) {
if (this.editors[i]._originalName == editorName) {
delete this.editors[i];
}
}
}
}
try {eval ( "delete WPro."+editor._internalId) } catch (e) {}
delete this.editors[editorName];
delete editor;
try {eval ("delete " + editorName) } catch (e) {}
}
}
wproObj.prototype.newEditor = function (internalId, originalName) {
eval('this.'+internalId+'=new wproEditor(\''+internalId+'\', \''+originalName+'\');');
this.editors[originalName] = eval('this.'+internalId);
this.editors[(WPro.editors.length)] = eval('this.'+internalId);
}
wproObj.prototype.makeEditor = function (obj) {
var id = obj._internalId;
obj.designTab = document.getElementById(id + '_designTab');
obj.sourceTab = document.getElementById(id + '_sourceTab');
obj.previewTab = document.getElementById(id + '_previewTab');
obj.designTabButton = document.getElementById(id + '_designTabButton') ? document.getElementById(id + '_designTabButton') : false;
obj.sourceTabButton = document.getElementById(id + '_sourceTabButton') ? document.getElementById(id + '_sourceTabButton') : false;
obj.previewTabButton = document.getElementById(id + '_previewTabButton') ? document.getElementById(id + '_previewTabButton') : false;
obj.guidelinesButton = document.getElementById(id + '_guidelinesButton') ? document.getElementById(id + '_guidelinesButton') : false;
obj.designToolbar = document.getElementById(id + '_designToolbar');
obj.sourceToolbar = document.getElementById(id + '_sourceToolbar');
obj.previewToolbar = document.getElementById(id + '_previewToolbar');
obj.editorborder = document.getElementById(id + '_border');
obj.container = document.getElementById(id + '_container');
obj.loadMessage = document.getElementById(id + '_loadMessage');
var div = document.createElement('DIV');
div.className = 'wproEditor wproDialogEditorShared '+obj.themeName;
div.innerHTML = '<div id="'+id+'_hiddenMenus" class="wproHiddenMenus"></div><iframe src="'+WPro.URL+'core/html/iframeSecurity.htm" class="wproFloatingMenu" id="'+id+'_stylesMenu" name="'+id+'_stylesMenu" frameborder="0" bgcolor="#ffffff"></iframe><iframe src="'+WPro.URL+'core/html/iframeSecurity.htm" class="wproFloatingMenu" id="'+id+'_fontMenu" name="'+id+'_fontMenu" frameborder="0" bgcolor="#ffffff"></iframe><iframe src="'+WPro.URL+'core/html/iframeSecurity.htm" class="wproFloatingMenu" id="'+id+'_sizeMenu" name="'+id+'_sizeMenu" frameborder="0" bgcolor="#ffffff"></iframe>';
var body = document.getElementsByTagName('BODY').item(0);
body.insertBefore(div, body.firstChild);
obj.hiddenMenus = document.getElementById(id + '_hiddenMenus');
obj.tagPathHolder = document.getElementById(id + '_tagPath') ? document.getElementById(id + '_tagPath') : false;
obj.editFrame = document.getElementById(id + '_editFrame');
obj.previewFrame = document.getElementById(id + '_previewFrame');
if (obj.editFrame.contentWindow) {
obj.editWindow = obj.editFrame.contentWindow;
obj.previewWindow = obj.previewFrame.contentWindow;
} else {
obj.editWindow = window.frames[id + '_editFrame'];
obj.previewWindow = window.frames[id + '_previewFrame'];
}
obj.editDocument= obj.editWindow.document;
obj.PMenuMenu = null;
obj.stylesPMenuMenu = document.getElementById(id+'_stylesMenu');
obj.fontPMenuMenu = document.getElementById(id+'_fontMenu');
obj.sizePMenuMenu = document.getElementById(id+'_sizeMenu');
obj.previewButtons = obj.previewToolbar.getElementsByTagName('BUTTON');
obj.sourceButtons = obj.sourceToolbar.getElementsByTagName('BUTTON');
obj.designButtons = obj.designToolbar.getElementsByTagName('BUTTON');
obj._location = String(document.location);
obj._domain = obj._location.replace(/(^(http|https):\/\/[^\/]*)[\s\S]*/i, '$1');
if (obj.baseURL) {
var loc = obj.baseURL;
loc = loc.replace(/(^(http|https):\/\/[^\/]*)[\s\S]*/i, "$1");
obj._baseDomain = loc;
}
if (obj.themeURL.substr(0,1) == '/') {
obj.stylesheets.push(obj._domain + obj.themeURL + 'document.css?v='+WPro.version);
obj.stylesheets.push(obj._domain + obj.langURL + 'document.css?v='+WPro.version);
} else {
obj.stylesheets.push(obj.themeURL + 'document.css?v='+WPro.version);
obj.stylesheets.push(obj.langURL + 'document.css?v='+WPro.version);
}
obj.stylesheets.push(obj._domain + WPro.URL + 'core/css/document.css?v='+WPro.version);
if (WPro.isGecko) {
obj.editorborder.style.MozUserSelect = 'none';
} else if (WPro.isIE) {
var n = obj.editorborder.all.length;
for (var i=0; i<n; i++) {
obj.editorborder.all[i].unselectable = "on"
}
obj.editFrame.unselectable = "off"
}
obj.fontColor = new wproColorPicker(obj.name);
obj.fontColor.set = wproE_fontColorSet;
obj.fontColor.pick = wproE_fontColorPick;
obj.highlightColor = new wproColorPicker(obj.name);
obj.highlightColor.set = wproE_highlightColorSet;
obj.highlightColor.pick = wproE_highlightColorPick;
WPro.eval('WPro.'+obj._internalId+'._e_stopDragResize=function(e){WPro.'+obj._internalId+'._stopDragResize(e)}');
WPro.eval('WPro.'+obj._internalId+'._e_onDragResize=function(e){WPro.'+obj._internalId+'._onDragResize(e)}');
if (document.getElementById(obj._internalId+'_dragresizeButton') ) {
WPro.eval('WPro.events.addEvent(document.getElementById("'+obj._internalId+'_dragresizeButton"),"mousedown",function(e){WPro.'+obj._internalId+'._startDragResize(e);})');
}
if (obj.tagPathHolder) {
obj.tagPath = new wproTagPath(obj.name);
}
obj.history = new wproHistory(obj.name);
obj.selAPI = new wproSelAPI(obj.name);
obj.PMenu = new wproPMenu(obj.name);
obj.PMenu.inDialog = false;
obj.PMenu.name = 'WPro.'+obj._internalId+'.PMenu';
WPro.eval('WPro.'+obj._internalId+'.closePMenu = function () { WPro.'+obj._internalId+'.PMenu.closePMenu(); }');
WPro.eval('WPro.'+obj._internalId+'.closePMenuTimeout = function () { setTimeout("WPro.'+obj._internalId+'.PMenu.closePMenu();",100); }');
WPro.eval('WPro.'+obj._internalId+'._keyDownHandler = function (evt) { wproKeyDownHandler (WPro.'+obj._internalId+', evt); }');
WPro.eval('WPro.'+obj._internalId+'._keyUpHandler = function (evt) { wproKeyUpHandler (WPro.'+obj._internalId+', evt); }');
WPro.eval('WPro.'+obj._internalId+'._mouseUpHandler = function (evt) { wproMouseUpHandler (WPro.'+obj._internalId+', evt); }');
WPro.eval('WPro.'+obj._internalId+'._mouseDownHandler = function (evt) { wproMouseDownHandler (WPro.'+obj._internalId+', evt); }' );
WPro.eval('WPro.'+obj._internalId+'._contextHandler = function (evt) { wproContextHandler (WPro.'+obj._internalId+', evt); }' );
WPro.eval('WPro.'+obj._internalId+'._clickHandler = function (evt) { wproClickHandler (WPro.'+obj._internalId+', evt); }' );
WPro.eval('WPro.'+obj._internalId+'._dblClickHandler = function (evt) { wproDblClickHandler (WPro.'+obj._internalId+', evt); }' );
if (WPro.isGecko) WPro.eval('WPro.'+obj._internalId+'._dropPasteHandler = function (evt) { setTimeout("wproDropPasteHandler (WPro.'+obj._internalId+')",1); }' );
WPro.eval('WPro.'+obj._internalId+'.prepareSubmission = function (evt) { WPro.'+obj._internalId+'.showLoadMessage(); WPro.'+obj._internalId+'._inSubmit = true; WPro.'+obj._internalId+'.updateValue(); WPro.'+obj._internalId+'.textarea.value=WPro.'+obj._internalId+'.triggerHTMLFilter(\'submit\',WPro.'+obj._internalId+'.textarea.value); WPro.'+obj._internalId+'.triggerEditorEvent(\'submit\'); WPro.'+obj._internalId+'._inSubmit=false; setTimeout(\'WPro.'+obj._internalId+'.hideLoadMessage()\',1); }' );
if (WPro.isGecko) {
obj.selectFix = wproSelectFix;
obj.fillContent = wproFillContent;
}
obj._initToolbar (obj.designToolbar);
obj._initToolbar (obj.sourceToolbar);
obj._initToolbar (obj.previewToolbar);
var plugins
if (plugins = obj.pluginsToLoad) {
var n = plugins.length;
for (var i=0;i<n;i++) {
obj.loadPlugin(plugins[i]);
}
}
var events
if (events = obj.eventsToLoad) {
var n = events.length;
for (var i=0;i<n;i++) {
obj.addEditorEvent(events[i][0],events[i][1]);
}
}
var filters
if (filters = obj.filtersToLoad) {
var n = filters.length;
for (var i=0;i<n;i++) {
obj.addHTMLFilter(filters[i][0],filters[i][1]);
}
}
var bsh
if (bsh = obj.bshToLoad) {
var n = bsh.length;
for (var i=0;i<n;i++) {
obj.addButtonStateHandler(bsh[i][0],bsh[i][1]);
}
}
var fh
if (fh = obj.fhToLoad) {
var n = fh.length;
for (var i=0;i<n;i++) {
obj.addFormattingHandler(fh[i][0],fh[i][1]);
}
}
var fvh
if (fvh = obj.fvhToLoad) {
var n = fvh.length;
for (var i=0;i<n;i++) {
obj.addFormattingValueHandler(fvh[i][0],fvh[i][1]);
}
}
obj.triggerEditorEvent('init');
obj.editorborder.style.display='block';
obj.resetDimensions();
if (obj.textarea.form) {
WPro.events.addEvent(obj.textarea.form, 'submit', WPro.eval('WPro.'+obj._internalId+'.prepareSubmission'));
WPro._setFormSubmitFunction(obj.textarea.form);
}
obj._setSnippetStatus(obj.textarea.value);
obj.addHTMLFilter('submit', wproSFClearEmpty);
obj.addHTMLFilter('design', wproDesignFilter);
obj.addButtonStateHandler('undo', wproUndoBSH);
obj.addButtonStateHandler('redo', wproRedoBSH);
if (WPro.isGecko) {
obj.addButtonStateHandler('cut', wproCutCopyBSH);
obj.addButtonStateHandler('copy', wproCutCopyBSH);
obj.addButtonStateHandler('paste', wproPasteBSH);
}
obj.addButtonStateHandler('guidelines', wproGuidelinesBSH);
obj.textarea.value = obj.textarea.value.replace(/\|wproSelectionEnd\|/gi, '').replace(/\|wproSelectionStart\|/gi, '');
switch(obj.startView) {
case 'design' :
obj._realToolbarHeight = obj.designToolbar.offsetHeight;
obj._movingToDesign = true;
obj.textarea.value = obj.triggerHTMLFilter('rawSource',obj.textarea.value);
obj._showDesign();
break;
case 'source' :
obj._movingToSource = true;
obj._showSource();
break;
case 'preview' :
obj._movingToPreview = true;
obj.updateDesign();
obj._showPreview();
break;
}
obj._sessTimeout();
}
function wproEditor (internalId, originalName) {
this.textarea = wp_getEditorTextareaByName(originalName);
this.textarea.style.display = 'none';
this._internalId = internalId
this.name = originalName;
this.id = originalName;
this.frameCount = 0;
this._preserve = {};
this._preserve['b_doctype']='';
this._preserve['b_html']='';
this._preserve['b_head']='';
this._preserve['b_body']='';
this._preserve['a_body']='';
this._preserve['a_html']='';
this._casePreserve = [];
this._inDesign = false;
this._inSource = false;
this._inPreview = false;
this._movingToDesign = false;
this._movingToSource = false;
this._movingToPreview = false;
this._loaded = false;
this._initiated = false;
this._initFocus = false;
this.plugins = [];
this.pluginsToLoad = [];
this.eventsToLoad = [];
this._events = {};
this.filtersToLoad = [];
this._filters = {};
this.bshToLoad = [];
this.buttonStateHandlers = [];
this.fhToLoad = [];
this.fvhToLoad = [];
this.formattingHandlers = [];
this.formattingValueHandlers = [];
this._EDEvents = [];
this._wordWrap = false;
this._realToolbarHeight = 0;
}
wproEditor.prototype._writeDocument = wproWriteDocument;
wproEditor.prototype.lineReturn = wproLineReturn;
wproEditor.prototype.start = function () {
if (WPro.isSafari) {
document.getElementById(this._internalId + '_border').style.display = 'block';
setTimeout("WPro.makeEditor(WPro."+this._internalId+")", 1);
} else {
WPro.makeEditor(this);
}
}
wproEditor.prototype.addEditorEvent = function (trigger, func) {
trigger = trigger.toLowerCase();
if (typeof(this._events[trigger])=='undefined') {
this._events[trigger] = [];
}
this._events[trigger].push(func);
}
wproEditor.prototype.removeEditorEvent = function (trigger, func) {
trigger = trigger.toLowerCase();
var actions
if (typeof(this._events[trigger])=='undefined') {
return;
}
actions = this._events[trigger];
var n = actions.length
for (var i=0; i<n; i++) {
if (actions[i]==func) {
actions.splice(i,1);
break;
}
}
}
wproEditor.prototype.triggerEditorEvent = function (trigger, param) {
trigger = trigger.toLowerCase();
if (typeof(this._events[trigger])!='undefined') {
var n = this._events[trigger].length
for (var i=0; i<n; i++) {
this._events[trigger][i](this, param);
}
}
}
wproEditor.prototype.addHTMLFilter = function (type, func, first) {
type = type.toLowerCase();
if (typeof(this._filters[type])=='undefined') {
this._filters[type] = [];
}
if (first) {
this._filters[type].unshift(func);
} else {
this._filters[type].push(func);
}
}
wproEditor.prototype.removeHTMLFilter = function (type, func, first) {
type = type.toLowerCase();
var actions
if (typeof(this._filters[type])=='undefined') {
return;
}
filters = this._filters[type];
var n = filters.length
for (var i=0; i<n; i++) {
if (filters[i]==func) {
filters.splice(i,1);
break;
}
}
}
wproEditor.prototype.triggerHTMLFilter = function (type, html) {
type = type.toLowerCase();
if (typeof(this._filters[type])!='undefined') {
var n = this._filters[type].length
for (var i=0; i<n; i++) {
html = this._filters[type][i](this, String(html));
}
return String(html);
} else {
return String(html);
}
}
wproEditor.prototype.loadPlugin = function (pluginName, params) {
if (eval('try {wproPlugin_'+pluginName+'}catch(e){}')) {
var p = eval('new wproPlugin_'+pluginName+'(this)');
this.plugins[pluginName] = p;
if (typeof(p.init)!='undefined') p.init(this, params);
}
}
wproEditor.prototype.addButtonStateHandler = function (cid, func) {
this.buttonStateHandlers[cid] = func;
}
wproEditor.prototype.addFormattingHandler = function (cid, func) {
this.formattingHandlers[cid.toLowerCase()] = func;
}
wproEditor.prototype.addFormattingValueHandler = function (cid, func) {
this.formattingValueHandlers[cid.toLowerCase()] = func;
}
wproEditor.prototype.showLoadMessage = function () {
this.loadMessage.style.width=this.editorborder.offsetWidth + 'px';
this.loadMessage.style.display = 'block';
}
wproEditor.prototype.hideLoadMessage= function () {
this.loadMessage.style.display = 'none';
}
wproEditor.prototype.preventInteraction= function () {
this.loadMessage.style.width=this.editorborder.offsetWidth + 'px';
this.loadMessage.style.display = 'block';
this.loadMessage.firstChild.style.display = 'none';
}
wproEditor.prototype.allowInteraction= function () {
this.loadMessage.style.display = 'none';
this.loadMessage.firstChild.style.display = 'block';
}
wproEditor.prototype.redraw = function () {
if ((!this._inPreview && !this._movingToPreview) || this._movingToDesign) {
this.showGuidelines();
this.setButtonStates();
this.editFrame.style.display = 'none';
this.editFrame.style.display='';
if (WPro.isGecko) {
WPro.callCommand(this.editDocument, "usecss", false, true);
WPro.callCommand(this.editDocument, "styleWithCss", false, false);
}
}
}
wproEditor.prototype.redrawTimeout = function () {
if (WPro.isGecko) {
if ((!this._inPreview && !this._movingToPreview) || this._movingToDesign) {
if (this.initFocus) {
eval('wproCurrentRange_'+this._internalId+' = this.selAPI.getRange()');
eval('wproCurrentScrollTop_'+this._internalId+' = this.editDocument.body.scrollTop + this.editDocument.documentElement.scrollTop');
eval('wproCurrentScrollLeft_'+this._internalId+' = this.editDocument.body.scrollTop + this.editDocument.documentElement.scrollTop');
}
eval('wproInitFocus_'+this._internalId+' = this.initFocus');
this.showGuidelines();
this.setButtonStates();
this.editFrame.parentNode.style.height = this.editFrame.offsetHeight + 'px';
this.editFrame.style.display = 'none';
setTimeout("WPro."+this._internalId+".editFrame.style.display='';WPro."+this._internalId+".editFrame.parentNode.style.height='';WPro."+this._internalId+"._enableDesignMode();if(wproInitFocus_"+this._internalId+"){wproCurrentRange_"+this._internalId+".select();WPro."+this._internalId+".editWindow.scrollTo(wproCurrentScrollLeft_"+this._internalId+",wproCurrentScrollTop_"+this._internalId+")};",1);
}
} else {
this.redraw();
}
}
wproEditor.prototype.getValue = function ()  {
if (this._inDesign) {
if (this._guidelines) {
this.hideGuidelines()
}
this._setSnippetStatus(this.textarea.value);
var bod = this.editDocument.body.innerHTML
bod = bod.replace(/<<([^>]*)>>/gi, "<$1>").replace(/<\/<([^>]*)>>/gi, "</$1>").replace(/<>/gi, "").replace(/<\/>/gi, "")
if (!this.snippet) {
var htmla = '';
var node = this.editDocument.getElementsByTagName('HTML')[0];
if (this.useXHTML) {
if (!node.getAttribute('xmlns')) {
htmla += ' xmlns="http://www.w3.org/1999/xhtml"';
}
if (!node.getAttribute('xml:lang') && this.htmlLang) {
htmla += ' xml:lang="' + this.htmlLang.toLowerCase() + '"';
}
}
if (!node.getAttribute('lang') && this.htmlLang) {
htmla += ' lang="' + this.htmlLang.toLowerCase() + '"';
}
if (!node.getAttribute('dir') && this.htmlDirection) {
htmla += ' dir="' + this.htmlDirection.toLowerCase() + '"';
}
htmla += WPro.getNodeAttributesString(node);
var heada = WPro.getNodeAttributesString(this.editDocument.getElementsByTagName('HEAD')[0]);
var bodya = WPro.getNodeAttributesString(this.editDocument.getElementsByTagName('BODY')[0]);
str = this.doctype + '<ht'+'ml'+htmla+'><he'+'ad'+heada+'>'+this.editDocument.getElementsByTagName('HEAD')[0].innerHTML+'</he'+'ad><bo'+'dy'+bodya+'>' + bod + '</bo'+'dy></ht'+'ml>';
str = eval('str.replace(/^([\\s\\S]*'+WPro.quoteMeta(this.doctype)+'[\\s\\S]*<html[^>]*>)/gi,this._preserve[\'b_doctype\']+"$1")');
str = eval('str.replace(/^([\\s\\S]*'+WPro.quoteMeta(this.doctype)+')([\\s\\S]*<html[^>]*>)/gi,"$1"+this._preserve[\'b_html\']+"$2")');
str = str.replace(/(<html[^>]*>)([\s\S]*<head[^>]*>)/gi, "$1"+this._preserve['b_head']+"$2");
str = str.replace(/(<\/head[^>]*>)([\s\S]*<body[^>]*>)/gi, "$1"+this._preserve['b_body']+"$2");
str = str.replace(/(<\/body[^>]*>)([\s\S]*<\/html[^>]*>)/gi, "$1"+this._preserve['a_body']+"$2");
str = str.replace(/(<\/html[^>]*>)([\s\S]*)$/gi, "$1"+this._preserve['a_html']+"$2");
} else {
var str = bod
}
if (this._guidelines && !this._inSource && !this.inPreview && !this._movingToSource && !this._movingToPreview) {
this.showGuidelines()
}
str = this.sourceFormatting(str);
} else if (this._inSource) {
var str = this.getText();
this._setSnippetStatus(str);
} else {
var str = this.textarea.value;
this._setSnippetStatus(str);
}
return str;
}
wproEditor.prototype.getHTML = wproEditor.prototype.getValue
wproEditor.prototype.getCode = wproEditor.prototype.getValue
wproEditor.prototype.urlFormatting = function (url) {
if (url.match(/="javascript:/gi) || url.match(/^javascript:/gi)) {
return url;
}
var attribute = false
var matches = url.match(/^ (data|href|src|action|longdesc|profile|usemap|background|cite|classid|codebase)=/gi)
if (matches) {
attribute = matches[0];
url = url.replace(/^ (data|href|src|action|longdesc|profile|usemap|background|cite|classid|codebase)="([^"]*)"$/gi, '$2');
}
var locationRegex = new RegExp('^'+WPro.quoteMeta(this._location).replace(/[\\]&/gi,'(\\&|\\&amp\\;)')+'#', 'gi');
url = url.replace(locationRegex, '#');
var locationRegex2 = new RegExp('^'+WPro.quoteMeta(this._domain + WPro.URL + 'core/html/')+'(iframeSecurity.htm|blank.htm)', 'gi');
url = url.replace(locationRegex2, '');
var location = this._location.replace(/^([\s\S]*\/)[^\/]*$/i, "$1");
if (this.urlFormat=='absolute') {
if (this._baseDomain) {
url = url.replace(/^\//gi, this._baseDomain+'/');
url = url.replace(/^([^#][^:"]*)$/gi, this.baseURL+'$1');
} else {
url = url.replace(/^\//gi, this._domain+'/');
url = url.replace(/^([^#][^:"]*)$/gi, location+'$1');
}
while(url.match(/([^:][^\/])\/[^\/]*\/\.\.\//i)) {
url = url.replace(/([^:][^\/])\/[^\/]*\/\.\.\//i, '$1/');
}
url = url.replace(/\/\.\.\//gi, '/');
} else if (this.urlFormat=='nodomain'||this.urlFormat=='relative') {
if (this._baseDomain) {
var loc = this._baseDomain;
} else {
var loc = this._domain;
}
if (loc.match(/^http(s|)\:\/\/www\./i)) {
var domainRegex = new RegExp('^'+( WPro.quoteMeta(loc).replace(/^(http(s|)\\\:\\\/\\\/)www\\\./i, '$1(www\\.|)') ) +'[^\/]*(|/)', 'gi');
} else {
var domainRegex = new RegExp('^'+( WPro.quoteMeta(loc).replace(/^(http(s|)\\\:\\\/\\\/)/i, '$1(www\\.|)') ) +'[^\/]*(|/)', 'gi');
}
url = url.replace(domainRegex, '$2');
if (this.urlFormat=='relative'&&this.baseURL) {
var b = this.baseURL.replace(domainRegex, '$2');
var r = new RegExp('^'+WPro.quoteMeta(b), 'gi');
url = url.replace(r, '');
if (url.substr(0,1)=='/') {
url = url.substr(1);
var c = b.match(/\//g);
for (var i=0;i<c.length;i++) {
url = '../'+url;
}
}
}
}
url = url.replace(/^[^#]*#[\s\S]+$/g, function (x){s=x.split('#');return s[0]+'#'+unescape(s[1]);});
if (this.encodeURLs) {
url = url.replace(/^[^"#]*/g, function (x){return x.replace(/ /g, '%20');});
} else {
url = unescape(url);
}
if (attribute) {
url = attribute+'"'+url+'"';
}
return url;
}
wproEditor.prototype.sourceFormatting = function (html) {
WPro.currentEditor = this;
html = this.triggerHTMLFilter('rawSource',html);
html = WPro.escapeServerTags(html);
html = WPro.escapeScriptTags(html);
if (WPro.isIE) {
while (html.match(/<[^>]+ [a-z_]+='[^']*[<>"][^']*'/gi)) {
html = html.replace(/<[^>]+ [a-z_]+='[^']*[<>"][^']*'/gi, function(x){var a = x.replace(/<[^>]+ [a-z_]+='([^']*[<>"][^']*)'/gi, '$1').replace(/"/gi, '&quot;').replace(/>/gi, '&gt;').replace(/</gi, '&lt;'); return x.replace(/(<[^>]+ [a-z_]+=)'[^']*[<>"][^']*'/gi, '$1"'+a+'"'); });
}
while (html.match(/<[^>]+ [a-z_]+="[^"]*[<>][^"]*"/gi)) {
html = html.replace(/<[^>]+ [a-z_]+="[^"]*[<>][^"]*"/gi, function(x){var a = x.replace(/<[^>]+ [a-z_]+="([^"]*[<>][^"]*)"/gi, '$1').replace(/>/gi, '&gt;').replace(/</gi, '&lt;'); return x.replace(/(<[^>]+ [a-z_]+=)"[^"]*[<>][^"]*"/gi, '$1"'+a+'"'); });
}
}
if (WPro.isGecko) {
html = html.replace(/<noembed[^>]*>[\s\S]*?<\/noembed>/gi, function(x){var b = x.replace(/(<noembed[^>]*>)[\s\S]*?<\/noembed>/i, '$1'); var c = x.replace(/<noembed[^>]*>([\s\S]*?)<\/noembed>/i, '$1');return b+WPro.htmlSpecialCharsDecode(c)+'</noembed>';});
}
var results = html.match(/<[^>]+>/g);
if (results) {
var rl = results.length;
for (var i=0; i < rl; i++) {
var original = results[i];
var f = results[i].substr(1,1);
if (f == '!' || f == '%') {
continue;
} else if (f == '?') {
if (results[i].substr(1,2)!='?x') {
continue;
}
}
results[i] = results[i].replace(/<\/?[^>|^ ]+/, function(x) { return x.toLowerCase() });
if (results[i].substr(1,1) == '/') {
results[i] = results[i].replace(/<\/(area|bgsound|base|basefont|br|comment|col|frame|hr|input|img|isindex|link|meta|param|spacer|wbr)>/gi, "");
html = html.replace(original, results[i]);
continue;
};
if (this.useXHTML) {
results[i] = results[i].replace(/[\s]+\/>$/, '>');
results[i] = results[i].replace(/^<((area|bgsound|base|basefont|br|comment|col|frame|hr|input|img|isindex|link|meta|param|spacer|wbr)(|[^>]+))>$/i, "<$1 />");
}
if (!/\s/.test(results[i])) {
html = html.replace(original, results[i]);
continue;
}
var tagName = results[i].replace(/<([^>|^ ]*)[^>]*>/g, "$1");
results[i] = results[i].replace(/ (nowrap|ismap|declare|noshade|checked|disabled|readonly|multiple|selected|noresize|defer)( |>)/gi, " $1=\"$1\"$2");
results[i] = results[i].replace(/ (nowrap|ismap|declare|noshade|checked|disabled|readonly|multiple|selected|noresize|defer)=["]*true["]*/gi, " $1=\"$1\"");
var stripquoted = results[i].replace(/ [^=]+= *"[^"]*"/g,"");
var tempregexp = new RegExp(" [^=]+=[^ |>]+", "g")
var unquoted = stripquoted.match(tempregexp);
if (unquoted) {
var ul = unquoted.length
for (var j=0; j < ul; j++) {
var addquotes = unquoted[j].replace(/( [^=]+=)([^ |>]+)/g, "$1\"$2\"");
results[i] = results[i].replace(unquoted[j],addquotes);
}
}
results[i] = results[i].replace(/ [a-zA-Z]+="/g, function(y) { return y.toLowerCase() })
results[i] = results[i].replace(/ (align|valign|shape|type|nowrap|ismap|declare|noshade|checked|disabled|readonly|multiple|selected|noresize|defer)="[^"]+"/g, function(y) { return y.toLowerCase() });
results[i] = results[i].replace(/ style="[^"]+/gi, function (y) {return ' style="'+WPro.styleFormatting(y, true);});
if (tagName == 'img' || tagName == 'area') {
if (!results[i].match(" alt=\"[^\"]*\"")) {
results[i] = results[i].replace(/(<\/?[^>|^ ]+)/, "$1 alt=\"\"");
}
} else if (tagName == 'a' && this.useXHTML) {
if (results[i].match(" name=\"[^\"]*\"") && !results[i].match(" id=\"[^\"]*\"")) {
results[i] = results[i].replace(/ name="([^"]+)"/, " id=\"$1\" name=\"$1\"");
}
}
if (tagName!='base') {
var a = ['href','src','action'];
for (var j=0;j<a.length;j++) {
var r = new RegExp(' _wpro_'+a[j]+'="[^"]*"', 'g');
if (results[i].match(r)) {
var r2 = new RegExp(' '+a[j]+'="[^"]*"', 'g');
results[i] = results[i].replace(r2, '');
var r3 = new RegExp(' _wpro_('+a[j]+')="([^"]*)"', 'g');
results[i] = results[i].replace(r3, ' $1="$2"');
}
}
results[i] = results[i].replace(/ (data|href|src|action|longdesc|profile|usemap|background|cite|classid|codebase)="[^"]+"/g, function(x){return WPro.currentEditor.urlFormatting(x);});
}
if (tagName == 'body') {
results[i] = results[i].replace(/ contenteditable="true"/, '');
}
results[i] = results[i].replace(/( class="[\s\S]*?)wproGuide[\s]*([\s\S]*?")/i, '$1$2');
results[i] = results[i].replace(/ _wpro_type="([^"]*)"/i, ' type="$1"');
results[i] = results[i].replace(/ type="text\/wpro"/i, '');
results[i] = results[i].replace(/ _wpro_(on[a-z]+="[^"]*")/gi, ' $1');
results[i] = results[i].replace(/ (_base_href|_moz_dirty|_moz_editor_bogus_node)="[^"]*"/gi, '').replace(/ style=\"\"/gi, "").replace(/ class=\"\"/gi, "").replace(/ type=\"_moz\"/gi, '');
html = html.replace(original, results[i]);
}
}
html = WPro.escapeTags(html, 'pre');
html = html.replace(/\n[\n\s\r\t]*(<[^>]+>)/g, "$1").replace(/\r/gi, '');
html = WPro.unescapeTags(html, 'pre');
html = html.replace(/(^|>)[^<>]+(<|$)/g, function(x) {return x.replace(/"/g, '&quot;');});
if (WPro.isIE) {
html = WPro.closeTags(html);
}
if (this.baseURL) {
html = eval ('html.replace(/<base[^>]* href="'+WPro.quoteMeta(this.baseURL)+'"[^>]*>/gi, "")');
}
html = html.replace(/<link[^>]* id="WPRO_STYLESHEET_[0-9]+"[^>]*>/gi, "");
if (this.defaultCSS != '') {
html = eval ('html.replace(/<style[^>]* type="text\\/css"[^>]*>' + WPro.quoteMeta(this.defaultCSS) + '<\\/style>/gi, "")');
}
html = html.replace(/(<meta content="MSHTML[^>]*>)/gi, "");
html = html.replace(/<p><\/p>/gi, '').replace(/<br><\/br>/gi, "<br />");
html = html.replace(/<a ([^>]*)>(\|wproSelectionStart\|\|wproSelectionEnd\||)&nbsp;<\/a>/gi, '<a $1>$2</a>');
html = WPro.escapeTags(html, 'pre');
html = html.replace(/(<\?xml[\s\S]*?>)([^\n])/gi, "$1\n$2").replace(/([^\n])<([A-Za-z])/gi, "$1\n<$2").replace(/\n<((a|abbr|acronym|applet|b|bdo|big|br|cite|code|dfn|em|embed|font|i|img|input|kbd|label|nobr|object|q|s|samp|select|small|span|strike|strong|sub|sup|textarea|tt|u|var)[ >])/gi, "<$1").replace(/([^\n])<\/(head|body|html)/gi, "$1\n</$2").replace(/([^\n])<body/gi, "$1\n<bo"+"dy").replace(/<br([^>]*)>([^\n])/gi, "<br$1>\n$2");
html = WPro.unescapeTags(html, 'pre');
html = html.replace(/<b(| [^>]*)>/gi, '<strong$1>').replace(/<\/b>/gi, '</strong>').replace(/<i(| [^>]*)>/gi, '<em$1>').replace(/<\/i>/gi, '</em>');
html = html.replace(/([^>\n])<br(| [^>]*)>\n<\/(p|div|h1|h2|h3|h4|h5|h6)>/gi, '$1</$3>').replace(/<(p|div|h1|h2|h3|h4|h5|h6)([^>]*)><br(| [^>]*)>\n<\/(p|div|h1|h2|h3|h4|h5|h6)>/gi, '<$1$2>&nbsp;</$4>');
if (this.escapeCharacters) {
html = WPro.escapeCharacters(html, this.escapeCharactersRange, this.escapeCharactersMappings, false);
} else {
html = WPro.escapeCharacters(html, null, null, true);
}
html = WPro.unescapeScriptTags(html);
html = html.replace(/[ ]*\n/g, '\n')
html = html.trim();
html = html.replace(/(\|wproSelectionStart\|\|wproSelectionEnd\|)\n/gi, "$1");
html = WPro.unescapeServerTags(html);
html = this.triggerHTMLFilter('source',html);
return html;
}
wproEditor.prototype.getPreviewHTML = function () {
var html = this.getValue();
this.textarea.value = html;
str = this._buildPreviewHTML(html);
str = this.triggerHTMLFilter('preview',str)
return str
}
wproEditor.prototype.getPreviewCode = wproEditor.prototype.getPreviewHTMLs
wproEditor.prototype._buildPreviewHTML = function (html,docStyles) {
var base = '';
var head = '';
if (this.baseURL) {
base += '<base href="'+this.baseURL+'">';
}
var num = this.stylesheets.length;
if (!docStyles) {
num --;
num --;
}
for (var i=0; i < num; i++) {
if (this.stylesheets[i] != '') {
head += '<link id="WPRO_STYLESHEET_'+i+'" href="' + this.stylesheets[i] + '" type="text/css" rel="stylesheet">';
}
}
if (this.defaultCSS != '') {
head += '<style type="text/css">' + this.defaultCSS + '</style>';
}
if (html.length == 0) {
if (this.emptyValue == 'auto') {
switch (this.lineReturns) {
case 'div' :
html = '<div>&nbsp;</div>';
break;
case 'p' :
html = '<p>&nbsp;</p>';
break;
case 'br' :
if (this.useXHTML) {
html = '<br />';
} else {
html = '<br>';
}
break;
}
} else {
html = this.emptyValue;
}
}
var htmla = '';
if (this.useXHTML) {
htmla += ' xmlns="http://www.w3.org/1999/xhtml"';
if (this.htmlLang) {
htmla += ' xml:lang="' + this.htmlLang.toLowerCase() + '"';
}
}
if (this.htmlLang) {
htmla += ' lang="' + this.htmlLang.toLowerCase() + '"';
}
if (this.htmlDirection) {
htmla += ' dir="' + this.htmlDirection.toLowerCase() + '"';
}
if (this.snippet) {
var str = this.doctype + '<ht'+'ml'+htmla+'><he'+'ad><title>Preview</title><meta http-equiv="Content-Type" content="text/html; charset=' + this.charset + '">' + base + head + '</he'+'ad><bo'+'dy>' + html + '</bo'+'dy></ht'+'ml>';
} else {
var str = html.replace(/<head([^>]*)>/gi, "<he"+"ad$1>"+base).replace(/<\/head>/gi, head+"</he"+"ad>").replace(/<html([^>]*)>/gi, "<ht"+"ml" + htmla + "$1>");
if (str.match(/^[\s\S]*<html[^>]*>[\s\S]*<body[^>]*>[\s\S]*<\/body[^>]*>[\s\S]*<\/html[^>]*>[\s\S]*$/gi)) {
if (eval('str.match(/^[\\s\\S]*'+WPro.quoteMeta(this.doctype)+'[\\s\\S]*<html[^>]*>/gi)')) {
this._preserve['b_doctype'] = eval('str.replace(/^([\\s\\S]*)'+WPro.quoteMeta(this.doctype)+'[\\s\\S]*<html[^>]*>[\\s\\S]*$/gi,"$1")');
this._preserve['b_html'] = eval('str.replace(/^[\\s\\S]*'+WPro.quoteMeta(this.doctype)+'([\\s\\S]*)<html[^>]*>[\\s\\S]*$/gi,"$1")');
str = eval('str.replace(/^[\\s\\S]*('+WPro.quoteMeta(this.doctype)+')[\\s\\S]*(<html[^>]*>)/gi, "$1$2")');
} else if (str.replace(/^[\s\S]*<html[^>]*>[\s\S]*$/gi)) {
this._preserve['b_doctype'] = '';
this._preserve['b_html'] = str.replace(/^([\s\S]*)<html[^>]*>[\s\S]*$/gi,"$1");
str = str.replace(/^[\s\S]*(<html[^>]*>)/gi,"$1");
}
if (str.match(/^[\s\S]*<html[^>]*>[\s\S]*<head[^>]*>[\s\S]*$/gi)) {
this._preserve['b_head'] = str.replace(/^[\s\S]*<html[^>]*>([\s\S]*)<head[^>]*>[\s\S]*$/gi,"$1");
str = str.replace(/(<html[^>]*>)[\s\S]*(<head[^>]*>)/gi,"$1$2");
}
if (str.match(/^[\s\S]*<\/head[^>]*>[\s\S]*<body[^>]*>([\s\S]*)$/gi)) {
this._preserve['b_body'] = str.replace(/^([\s\S]*)<\/head[^>]*>([\s\S]*)<body[^>]*>([\s\S]*)$/gi,"$2");
str = str.replace(/(<\/head[^>]*>)[\s\S]*(<body[^>]*>)/gi,"$1$2");
}
this._preserve['a_body'] = str.replace(/^([\s\S]*)<\/body[^>]*>([\s\S]*)<\/html[^>]*>([\s\S]*)$/gi,"$2");
this._preserve['a_html'] = str.replace(/^([\s\S]*)<\/html[^>]*>([\s\S]*)$/gi,"$2");
str = str.replace(/(<\/body[^>]*>)[\s\S]*(<\/html[^>]*>)[\s\S]*$/gi, "$1$2");
}
}
str = WPro.escapeServerTags(str);
if (!WPro.isIE) {
str = str.replace(/<strong>/gi, '<b>').replace(/<strong /gi, '<b ').replace(/<\/strong>/gi, '</b>').replace(/<em>/gi, '<i>').replace(/<em /gi, '<i ').replace(/<\/em>/gi, '</i>');
}
return str;
}
wproEditor.prototype.getSelectedText = function () {
return this.selAPI.getRange().toString();
}
wproEditor.prototype.getSelectedHTML = function () {
var str;
return this.sourceFormatting(this.selAPI.getRange().getHTMLText());
}
wproEditor.prototype.setValue = function (html) {
this.textarea.value=html;
if (this._inDesign) {
this.updateDesign();
} else if (this._inSource) {
this.updateValue(html);
} else if (this._inPreview) {
this.updatePreview();
}
}
wproEditor.prototype.setHTML = wproEditor.prototype.setValue
wproEditor.prototype.setCode = wproEditor.prototype.setValue
wproEditor.prototype.insertAtSelection = function (code) {
if (code) {
this.focus();
var range = this.selAPI.getRange()
range.pasteHTML(code);
range.select();
this.redraw();
}
}
wproEditor.prototype.insertImage = function (src, attrs, style) {
var UDBeforeState = this.history.pre();
var img;
if (!attrs) attrs=[];
var range = this.selAPI.getRange();
if (range.nodes[0]) {
if (range.nodes[0].tagName=='IMG') {
if (!range.nodes[0].className.match(/wproFilePlugin/i)) {
img = range.nodes[0];
}
}
}
if (!img) {
if (WPro.isIE) {
this.callFormatting('insertimage', src);
} else {
var img = this.editDocument.createElement('IMG');
img.src= src;
range.insertNode(img, true);
range.select();
}
var range = this.selAPI.getRange();
if (range.nodes[0]) {
if (range.nodes[0].tagName=='IMG') {
img = range.nodes[0];
}
}
}
if (img) {
var l = attrs.length
img.src = src;
img.setAttribute('_wpro_src', src);
var width = '';
var height = '';
var border = '';
for (var a in attrs) {
if (this.strict) {
if (a=='border'&&attrs[a]!='') {
border = attrs[a]+'px';continue;
}
}
if (a=='class') {
img.className = attrs[a];
} else if (a=='style') {
img.style.cssText = attrs[a];
} else if (a=='width') {
if (img.style.width) img.style.width = attrs[a] + 'px';
img.setAttribute(a, attrs[a]);
} else if (a=='height') {
if (img.style.height) img.style.height = attrs[a] + 'px';
img.setAttribute(a, attrs[a]);
} else if (attrs[a]==''&&a!='alt'&&a!='title') {
img.removeAttribute(a);
} else {
img.setAttribute(a, attrs[a]);
}
}
if (this.strict) {
if (border) {
img.style.borderWidth = border;
}
}
if (style) {
this.applyStyle(style, [img]);
}
}
this.history.post(UDBeforeState);
this.redraw();
}
wproEditor.prototype._addEditDocumentEvents = function () {
this._removeEditDocumentEvents();
this._EDEvents[0]=WPro.events.addEvent(this.editDocument, 'mouseup', eval('WPro.' + this._internalId + '._mouseUpHandler'));
this._EDEvents[1]=WPro.events.addEvent(this.editDocument, 'mousedown', eval('WPro.' + this._internalId + '._mouseDownHandler'));
if (WPro.isIE) {
var keyhandler = 'keydown';
} else {
var keyhandler = 'keypress';
}
this._EDEvents[2]=WPro.events.addEvent(this.editDocument, keyhandler, eval('WPro.' + this._internalId + '._keyDownHandler'));
this._EDEvents[3]=WPro.events.addEvent(this.editDocument, 'keyup', eval('WPro.' + this._internalId + '._keyUpHandler'));
this._EDEvents[4]=WPro.events.addEvent(this.editDocument, 'contextmenu', eval('WPro.' + this._internalId + '._contextHandler'));
this._EDEvents[5]=WPro.events.addEvent(this.editDocument, 'click', eval('WPro.' + this._internalId + '._clickHandler'));
this._EDEvents[6]=WPro.events.addEvent(this.editDocument, 'dblclick', eval('WPro.' + this._internalId + '._dblClickHandler'));
if (WPro.isGecko) {
this._EDEvents[7]=WPro.events.addEvent(this.editDocument, 'dragdrop', eval('WPro.' + this._internalId + '._dropPasteHandler'));
}
}
wproEditor.prototype._removeEditDocumentEvents = function () {
l = this._EDEvents.length;
for (var i=0;i<l;i++) {
if (this._EDEvents[i]) {
WPro.events.removeEventById(this._EDEvents[i]);
}
}
this._EDEvents = [];
}
wproEditor.prototype.updateValue = function (str) {
if (this._inDesign && this._movingToSource) {
this._markSelection();
}
if (!str) {
var str = this.getValue();
}
this.textarea.value = str;
this._setSnippetStatus(str);
if ((this._inSource || this._movingToSource) && !this._inSubmit) {
str = this.syntaxHighlight(str);
var html = '<!DOCTYPE html PUBLIC "-/'+'/W3C/'+'/DTD XHTML 1.0 Transitional/'+'/EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><ht'+'ml xmlns="http://www.w3.org/1999/xhtml"><he'+'ad><title>Source Code</title><link rel="stylesheet" href="'+this._domain+WPro.URL+'core/css/source.css?v='+WPro.version+'" type="text/css"></he'+'ad><bo'+'dy>' + str + '</bo'+'dy></ht'+'ml>'
if (!this._initiated) {
if (WPro.isIE&&WPro.browserVersion<7) {
this._enableDesignMode();
}
}
this._writeDocument(html);
if (!this._initiated) {this.tempStr = html;}
this._updateHTMLTimeout();
}
}
wproEditor.prototype.updateHTML = wproEditor.prototype.updateValue
wproEditor.prototype._updateHTMLTimeout = function () {
if (this.editDocument.body) {
this._addEditDocumentEvents();
this.triggerEditorEvent('enterSource');
if (!this._initiated) {
this._initiated = true;
if (!WPro.isIE) {
this._writeDocument(this.tempStr);
this.tempStr = null;
this._enableDesignMode();
}
this.hideLoadMessage();
}
this._movingToSource = false;
WPro.timer.addTimer('WPro.'+this._internalId+'._selectSelection()', 100);
WPro.timer.addTimer('WPro.'+this._internalId+'.toggleWordWrap(true)', 50);
} else {
WPro.timer.addTimer('WPro.' + this._internalId + '._updateHTMLTimeout()',400);
}
}
wproEditor.prototype.syntaxHighlight = function (html) {
html = WPro.htmlSpecialChars(html);
html = html.replace("|wproSelectionStart|", '<span id="selection">').replace("|wproSelectionEnd|", '</span>');
html=html.replace(/(&lt;(p|div)><span id=\"selection\"><\/span>)(&lt;\/(p|div)>)/gi, "$1&amp;nbsp;$3");
var htmlTag = /(&lt;([\s\S]*?)&gt;)/gi
var styleTag = /(&lt;(style|\/style)([\s\S]*?)&gt;)/gi
var tableTag = /(&lt;(table|tbody|th|tr|td|\/table|\/tbody|\/th|\/tr|\/td)([\s\S]*?)&gt;)/gi
var formTag = /(&lt;(form|input|textarea|button|select|option|\/form|\/input|\/textarea|\/button|\/select|\/option)([\s\S]*?)&gt;)/gi
var commentTag = /(&lt;!--([\s\S]*?)--&gt;)/gi
var imageTag = /(&lt;img([\s\S]*?)&gt;)/gi
var linkTag = /(&lt;(a|\/a)([\s\S]*?)&gt;)/gi
var scriptTag = /(&lt;(script|\?[^x]|%|\/script)([\s\S]*?)&gt;)/gi
var objectTag = /(&lt;(applet|object|\/applet|\/object)([\s\S]*?)&gt;)/gi
var attributeValue = /(=)(&quot;([\s\S]*?)&quot;)/gi
var specChar = /(&amp;[^;]+;)/gi
html = html.replace(htmlTag, "<span class=\"htmlTag\">$1</span>").replace(styleTag, "<span class=\"styleTag\">$1</span>").replace(formTag, "<span class=\"formTag\">$1</span>").replace(tableTag, "<span class=\"tableTag\">$1</span>").replace(commentTag, "<span class=\"commentTag\">$1</span>").replace(imageTag, "<span class=\"imageTag\">$1</span>").replace(linkTag, "<span class=\"linkTag\">$1</span>").replace(scriptTag, "<span class=\"scriptTag\">$1</span>").replace(objectTag, "<span class=\"objectTag\">$1</span>").replace(attributeValue, "$1<span class=\"attributeValue\">$2</span>").replace(specChar, "<span class=\"character\">$1</span>");
html = html.replace(/\n/gi, '<br>');
html = this.triggerHTMLFilter('syntax', html);
return html;
}
wproEditor.prototype.syntaxHighlightClicked = function () {
this.showLoadMessage();
WPro.timer.addTimer('WPro.'+this._internalId+'._syntaxHighlightClicked()', 1);
}
wproEditor.prototype._syntaxHighlightClicked = function () {
if (this._inSource) {
str = this.getText();
str = this.syntaxHighlight( str );
this.editDocument.body.innerHTML = str;
}
this.hideLoadMessage();
}
wproEditor.prototype.getText = function () {
return WPro.getNodeText(this.editDocument.body).replace(/\xA0/gi, ' ');
}
wproEditor.prototype.updateDesign = function () {
if (this._inSource) {
this.textarea.value = this.getText();
}
var str = this.textarea.value;
this._setSnippetStatus(str);
str = this._buildPreviewHTML(str,true);
if (!this._initiated) {
if (WPro.isIE&&WPro.browserVersion<7) {
this._enableDesignMode();
}
}
str = this.triggerHTMLFilter('design',str);
this._writeDocument(str);
if (!this._initiated) {this.tempStr = str;}
this._updateDesignTimeout();
}
wproEditor.prototype.updateWysiwyg = wproEditor.prototype.updateDesign
wproEditor.prototype._updateDesignTimeout = function () {
if (this.editDocument.body) {
this._addEditDocumentEvents();
this.triggerEditorEvent('enterDesign');
if (!this._initiated) {
this._initiated = true;
if (!WPro.isIE) {
this._writeDocument(this.tempStr);
this.tempStr = null;
this._enableDesignMode();
}
this.hideLoadMessage();
} else {
this.history.add(true);
}
if (!this._loaded) {
this.triggerEditorEvent('load');
this._loaded = true;
}
this.redrawTimeout();
this._movingToDesign = false;
} else {
WPro.timer.addTimer('WPro.' + this._internalId + '._updateDesignTimeout()', 400);
}
}
wproEditor.prototype.showDesign = function () {
if (!this._inDesign && !this._movingToDesign && !this._movingToSource && !this._movingToPreview) {
this.showLoadMessage();
this._movingToDesign = true;
WPro.timer.addTimer("WPro."+this._internalId+"._showDesign()",1);
}
this.closePMenu();
}
wproEditor.prototype._showDesign = function () {
this.sourceToolbar.style.display = 'none';
this.designToolbar.style.display = 'block';
this.editFrame.style.height = this.designFrameHeight;
this.previewTab.className = 'wproHiddenTab';
this.designTab.className = 'wproVisibleTab';
if (this.tagPath)this.tagPathHolder.style.display='block';
if (this.designTabButton)
this.designTabButton.className='wproTButtonUp';
if (this.sourceTabButton)
this.sourceTabButton.className='wproTButtonDown';
if (this.previewTabButton)
this.previewTabButton.className='wproTButtonDown';
this.updateDesign();
this._inDesign = true;
this._inSource = false;
this._inPreview = false;
this.hideLoadMessage();
}
wproEditor.prototype.showSource = function () {
if (!this._inSource && !this._movingToDesign && !this._movingToSource && !this._movingToPreview) {
this.history.add();
this.showLoadMessage();
this._movingToSource = true;
WPro.timer.addTimer("WPro."+this._internalId+"._showSource()",1);
}
this.closePMenu();
}
wproEditor.prototype.showCode = wproEditor.prototype.showSource
wproEditor.prototype._showSource = function () {
this.sourceToolbar.style.display = 'block';
this.designToolbar.style.display = 'none';
this.editFrame.style.height = this.sourceFrameHeight;
this.previewTab.className = 'wproHiddenPreviewTab';
this.previewTab.className = 'wproHiddenTab';
this.designTab.className = 'wproVisibleTab';
if (this.tagPath)this.tagPath.kill();
if (this.designTabButton)
this.designTabButton.className='wproTButtonDown';
if (this.sourceTabButton)
this.sourceTabButton.className='wproTButtonUp';
if (this.previewTabButton)
this.previewTabButton.className='wproTButtonDown';
this.updateValue();
this._inDesign = false;
this._inSource = true;
this._inPreview = false;
this.hideLoadMessage()
}
wproEditor.prototype.showPreview = function () {
if (!this._inPreview && !this._movingToDesign && !this._movingToSource && !this._movingToPreview) {
this.showLoadMessage();
this._movingToPreview = true;
WPro.timer.addTimer("WPro."+this._internalId+"._showPreview()",1);
}
this.closePMenu();
}
wproEditor.prototype._showPreview = function () {
if (WPro.isIE) {
this.designTab.className='wproHiddenTab';
} else {
this.designTab.className='wproHiddenDesignTab';
}
this.previewTab.className='wproVisibleTab';
this.previewFrame.style.height = this.previewFrameHeight;
if (this.tagPath)this.tagPath.kill();
if (this.designTabButton)
this.designTabButton.className='wproTButtonDown';
if (this.sourceTabButton)
this.sourceTabButton.className='wproTButtonDown';
if (this.previewTabButton)
this.previewTabButton.className='wproTButtonUp';
this.updatePreview();
this._inDesign = false;
this._inSource = false;
this._inPreview = true;
this._movingToPreview = false;
this.hideLoadMessage();
}
wproEditor.prototype.updatePreview = function () {
if (!this._inPreview&&!this._movingToPreview) {
return;
}
var str = this.getPreviewHTML();
this.previewWindow.document.open('text/html', 'replace');
this.previewWindow.document.write( str );
this.previewWindow.document.close();
if (this._initFocus) {
this.previewWindow.focus();
}
this.triggerEditorEvent('enterPreview');
if (!this._loaded) {
this.triggerEditorEvent('load');
this._loaded = true;
}
}
wproEditor.prototype.focus = function () {
wp_current_obj = this;
WPro.currentEditor = this;
if (this._inDesign) {
this.editWindow.focus();
} else if (this._inSource) {
this.editWindow.focus();
} else if (this._inPreview) {
this.previewWindow.focus();
}
}
wproEditor.prototype.moveFocus = wproEditor.prototype.focus
wproEditor.prototype.getDialogPluginURL = function (url) {
var url = WPro.editorLink('dialog.php?dialog=' + url + '&' + this.sid + (WPro.phpsid ? '&' + WPro.phpsid : '') + (this.appendToQueryStrings ? '&' + this.appendToQueryStrings : ''));
return url;
}
wproEditor.prototype.openDialogPlugin = function (url, width, height, features, modal, indialog, openerID) {
var url = this.getDialogPluginURL(url);
var frame = false;
if (this.iframeDialogs) {
if (indialog) {
this.frameCount++
var f = document.getElementById(this._internalId+'_dialogFrame').cloneNode(false);
f.style.display='none';
f.id=this._internalId+'_dialogFrame_'+this.frameCount;
document.getElementById(this._internalId+'_dialogFrame').parentNode.appendChild(f);
frame = this._internalId+'_dialogFrame_'+this.frameCount;
url += '&dialogFrameID='+this.frameCount;
if (typeof(openerID)!='undefined') {
url += '&dialogOpenerID='+openerID;
}
} else {
frame = this._internalId+'_dialogFrame'
}
url+='&iframe=true';
}
this.openDialog(url, modal, width, height, features, frame, openerID);
}
wproEditor.prototype.openDialog = function (url, modal, width, height, features, iframe, openerID) {
this.focus();
if (iframe && (!modal || modal=='modal')) this.preventInteraction();
wp_openDialog(url, modal, width, height, features, iframe, openerID);
}
wproEditor.prototype.showTagEditor = function (usetagpath, tagIndex) {
var node;
if (usetagpath) {
if (this.tagPath.tags[tagIndex]) {
node = this.tagPath.tags[tagIndex];
this.tagPath.selectedNode = node;
}
}
if (!node) {
usetagpath = false;
var range = this.selAPI.getRange();
var tagName = '';
if (range.type=='control') {
node = range.nodes[0];
} else {
if (this.tagPath) {
if (this.tagPath.selectedNode) {
node = this.tagPath.selectedNode;
}
}
if (!node) {
node = range.getCommonAncestorContainer();
}
}
}
tagName = node.tagName;
if (tagName == 'IMG' && node.className.match(/wproFilePlugin/i)) {
if (!/('object'\:\{|\%27object\%27\%3A\%7B)/i.test(String(node.getAttribute("_wpro_media_data")))) {
tagName = 'EMBED';
} else {
tagName = 'OBJECT';
}
}
if (tagName) {
this.openDialogPlugin ('wproCore_tagEditor&tagName='+tagName, 600, 375);
}
}
wproEditor.prototype.deleteNodeClicked = function () {
this.deleteSelectedNodes();
if (this.tagPath) this.tagPath.build();
}
wproEditor.prototype.removeNodeClicked = function () {
this.removeSelectedNodes();
if (this.tagPath) this.tagPath.build();
}
wproEditor.prototype.deleteSelectedNodes = function () {
var range = this.selAPI.getRange();
var tagName = '';
if (range.type=='control') {
n = range.nodes.length
for (var i=0;i<n;i++) {
range.nodes[i].parentNode.removeChild(range.nodes[i]);
}
} else {
node = range.getCommonAncestorContainer();
if (node.tagName != 'BODY') node.parentNode.removeChild(node);
}
}
wproEditor.prototype.removeSelectedNodes = function () {
var range = this.selAPI.getRange();
var tagName = '';
if (range.type=='control') {
n = range.nodes.length
for (var i=0;i<n;i++) {
WPro.removeNode(range.nodes[i]);
}
} else {
node = range.getCommonAncestorContainer();
WPro.removeNode(node);
}
}
wproEditor.prototype.toggleWordWrap = function (wrap) {
if (this._inSource) {
var b = document.getElementById(this._internalId+'_wordWrapButton');
var bo = this.editDocument.getElementsByTagName('BODY').item(0);
if (wrap) {
if (this._wordWrap) {
this._wordWrap = false;
} else {
this._wordWrap = true;
}
}
if (this._wordWrap) {
bo.style.whiteSpace = '';
bo.style.backgroundImage = '';
bo.style.margin = '';
b.className = 'wproReady';
this._wordWrap = false;
} else {
bo.style.whiteSpace = 'normal';
bo.style.backgroundImage = 'none';
bo.style.margin = '3px';
b.className = 'wproLatched';
this._wordWrap = true;
}
}
}
wproEditor.prototype.toggleGuidelines = function () {
if (this._guidelines == true) {
if (this._inDesign) {
this.hideGuidelines(true);
}
this._guidelines = false
} else {
if (this._inDesign) {
this.showGuidelines(true);
}
this._guidelines = true
}
}
wproEditor.prototype.showGuidelines = function (change) {
if (change) {
this._guidelines = true;
if (this.guidelinesButton) {
this.guidelinesButton.className = 'wproLatched'
}
}
if ((this._inDesign||this._movingToDesign)&&this._guidelines) {
this._hideGuideOnTags('TABLE');
this._hideGuideOnTags('FORM');
this._hideGuideOnTags('A');
this._showGuideOnTags('TABLE');
this._showGuideOnTags('FORM');
this._showGuideOnTags('A');
}
}
wproEditor.prototype._showGuideOnTags = function (name) {
var t = this.editDocument.getElementsByTagName(name);
var l = t.length
for (var i = 0; i < l; i++) {
c = t[i].className.toString()
c = c.replace(/[\s]*wproGuide[\s]*/gi, '');
switch (name) {
case 'TABLE':
var v = t[i].getAttribute('border');
if (!v || v==0) {
t[i].className = ('wproGuide ' + c);
}
break;
case 'A':
var v = t[i].getAttribute('name');
var v2 = t[i].getAttribute('id');
if (v || v2) {
t[i].className = ('wproGuide ' + c);
}
if (t[i].innerHTML == '') {
var s = this.editDocument.createTextNode(String.fromCharCode(160));
t[i].appendChild(s);
}
break;
default:
t[i].className = ('wproGuide ' + c);
break;
}
}
}
wproEditor.prototype.hideGuidelines = function (change) {
if (change) {
this._guidelines = false;
if (this.guidelinesButton) {
this.guidelinesButton.className = 'wproReady'
}
}
if (this._inDesign) {
this._hideGuideOnTags('TABLE');
this._hideGuideOnTags('FORM');
this._hideGuideOnTags('A');
}
}
wproEditor.prototype._hideGuideOnTags = function (name) {
var t = this.editDocument.getElementsByTagName(name);
var l = t.length
for (var i = 0; i < l; i++) {
c = t[i].className.toString()
c = c.replace(/[\s]*wproGuide[\s]*/gi, '');
t[i].className = c;
if (name == 'A') {
if (t[i].innerHTML == '&nbsp;') {
t[i].removeChild(t[i].firstChild);
}
}
}
}
wproEditor.prototype._startDragResize = function (e) {
this._inDragResize = true;
this._dragOrigY = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
this._dragOrigHeight = Math.abs(this.editorborder.offsetHeight);
this.editFrame.style.visibility = 'hidden';
this.previewFrame.style.visibility = 'hidden';
WPro.events.addEvent(document,'mousemove',eval("WPro."+this._internalId+"._e_onDragResize"));
WPro.events.addEvent(document,'mouseup',eval("WPro."+this._internalId+"._e_stopDragResize"));
}
wproEditor.prototype._stopDragResize = function () {
this._inDragResize = false;
this.editFrame.style.visibility = '';
this.previewFrame.style.visibility = '';
WPro.events.removeEvent(document,'mousemove',eval("WPro."+this._internalId+"._e_onDragResize"));
WPro.events.removeEvent(document,'mouseup',eval("WPro."+this._internalId+"._e_stopDragResize"));
}
wproEditor.prototype._onDragResize = function (e) {
if (this._inDragResize) {
var mousePosY = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
var diffY = mousePosY - this._dragOrigY;
var height = this._dragOrigHeight + diffY
this.resizeTo(this.specifiedWidth,height);
}
}
wproEditor.prototype.resizeTo = function (width, height) {
if (this._inDesign) {
this._realToolbarHeight = this.designToolbar.offsetHeight;
}
if (width.toString().search("%") == -1 && width.toString().search("px") == -1 && width != '') {
width = width + 'px';
}
this.container.style.width = width;
var id = this._internalId;
var th = this.designToolbar.getElementsByTagName('DIV');
var tabHeight = (document.getElementById(id + '_tabHolder').offsetHeight) ? (document.getElementById(id + '_tabHolder').offsetHeight) : 20;
var toolbarHeight = this._realToolbarHeight ? this._realToolbarHeight : (this.designToolbar.offsetHeight ? this.designToolbar.offsetHeight : ((this.toolbarHeight * th.length) +0));
if ( (height - (toolbarHeight + tabHeight)) < 50) {
height = (toolbarHeight+tabHeight+50);
}
this.designFrameHeight = (height - (toolbarHeight + tabHeight + 3));
if (this.tagPath)this.designFrameHeight	-= (this.tagPath.offsetHeight ? this.tagPath.offsetHeight : 21);
this.designFrameHeight += 'px';
this.sourceFrameHeight = (height - (this.toolbarHeight + tabHeight + 3)) + 'px';
this.previewFrameHeight = (height - (this.toolbarHeight + tabHeight + 3)) + 'px';
if (this._inPreview) {
this.previewFrame.style.height = this.previewFrameHeight;
} else if (this._inSource) {
this.editFrame.style.height = this.sourceFrameHeight;
} else {
this.editFrame.style.height = this.designFrameHeight;
}
wproResizeLoadMessageTo(this._internalId, width, height);
}
wproEditor.prototype.resetDimensions = function () {
this.resizeTo(this.specifiedWidth, this.specifiedHeight);
}
wproEditor.prototype.getStyles = function () {
var styles = ''
if (this.baseURL) {
styles += '<base href="'+this.baseURL+'">';
}
var stylesheets = this.editDocument.getElementsByTagName('link')
var l=stylesheets.length
for (var i=0; i < l; i++) {
if (stylesheets[i].href) {
if (stylesheets[i].rel) {
if (stylesheets[i].rel.toLowerCase() == "stylesheet") {
styles += '<link rel="stylesheet" href="'+ stylesheets[i].href +'" type="text/css">'
}
} else if (stylesheets[i].type) {
if (stylesheets[i].type.toLowerCase() == "text/css") {
styles += '<link rel="stylesheet" href="'+ stylesheets[i].href +'" type="text/css">'
}
}
}
}
var styleTags = this.editDocument.getElementsByTagName('style')
var l=styleTags.length
for (var i=0; i < l; i++) {
styles += '<style type="text/css">'+ styleTags[i].innerHTML +'</style>';
}
if (this.fragmentCSS) {
styles += '<style type="text/css">'+ this.fragmentCSS +'</style>'
}
if (this.fragmentStylesheet) {
styles += '<link rel="stylesheet" href="'+ this.fragmentStylesheet +'" type="text/css">'
}
return styles
}
wproEditor.prototype.callFormatting = function (sFormatString, sValue) {
this.history.add();
this.focus();
sFormatString = sFormatString.toLowerCase();
if (WPro.isGecko&&sFormatString=='backcolor') sFormatString='hilitecolor';
if (!sValue) {
sValue = null;
}
var beyondBlock = /^(blockquote|table|tr|td|th|ul|ol)$/i
var other = /^(b|strong|i|em|u|sup|sub|strike)$/i
var range = this.selAPI.getRange();
if (!WPro.isIE) {
WPro.callCommand(this.editDocument, "usecss", false, false);
WPro.callCommand(this.editDocument, "styleWithCss", false, true);
}
if (this.formattingHandlers[sFormatString.toLowerCase()]) {
this.formattingHandlers[sFormatString.toLowerCase()](this, sFormatString.toLowerCase(), sValue);
} else if (sFormatString == "formatblock" && (beyondBlock.test(sValue.replace(/[><]/gi, '')) || other.test(sValue.replace(/[><]/gi, '')) ) ) {
var tagName = sValue.replace(/[><]/gi, '').toUpperCase();
if (tagName == 'BLOCKQUOTE' && !range.getContainerByTagName('BLOCKQUOTE') ) {
WPro.callCommand(this.editDocument, "indent", false, null);
} else if (tagName == 'UL' && !range.getContainerByTagName('UL') ) {
WPro.callCommand(this.editDocument, "insertunorderedlist", false, null);
} else if (tagName == 'OL' && !range.getContainerByTagName('OL') ) {
WPro.callCommand(this.editDocument, "insertorderedlist", false, null);
} else if (tagName=='B'||tagName=='STRONG') {
WPro.callCommand(this.editDocument, "bold", false, null);
} else if (tagName=='I'||tagName=='EM') {
WPro.callCommand(this.editDocument, "italic", false, null);
} else if (tagName=='U') {
WPro.callCommand(this.editDocument, "underline", false, null);
} else if (tagName=='SUP') {
WPro.callCommand(this.editDocument, "superscript", false, null);
} else if (tagName=='SUB') {
WPro.callCommand(this.editDocument, "subscript", false, null);
} else if (tagName=='STRIKE') {
WPro.callCommand(this.editDocument, "strikethrough", false, null);
} else {
return;
}
} else if (sFormatString == 'hilitecolor') {
if (!sValue) {
sFormatString = 'removeformat';
}
WPro.callCommand(this.editDocument, "usecss", false, false);
WPro.callCommand(this.editDocument, "styleWithCss", false, true);
WPro.callCommand(this.editDocument, sFormatString, false, sValue);
WPro.callCommand(this.editDocument, "usecss", false, true);
WPro.callCommand(this.editDocument, "styleWithCss", false, false);
} else if (sFormatString == "undo") {
this.history.undo();
return;
} else if (sFormatString == "redo") {
this.history.redo();
return;
} else if (sFormatString == "cut" && WPro.isGecko) {
var range = this.selAPI.getRange();
WPro.clipBoard = range.getHTMLText();
range.deleteContents();
} else if (sFormatString == "copy" && WPro.isGecko) {
WPro.clipBoard = this.selAPI.getRange().getHTMLText();
} else if (sFormatString == "paste" && WPro.isGecko) {
if (WPro.clipBoard) {
this.insertAtSelection (WPro.clipBoard);
}
} else {
if (!sValue) {
if (sFormatString == "fontsize"|| sFormatString == "fontname"|| sFormatString == "backcolor") {
sFormatString = 'removeformat';
}
}
if (sFormatString == "formatblock" && WPro.isIE && sValue.match(/^[a-z0-9]*$/i)) {
sValue = '<'+sValue+'>';
}
WPro.callCommand(this.editDocument, sFormatString, false, sValue);
}
if (sFormatString == 'removeformat') {
var range = this.selAPI.getRange();
if (range.type == 'control') {
for (var i=0; i<range.nodes.length; i++) {
WPro.stripAttributes(range.nodes[i], false, /^(class|style)$/i);
}
} else {
var tags = [];
var container = range.getCommonAncestorContainer();
var p = WPro.getBlockParent(container)
if (!range.getContainerByTagName('LI') && !range.getContainerByTagName('BLOCKQUOTE') && p) {
this.applyStyle(this.lineReturns);
var range = this.selAPI.getRange();
var container = range.getCommonAncestorContainer();
}
var test = ['SPAN']
for (var i=0;i<test.length;i++) {
var node = false;
if (node=range.getContainerByTagName(test[i])) {
WPro.stripAttributes(node, false, /^(class|style)$/i);
break;
}
}
this.applyStyle('span');
}
}
if (WPro.isGecko) {
var range = this.selAPI.getRange();
p = range.getBlockContainer();
var t = p.tagName;
if (t == 'BODY') {
WPro.callCommand(this.editDocument, 'formatblock', false, ((this.lineReturns=='div') ? 'DIV' : 'P') )
}
}
this.history.add();
this.setButtonStates();
}

wproEditor.prototype._applyStyle_font2Span = function (node, tagName, attributes, origElm, retArr, nested) {
var fonts = node.getElementsByTagName("FONT")
var n = fonts.length
var j = 0
for (var i = 0; i < n; i++) {
if (fonts[j]) {
if (fonts[j].getAttribute('face') == "wp_bogus_font") {
var newNode = this.editWindow.document.createElement(tagName)
WPro.addAttributes(newNode, attributes, origElm)
this._applyStyle_font2Span(fonts[j], tagName, attributes, origElm, retArr, true)
WPro.setInnerHTML(newNode, fonts[j].innerHTML);
fonts[j].parentNode.insertBefore(newNode, fonts[j].nextSibling)
fonts[j].parentNode.removeChild(fonts[j]);
retArr.push(newNode);
} else {
j++
}
} else {
j++
}
}
}
wproEditor.prototype._applyStyle_resolveNesting = function (node, tagName, attributes, origElm, spans) {
n = spans.length;
var j = 0;
for (var i=0;i<n;i++) {
if (spans[j]) {
var s = spans[j].getElementsByTagName(tagName);
var n2 = s.length;
var l = 0;
for (var k=0;k<n2;k++) {
if (s[l]) {
var node = s[l]
var pnode = node.parentNode;
var cn = node.childNodes;
for (var m=0; m<cn.length; m++) {
pnode.insertBefore(cn[m].cloneNode(true), node );
}
pnode.removeChild(node);
} else {
l++;
}
}
var p = spans[i].parentNode
if (p.tagName) {
if (p.tagName == tagName) {
var cn = p.childNodes;
if (cn.length == 1) {
WPro.stripAttributes(p);
WPro.addAttributes(p, attributes, origElm)
var cn = spans[j].childNodes;
for (var m=0; m<cn.length; m++) {
p.appendChild(cn[m].cloneNode(true));
}
p.removeChild(spans[j]);
}
}
}
j++;
}
}
}
wproEditor.prototype.applyStyle = function (elm, nodes, swapNode, preserveAttrs) {
var UDBeforeState = this.history.pre();
if (preserveAttrs) {
var preserveAttrs = /^(a-z0-9)$/i
} else {
if (this.preserveAttributes) {
var preserveAttrs = /^(table|hr|tr|td|th|a|img|object|embed)$/i
} else {
var preserveAttrs = /^(a|img|object|embed)$/i
}
}
var attrsKeep = /^(id|name|title|alt|value|colspan|rowspan|for|scope|target|codetype|type|archive|standby|declare|usemap|data|href|src|action|longdesc|profile|usemap|background|cite|classid|codebase)$/i
var attrsKill = /^(class)$/i
var beyondBlock = /^(blockquote|table|tr|td|th|ul|ol|div)$/i
var genericBlock = false;
if (typeof(elm) == 'string') {
if (elm.substr(0, 7)=='*block*') {
genericBlock = true;
var d = this.editDocument.createElement('DIV');
var a = elm.replace(/^\*block\*([^>]*?)$/gi, "$1");
d.innerHTML = '<div '+a+'>&nbsp;</'+i+'>';
elm = d.firstChild;
} else {
var d = this.editDocument.createElement('DIV');
var a = elm.replace(/^[a-z0-9]+([^>]*?)$/gi, "$1");
var i = elm.replace(/^([a-z0-9]+)[^>]*?$/gi, "$1");
d.innerHTML = '<'+i+' '+a+'>&nbsp;</'+i+'>';
elm = d.firstChild;
}
}
if (!elm) return;
if (!elm.tagName) return;
elm.style.position = '';
var attrs = elm.attributes;
var tagName = elm.tagName.toUpperCase();
var range = this.selAPI.getRange();
if (/^(abbr|acronym|b|bdo|big|cite|code|dfn|em|font|i|kbd|label|nobr|q|s|samp|small|span|strike|strong|sub|sup|tt|u|var)$/i.test(tagName) && !nodes) {
if (range.toString().length == 0 && range.type != 'control') {
var s = this.editDocument.createElement(tagName);
WPro.addAttributes(s, attrs, elm)
if (WPro.isIE) {
range.insertNode(s,true);
} else {
range.insertNode(s);
range.selectNodeContents(s);
}
range.select();
} else {
WPro.callCommand(this.editDocument, "FontName", false, "wp_bogus_font");
var spans = [];
this._applyStyle_font2Span(this.editDocument.body, tagName, attrs, elm, spans, false);
this._applyStyle_resolveNesting(this.editDocument.body, tagName, attrs, elm, spans);
}
} else if (tagName == 'A' && !nodes) {
var p = range.getContainerByTagName('A');
if (p) {
if (!preserveAttrs) WPro.stripAttributes(p, attrsKeep, attrsKill);
WPro.addAttributes(p, attrs, elm);
} else if (range.type == "control") {
var nodes = range.nodes;
var num = nodes.length;
for (var i=0; i < num; i++) {
if (nodes[i].tagName == 'A') {
if (!preserveAttrs) WPro.stripAttributes(nodes[i], attrsKeep, attrsKill);
WPro.addAttributes(nodes[i], attrs, elm);
}
}
}
} else if (range.type == "control" || nodes) {
var x = false
var doSwap = false;
if (!nodes) {
var nodes = range.nodes;
x = true
}
if (swapNode) {
doSwap = true;
} else {
if (tagName=='TD'||tagName=='TH') {
swapNode = /^(TD|TH)$/i
} else {
swapNode = /^XXXXXX$/
}
}
var num = nodes.length;
for (var i=0; i < num; i++) {
if (!WPro.isIE && (tagName=='TR'||tagName=='TABLE') && x) {
var pNode = WPro.getParentNodeByTagName(nodes[i], tagName)
if (pNode) {
WPro.addAttributes(pNode,attrs,elm);
if (tagName=='TABLE') {
break;
}
}
} else if ((nodes[i].tagName == tagName) || (doSwap && swapNode.test(tagName))) {
if (!preserveAttrs.test(tagName)) {
if (!preserveAttrs) WPro.stripAttributes(nodes[i], attrsKeep);
} else {
if (!preserveAttrs) WPro.stripAttributes(nodes[i], attrsKeep, attrsKill);
}
WPro.addAttributes(nodes[i],attrs,elm);
if (doSwap && swapNode.test(tagName)) {
var ne = this.editDocument.createElement(tagName);
WPro.addAttributes(ne, nodes[i].attributes, nodes[i]);
if (nodes[i].childNodes) {
var cn = nodes[i].childNodes;
for (var i=0; i<cn.length; i++) {
ne.appendChild(cn[i].cloneNode(true));
}
}
nodes[i].parentNode.insertBefore(ne, nodes[i].nextSibling);
nodes[i].parentNode.removeChild(nodes[i]);
}
} else if (nodes[i].childNodes) {
var f = nodes[i].getElementsByTagName(tagName)
n = f.length;
for (var j=0; j<n; j++) {
if (!preserveAttrs.test(tagName)) {
if (!preserveAttrs) WPro.stripAttributes(f[j], attrsKeep);
} else {
if (!preserveAttrs) WPro.stripAttributes(f[j], attrsKeep, attrsKill);
}
WPro.addAttributes(f[j],attrs,elm);
}
}
}
} else {
if (!genericBlock) {
this.callFormatting("FormatBlock", tagName);
}
var range = this.selAPI.getRange();
var startRange = range.cloneRange()
startRange.collapse(true);
if ((beyondBlock.test(tagName) || WPro.inline_tags.test(tagName)) && !genericBlock) {
var parentTag = startRange.getContainerByTagName(tagName)
} else {
var parentTag = startRange.getBlockContainer();
}
if (parentTag.tagName == 'BODY') {
if (this.lineReturns == 'div') {
this.callFormatting("FormatBlock", 'div');
} else {
this.callFormatting("FormatBlock", 'p');
}
var range = this.selAPI.getRange();
var startRange = range.cloneRange()
startRange.collapse(true);
var parentTag = startRange.getBlockContainer();
}
var endRange = range.cloneRange();
endRange.collapse(false);
if ((beyondBlock.test(tagName) || WPro.inline_tags.test(tagName)) && !genericBlock) {
var endTag = endRange.getContainerByTagName(tagName)
} else {
var endTag = endRange.getBlockContainer();
}
if (endTag.tagName == 'BODY') {
if (this.lineReturns == 'div') {
this.callFormatting("FormatBlock", 'div');
} else {
this.callFormatting("FormatBlock", 'p');
}
var range = this.selAPI.getRange();
var endRange = range.cloneRange();
endRange.collapse(false);
var endTag = endRange.getBlockContainer();
}
if (genericBlock) {
var f = this.editDocument.getElementsByTagName("body")[0].getElementsByTagName("*");
} else {
var f = this.editDocument.getElementsByTagName(tagName)
}
n = f.length;
var foundStart = false;
for (var i=0; i<n; i++) {
if (genericBlock) {
if (!f[i].tagName) continue;
if (WPro.inline_tags.test(f[i].tagName)) continue;
}
if (f[i] == parentTag) {
foundStart = true;
}
if (foundStart) {
if (!preserveAttrs.test(tagName)) {
WPro.stripAttributes(f[i], attrsKeep);
} else {
WPro.stripAttributes(f[i], attrsKeep, attrsKill);
}
WPro.addAttributes(f[i],attrs,elm);
}
if (f[i] == endTag) {
break;
}
}
}
this.history.post(UDBeforeState);
}
wproEditor.prototype.print = function () {
if (this._inPreview) {
this.previewWindow.focus();
this.previewWindow.print();
} else {
this.editWindow.focus();
this.editWindow.print();
}
}
wproEditor.prototype._enableDesignMode = function () {
if (!this._inPreivew) {
try {
if (WPro.isIE&&WPro.browserVersion>=7) {
this.editWindow.document.body.contentEditable = true;
} else {
this.editWindow.document.designMode = 'on'
}
if (WPro.isIE) {
} else {
WPro.callCommand(this.editDocument, "usecss", false, true);
WPro.callCommand(this.editDocument, "styleWithCss", false, false);
}
}catch(e){};
}
}
wproEditor.prototype._sessTimeout = function () {
wpro_sessTimeout (this._internalId, this.sid, WPro.phpsid, WPro.URL, this.appendToQueryStrings, this.sessRefresh, WPro.route);
}
wproEditor.prototype._createSessTag = function (action) {
wpro_createSessTag (action, this._internalId, this.sid, WPro.phpsid, WPro.URL, this.appendToQueryStrings, WPro.route)
}
wproEditor.prototype.showContextMenu = function (evt) {
var position = WPro.getElementPosition(this.editFrame)
var posX = evt.clientX + position['left']
var posY = evt.clientY + position['top']
if (this._inSource) {var allowed =/^(cut|copy|paste|selectall)$/i} else {var allowed = null}
this.showFloatingMenu(this.contextMenu, posX, posY, allowed);
}
wproEditor.prototype.showButtonMenu = function (button, items) {
if(button.className=='wproDisabled')return;
this._mDown(button);
var position = WPro.getElementPosition(button);
var posX = position['left']
var posY = position['top'] + button.offsetHeight
this.showFloatingMenu(items, posX, posY);
}
wproEditor.prototype._addListMenuContent = function (func, i, content) {
var title = i ? '&lt;'+i.replace(/\"/gi,'&quot;')+'&gt;' : '';
return '<div class="wproOff" onmouseover="on(this)" onmouseout="off(this)" onclick="parent.WPro.'+this._internalId+'.PMenu.reselectRange();'+func+'" title="'+title+'">'+content+'</div>';
}
wproEditor.prototype._addListMenuHeading = function (text) {
return '<div class="wproHeading">' + text + '</div>'
}
wproEditor.prototype.showListMenu = function (button, menu) {
button.className='wproDown wproDropDown';
var frame = eval('WPro.'+this._internalId+'.'+menu+'PMenuMenu');
var iCount = 0;
if (frame == this.PMenu.PMenu) {
this.closePMenu();
return;
}
var cstyles = '<style type="text/css">';
cstyles += 'body{margin:0px;border:0px;}';
cstyles += '#wproMContainer img {position:static;width:20px;height:20px;vertical-align:middle}';
cstyles += 'div.wproHeading{position:static;display:block;padding:4px 1px 1px;margin:0px;background:#999999;color:#ffffff;font-family:verdana;font-size:12px;font-weight:bold;clear:both}';
cstyles += '#wproMContainer{position:static;overflow:hidden;display:block;white-space:nowrap;width:100%;margin:0px;padding:0px;border:0px}';
cstyles += '.wproOff{position:static;display:block;clear:both;overflow:hidden;cursor:pointer;cursor:hand;}';
cstyles += '.wproOn{position:static;display:block;clear:both;margin:0px;overflow:hidden;cursor:pointer;cursor:hand;}';
cstyles += '.noStyles {font-family:verdana;font-size:11px;color:#000000;background-color:#ffffff;text-align:center}';
cstyles += 'table{width:98%}';
cstyles += '.wproOff p, .wproOn p{margin:0px}';
cstyles += '.wproOff input, .wproOff button, .wproOff select, .wproOff form{display:none}';
cstyles += '.wproOn input, .wproOn button, .wproOn select, .wproOn form{display:none}';
cstyles += '</style>';
var styles1 = '<style type="text/css">body{padding:0px;}.wproOff{border:1px solid #eeeeee;margin:2px;padding:2px;}.wproOn{border:3px solid highlight;padding:0px;margin:2px;}</style>';
var styles2 = '<style type="text/css">body{padding:3px;background:#ffffff;color:#000000;}.wproOff{color:#000000;background:#ffffff}.wproOn{color:highlighttext;background:highlight}</style>';
var head = '<style type="text/css">body{background-color:#ffffff;}</style>'+this.getStyles()+'<script type="text/javascript">function on (elm) {elm.className="wproOn";} function off (elm) {elm.className="wproOff";}</script>' + cstyles;
var str = '';
var width;
switch (menu) {
case 'styles':
var range = this.selAPI.getRange();
width = 350;
head += styles1;
var rangeType = range.type;
if (rangeType != 'control') {
var lr = (this.lineReturns == 'br') ? 'p' : this.lineReturns;
var script = 'parent.WPro.'+this._internalId+'.callFormatting(\'RemoveFormat\');';
str += this._addListMenuContent(script,'','<'+lr+'>'+this.lng['clearFormatting']+'</'+lr+'>');
}
var doTable = false;
if (rangeType == 'control' ) {
var t = range.nodes[0].tagName;
if (t == 'TABLE' || t == 'TD' || t == 'TH') {
doTable = true;
}
} else if (range.getContainerByTagName('TABLE')) {
doTable = true;
}
if (doTable) {
if (this.tableStyles) {
str += this._addListMenuHeading(this.lng['tableStyles']);
for (var i in this.tableStyles) {
str += this._addListMenuContent('parent.WPro.'+this._internalId+'.applyStyle(this.firstChild)', i, '<'+i+' style="position:static"><tbody><tr><td>'+this.tableStyles[i]+'</td></tr></tbody></'+i.replace(/ [^>]+/gi, '')+'>');
iCount++;
}
}
if (this.rowStyles) {
str += this._addListMenuHeading(this.lng['rowStyles']);
for (var i in this.rowStyles) {
str += this._addListMenuContent('parent.WPro.'+this._internalId+'.applyStyle(this.firstChild.firstChild.firstChild)', i, '<table><tbody><'+i+' style="position:static"><td>'+this.rowStyles[i]+'</td></'+i.replace(/ [^>]+/gi, '')+'></tbody></table>');
iCount++;
}
}
if (this.cellStyles) {
str += this._addListMenuHeading(this.lng['cellStyles']);
for (var i in this.cellStyles) {
str += this._addListMenuContent('parent.WPro.'+this._internalId+'.applyStyle(this.firstChild.firstChild.firstChild.firstChild)', i, '<table><tbody><tr><'+i+' style="position:static">'+this.cellStyles[i]+'</'+i.replace(/ [^>]+/gi, '')+'></tr></tbody></table>');
iCount++;
}
}
}
if (this.linkStyles) {
var doLink = false;
var p = range.getContainerByTagName('A');
if (p) {
doLink = true;
} else if (range.type == "control") {
var nodes = range.nodes;
var num = nodes.length;
for (var i=0; i < num; i++) {
if (nodes[i].tagName == 'A') {
doLink = true;
break;
}
}
}
if (doLink) {
str += this._addListMenuHeading(this.lng['linkStyles']);
for (var i in this.linkStyles) {
str += this._addListMenuContent('parent.WPro.'+this._internalId+'.applyStyle(this.firstChild)', i, '<'+i+' style="position:static">'+this.linkStyles[i]+'</'+i.replace(/ [^>]+/gi, '')+'>');
iCount++;
}
}
}
if (rangeType != 'control') {
if (this.paragraphStyles) {
str += this._addListMenuHeading(this.lng['paragraphStyles']);
for (var i in this.paragraphStyles) {
if (i.substr(0, 7)=='*block*') {
var a = i.replace(/^\*block\*([^>]*?)$/gi, "$1");
str += this._addListMenuContent('parent.WPro.'+this._internalId+'.applyStyle(\''+WPro.addSlashes(WPro.htmlSpecialChars(i))+'\')', i, '<p '+a+' style="position:static">'+this.paragraphStyles[i]+'</'+i.replace(/ [^>]+/gi, '')+'>');
} else {
str += this._addListMenuContent('parent.WPro.'+this._internalId+'.applyStyle(this.firstChild)', i, '<'+i+' style="position:static">'+this.paragraphStyles[i]+'</'+i.replace(/ [^>]+/gi, '')+'>');
}
iCount++;
}
}
if (this.textStyles) {
str += this._addListMenuHeading(this.lng['textStyles']);
for (var i in this.textStyles) {
str += this._addListMenuContent('parent.WPro.'+this._internalId+'.applyStyle(this.firstChild)', i, '<'+i+' style="position:static">'+this.textStyles[i]+'</'+i.replace(/ [^>]+/gi, '')+'>');
iCount++;
}
}
if (this.listStyles) {
str += this._addListMenuHeading(this.lng['listStyles']);
for (var i in this.listStyles) {
str += this._addListMenuContent('parent.WPro.'+this._internalId+'.applyStyle(this.firstChild)', i, '<'+i+' style="position:static"><li>'+this.listStyles[i]+'</li></'+i.replace(/ [^>]+/gi, '')+'>');
iCount++;
}
}
if (this.listItemStyles) {
str += this._addListMenuHeading(this.lng['listItemStyles']);
for (var i in this.listItemStyles) {
str += this._addListMenuContent('parent.WPro.'+this._internalId+'.applyStyle(this.firstChild)', i, '<'+i+' style="position:static">'+this.listItemStyles[i]+'</'+i.replace(/ [^>]+/gi, '')+'>');
iCount++;
}
}
} else {
var tagName = range.nodes[0].tagName
if (tagName == 'IMG') {
if (this.imageStyles) {
var filePlugin = range.nodes[0].className.match(/wproFilePlugin/i);
str += this._addListMenuHeading(this.lng['imageStyles']);
for (var i in this.imageStyles) {
var i2 = i.replace(/^([\w]+)/gi, "");
if (!i.match('alt=')) {
i2+=' alt=""';
}
if (!i.match('src=')) {
i2+=' src="'+this._domain+this.themeURL+'buttons/image.gif"';
}
if (filePlugin) {
if (i.match('class=')) {
i = i.replace(/ class="([^"]*)"/gi, " class=\"wproFilePlugin $1\"");
} else {
i += ' class="wproFilePlugin"';
}
}
str += this._addListMenuContent('parent.WPro.'+this._internalId+'.applyStyle(this.firstChild.firstChild)', i, '<span style="display:none"><'+i+'></span><img '+i2+' style="position:static">&nbsp;'+this.imageStyles[i]);
iCount++;
}
}
break;
} else if (tagName == 'HR') {
if (this.rulerStyles) {
str += this._addListMenuHeading(this.lng['rulerStyles']);
for (var i in this.rulerStyles) {
str += this._addListMenuContent('parent.WPro.'+this._internalId+'.applyStyle(this.firstChild)', i, '<'+i+'>'+this.rulerStyles[i]);
iCount++;
}
}
} else if (tagName == 'SELECT') {
if (this.selectStyles) {
str += this._addListMenuHeading(this.lng['listBoxStyles']);
for (var i in this.selectStyles) {
str += this._addListMenuContent('parent.WPro.'+this._internalId+'.applyStyle(this.firstChild)', i, '<'+i+' style="position:static"></'+i.replace(/ [^>]+/gi, '')+'>'+this.selectStyles[i]);
iCount++;
}
}
break;
} else if (tagName == 'TEXTAREA') {
if (this.textareaStyles) {
str += this._addListMenuHeading(this.lng['textBoxStyles']);
for (var i in this.textareaStyles) {
str += this._addListMenuContent('parent.WPro.'+this._internalId+'.applyStyle(this.firstChild)', i, '<'+i+' style="position:static"></'+i.replace(/ [^>]+/gi, '')+'>'+this.textareaStyles[i]);
iCount++;
}
}
break;
}
var doInput = false;
var inputType = '';
if (tagName == 'INPUT') {
doInput = true;
inputType = range.nodes[0].getAttribute('type').toString().toLowerCase();
}
if (doInput) {
if (inputType == 'text') {
if (this.textInputStyles) {
str += this._addListMenuHeading(this.lng['textFieldStyles']);
for (var i in this.textInputStyles) {
str += this._addListMenuContent('parent.WPro.'+this._internalId+'.applyStyle(this.firstChild)', i, '<'+i+' style="position:static">'+this.textInputStyles[i]);
iCount++;
}
}
} else if (inputType == 'button' || inputType == 'submit' || inputType == 'reset') {
if (this.buttonInputStyles) {
str += this._addListMenuHeading(this.lng['buttonStyles']);
for (var i in this.buttonInputStyles) {
str += this._addListMenuContent('parent.WPro.'+this._internalId+'.applyStyle(this.firstChild)', i, '<'+i+' style="position:static">'+this.buttonInputStyles[i]);
iCount++;
}
}
} else if (inputType == 'radio') {
if (this.radioInputStyles) {
str += this._addListMenuHeading(this.lng['optionButtonStyles']);
for (var i in this.radioInputStyles) {
str += this._addListMenuContent('parent.WPro.'+this._internalId+'.applyStyle(this.firstChild)', i, '<'+i+' style="position:static">'+this.radioInputStyles[i]);
iCount++;
}
}
} else if (inputType == 'checkbox') {
if (this.checkboxInputStyles) {
str += this._addListMenuHeading(this.lng['checkBoxStyles']);
for (var i in this.checkboxInputStyles) {
str += this._addListMenuContent('parent.WPro.'+this._internalId+'.applyStyle(this.firstChild)', i, '<'+i+' style="position:static">'+this.checkboxInputStyles[i]);
iCount++;
}
}
} else if (inputType == 'image') {
if (this.imageInputStyles) {
str += this._addListMenuHeading(this.lng['imageButtonStyles']);
for (var i in this.imageInputStyles) {
str += this._addListMenuContent('parent.WPro.'+this._internalId+'.applyStyle(this.firstChild)', i, '<'+i+' style="position:static">'+this.imageInputStyles[i]);
iCount++;
}
}
} else if (inputType == 'file') {
if (this.fileInputStyles) {
str += this._addListMenuHeading(this.lng['fileSelectStyles']);
for (var i in this.fileInputStyles) {
str += this._addListMenuContent('parent.WPro.'+this._internalId+'.applyStyle(this.firstChild)', i, '<'+i+' style="position:static">'+this.fileInputStyles[i]);
iCount++;
}
}
} else {
if (this.inputStyles) {
str += this._addListMenuHeading(this.lng['formInputStyles']);
for (var i in this.inputStyles) {
str += this._addListMenuContent('parent.WPro.'+this._internalId+'.applyStyle(this.firstChild)', i, '<'+i+' style="position:static">'+this.inputStyles[i]);
iCount++;
}
}
}
}
}
break;
case 'font':
width = 275;
head += styles2;
str += this._addListMenuContent('parent.WPro.'+this._internalId+'.callFormatting(\'fontname\', \'\')', '', '<font face="'+this.fontMenu[i]+'">'+this.lng['default']+'</font>');
var n = this.fontMenu.length;
for (var i=0;i<n;i++) {
str += this._addListMenuContent('parent.WPro.'+this._internalId+'.callFormatting(\'fontname\', \''+this.fontMenu[i]+'\')', '', '<span style="font-family:'+this.fontMenu[i]+'">'+this.fontMenu[i]+'</span>');
iCount++;
}
break;
case 'size':
width = 100;
head += styles2;
str += this._addListMenuContent('parent.WPro.'+this._internalId+'.callFormatting(\'fontsize\', \'\')', '', this.lng['default']);
for (var i in this.sizeMenu) {
if (i.match(/[A-Za-z]/gi)) {
str += this._addListMenuContent('parent.WPro.'+this._internalId+'.callFormatting(\'fontsize\', \''+i+'\')', '', '<span style="font-size:'+i+'">'+this.sizeMenu[i]+'</span>');
} else {
str += this._addListMenuContent('parent.WPro.'+this._internalId+'.callFormatting(\'fontsize\', \''+i+'\')', '', '<font size="'+i+'">'+this.sizeMenu[i]+'</font>');
}
iCount++;
}
break;
default:
return;
}
if (iCount == 0) str += '<div class="wproOff noStyles">There are no styles available for this selection</div>';
var body = '<div id="wproMContainer" onclick="parent.WPro.'+this._internalId+'.PMenu.reselectRange();parent.WPro.'+this._internalId+'.closePMenu();">'+str+'</div>'
var position = WPro.getElementPosition(button);
var posx = position['left']
var posy = position['top'] + button.offsetHeight
if (frame.style)
frame.style.display = 'block';
var ret = this.PMenu.showDropDown(frame, head, body,width, 50, posx, posy);
this._menuHeightTimeout(menu, ret['width'], ret['height'], ret['posx'], ret['posy']);
}
wproEditor.prototype._menuHeightTimeout = function (menu, width, height, posx, posy) {
var win = eval('WPro.'+this._internalId+'.'+menu+'PMenuMenu');
if (win.contentWindow) {
var doc = win.contentWindow.document;
} else {
var doc = win.document;
}
if (doc.body) {
var maxHeight = 100
var docHeight = this.editFrame.offsetHeight
if (docHeight > 112) {
maxHeight = docHeight
}
var container
if (container = doc.getElementById('wproMContainer')) {
var height = container.offsetHeight + 5
if (height < maxHeight && height > 0) {
height = (height + 16);
} else {
height = maxHeight;
}
win.style.height = height +'px';
}
if (win.contentWindow) {
win.contentWindow.focus();
}
} else {
WPro.timer.addTimer("WPro."+this._internalId+"._menuHeightTimeout('"+menu+"', '"+width+"', '"+height+"', '"+posx+"', '"+posy+"');",1);
}
}
wproEditor.prototype.showFloatingMenu = function (items, posx, posy, allowed) {
WPro.currentEditor = this;
wp_current_obj = this;
if (this.hiddenMenus.firstChild) {
this.hiddenMenus.removeChild(this.hiddenMenus.firstChild);
}
var range = this.selAPI.getRange();
var inTable = false;
var inA = false;
if (range.type=='control') {
var tn = range.nodes[0].tagName;
if (tn=='A') {
inA = range.nodes[0];
} else if (tn=='TABLE') {
inTable = range.nodes[0];
}
}
if (!inTable) inTable = range.getContainerByTagName('TABLE');
if (!inA) inA = range.getContainerByTagName('A');
var node = document.createElement('DIV');
node.className = 'wproFloatingMenu';
var a = document.createElement('A');
a.setAttribute('href', 'javascript:void(null);');
a.style.margin = '0px';
a.style.padding = '0px';
node.appendChild(a);
var doneItems = [];
var n = items.length;
var bCount = 0;
var sCount = 0;
for (var i = 0; i < n; i++) {
if (!items[i]) {
break;
}
if (items[i][0] == 'separator') {
if (doneItems[doneItems.length-1]) {
if (doneItems[doneItems.length-1] == 'separator') {
continue;
}
} else {
continue;
}
var a = document.createElement('DIV');
a.className = 'wproSeparator';
var img = document.createElement('img');
img.setAttribute('width', '1');
img.setAttribute('height', '1');
a.appendChild(img);
doneItems.push('separator');
} else if (items[i][0] == 'titleSeparator') {
var a = document.createElement('DIV');
a.className = 'wproTitleSeparator';
a.innerHTML = items[i][1]
doneItems.push('separator');
} else {
var str = items[i][1]
if (allowed) {
if (!allowed.test(items[i][0])) {
continue;
}
}
if (items[i][0] == 'tageditor' || items[i][0] == 'removetag' || items[i][0] == 'deletetag') {
if (range.type=='control') {
var tagName = range.nodes[0].tagName;
if (tagName == 'IMG' && range.nodes[0].className.match(/wproFilePlugin/i)) {
if (!/('object'\:\{|\%27object\%27\%3A\%7B)/i.test(String(range.nodes[0].getAttribute("_wpro_media_data")))) {
tagName = 'EMBED';
} else {
tagName = 'OBJECT';
}
}
} else {
if (this.tagPath) {
if (this.tagPath.selectedNode) {
var tagName = this.tagPath.selectedNode.tagName;
}
}
if (!tagName) {
var tagName = range.getCommonAncestorContainer().tagName;
}
}
if ((this.snippet || items[i][0] == 'removetag' || items[i][0] == 'deletetag') && tagName=='BODY') continue;
if (items[i][0] == 'removetag' && /^(table|td|th|tr|ul|ol)$/i.test(tagName)) continue;
if (range.type=='control'&&items[i][0] == 'removetag') continue;
str = str.replace(/##tagname##/, '&lt;'+tagName+'&gt;');
}
var a = document.createElement('A');
var img = document.createElement('IMG');
var t = document.createElement('SPAN');
t.innerHTML = str;
var f = 'WPro.'+this._internalId+'.PMenu.reselectRange();'+items[i][2]+';WPro.'+this._internalId+'.closePMenuTimeout();';
a.setAttribute('href','javascript:void(null);');
eval('a.onclick = function () {'+f+'}');
a.onmouseover = wproFMenuOver;
a.onmouseout = wproFMenuOut;
a.onfocus = wproFMenuOver;
a.onblur = wproFMenuOut;
if (items[i][4] > 22 || items[i][5] > 22) {
img.setAttribute('src', this.themeURL+'buttons/spacer.gif');
} else {
img.setAttribute('src', (items[i][3].match(/\//)?'':this.themeURL+'buttons/')+items[i][3]);
}
img.setAttribute('width', '20');
img.setAttribute('height', '20');
a.appendChild(img);
a.appendChild(t);
if (items[i][6]) {
var className = this.getButtonState(a, items[i][6], inTable, inA, range);
a.className = className;
if (className!='wproDisabled'&&className!='wproHidden') {
doneItems.push('button');
} else {
continue;
}
} else {
doneItems.push('button');
}
bCount ++;
}
node.appendChild(a);
}
if (doneItems[doneItems.length-1]) {
if (doneItems[doneItems.length-1] == 'separator') {
node.removeChild(node.lastChild);
}
}
if (bCount == 0) {
return;
}
this.hiddenMenus.appendChild(node);
node.style.display = 'block';
node.style.visibility = 'visible'
this.PMenu.showPMenu(node, node.offsetWidth, node.offsetHeight, posx, posy);
}
wproEditor.prototype._mOver = function (elm) {
if (!this._initFocus) return;
var cmd = elm.getAttribute("_wp_cid")
var className = elm.className;
if (className=="wproDisabled") return;
if (className=="wproLatched") {
elm.className="wproLatchedOver";
return;
}
if (className=="wproReady") {
elm.className="wproOver";
return;
}
elm.className="wproOver"
}
wproEditor.prototype._mOut = function (elm) {
if (!this._initFocus)return;
var cmd = elm.getAttribute("_wp_cid")
var className = elm.className;
if (className=="wproDisabled")return;
if (className=="wproLatched")return;
if (className=="wproOver") {
elm.className="wproReady";
return;
}
if (className=="wproLatchedOver") {
elm.className="wproLatched";
return;
}
elm.className="wproReady"
}
wproEditor.prototype._mDown = function (elm) {
if (!this._initFocus) {
this.editWindow.focus();
this._initFocus = true;
}
if (elm.className == "wproDisabled")return;
elm.className="wproDown";
}
wproEditor.prototype._mUp = function (elm) {
var style=elm.className
if (style=="wproDisabled")return;
if (style=="wproLatched")return;
if (elm.getAttribute('onmousedown')) {
if (elm.getAttribute('onmousedown').toString().match(/showButtonMenu/i)) {
return;
}
}
elm.className="wproOver"
elm.blur();
}

wproEditor.prototype.getButtonState = function (srcElement, cmd, inTable, inA, range) {
var returnValue = "wproReady";
if (this.buttonStateHandlers[cmd]) {
if (range == undefined) range = this.selAPI.getRange();
if (inA == undefined) inA = range.getContainerByTagName('A');
if (inTable == undefined) inTable = range.getContainerByTagName('TABLE');
returnValue = this.buttonStateHandlers[cmd](this,srcElement,cmd,inTable,inA,range);
} else {
try {
if (this.editDocument.queryCommandState(cmd)) {
returnValue="wproLatched"
} else if (!this.editDocument.queryCommandEnabled(cmd)) {
returnValue="wproDisabled"
} else {
returnValue="wproReady"
}
} catch (e) {}
}
return returnValue ? (returnValue==''?"wproReady":returnValue) : "wproReady";
}
wproEditor.prototype._reactivate = function () {
if (!this._inPreview) {
if (this.editWindow.document.body) {
this._enableDesignMode();
}
}
}
wproEditor.prototype.setButtonStates = function () {
try {
this.editDocument.queryCommandValue('FontName');
} catch (e) {
this._reactivate();
}
if (this.tagPath) {
this.tagPath.build();
}
this._initFocus = true
if (this._inSource) {
var buttons = this.sourceButtons;
} else if (this._inDesign) {
var buttons = this.designButtons;
} else {
return;
}
var range = this.selAPI.getRange();
var inTable = false;
var inA = false;
if (range.type=='control') {
var tn = range.nodes[0].tagName;
if (tn=='A') {
inA = range.nodes[0];
} else if (tn=='TABLE') {
inTable = range.nodes[0];
}
}
if (!inTable) inTable = range.getContainerByTagName('TABLE');
if (!inA) inA = range.getContainerByTagName('A');
var n = buttons.length;
for (var i = 0; i < n; i++) {
if (!buttons[i].getAttribute("_wp_cid")) { continue };
var cmd = buttons[i].getAttribute("_wp_cid")
var className = this.getButtonState(buttons[i],cmd,inTable,inA,range);
if (buttons[i].className != className) {
buttons[i].className = className;
}
}
if (this._inDesign) {
var font_face_value = this._getFormattingValue('FontName')
var font_size_value = this._getFormattingValue('FontSize')
var style_value = this._getFormattingValue('FormatBlock')
this._setListText('_font', font_face_value);
this._setListText('_size', this.sizeMenu[font_size_value] ? this.sizeMenu[font_size_value] : font_size_value);
this._setListText('_styles', style_value);
}
}
wproEditor.prototype.getComputedStyle = function ( element, cssRule ) {
return WPro.getComputedStyle(this.editDocument, element, cssRule);
}
wproEditor.prototype._getFormattingValue = function (com) {
com = com.toLowerCase();
if (this.formattingValueHandlers[com]) {
return this.formattingValueHandlers[com](this, com);
}
var value = '';
var range = this.selAPI.getRange();
switch (com) {
case 'formatblock':
var styles = []
if (range.type == 'control') {
var tagName = range.nodes[0].tagName
if (/^(img)$/i.test(tagName) && this.imageStyles) {
styles = this.imageStyles;
} else if (/^(object|embed)$/i.test(tagName) && this.objectStyles) {
styles = this.objectStyles;
} else if (/^(table)$/i.test(tagName) && this.tableStyles) {
styles = this.tableStyles;
} else if (/^(a)$/i.test(tagName) && this.linkStyles) {
styles = this.linkStyles;
} else if (/^(textarea)$/i.test(tagName) && this.textareaStyles) {
styles = this.textareaStyles;
} else if (/^(input)$/i.test(tagName)) {
var type = range.nodes[0].type
if (type=='submit'||type=='reset') type = 'button';
if (type=='password') type = 'text';
if (typeof(eval('this.'+type+'InputStyles')) != 'undefined') {
styles = eval('this.'+type+'InputStyles');
}
if (this.inputStyles) {
styles = styles.concat(this.inputStyles);
}
}
if (value = this._matchStyleToTag(range.nodes[0], styles)) {
break;
}
}
var tags = [];
var container = range.getCommonAncestorContainer();
while (container.parentNode && container.tagName != 'BODY') {
if (container.nodeType != 1) {
container = container.parentNode;
}
tags.push(container);
container = container.parentNode
}
var l = tags.length
for (var i = 0; i < l; i++) {
if (/^(span|strong|b|em|i|u|strike|sup|sub)$/i.test(tags[i].tagName) && this.textStyles) {
styles = this.textStyles
} else if (/^(a)$/i.test(tags[i].tagName) && this.linkStyles) {
styles = this.linkStyles
} else if (WPro.supported_blocks.test(tags[i].tagName) && this.paragraphStyles) {
styles = this.paragraphStyles
} else if (/^(td|th)$/i.test(tags[i].tagName) && this.cellStyles) {
styles = this.cellStyles
} else if (/^(tr)$/i.test(tags[i].tagName) && this.rowStyles) {
styles = this.rowStyles
} else if (/^(table)$/i.test(tags[i].tagName) && this.tableStyles) {
styles = this.tableStyles
} else if (/^(ol|ul)$/i.test(tags[i].tagName) && this.listStyles) {
styles = this.listStyles
} else if (/^(li)$/i.test(tags[i].tagName) && this.listItemStyles) {
styles = this.listItemStyles
}
if (value = this._matchStyleToTag(tags[i], styles)) {
break;
}
}
break;
default:
try {
value = this.editDocument.queryCommandValue(com)
}catch(e){}
}
return value;
}
wproEditor.prototype._matchStyleToTag = function (tag, styles) {
if (tag) {
var nodeTagName = tag.tagName;
for (var style in styles) {
var tagName = style.replace(/^([a-z0-9]+)[^>]*?$/gi, "$1", style);
if (WPro.isGecko) {
if (tagName=='strong') tagName = 'b';
if (tagName=='em') tagName = 'i';
} else {
if (tagName=='b') tagName = 'strong';
if (tagName=='i') tagName = 'em';
}
if (tagName.toUpperCase() != nodeTagName) continue;
var attrs = style.match(/ [a-z]+="[^"]*"/gi);
if (attrs) {
var matches = false;
for (j=0;j<attrs.length;j++) {
var nodeName = attrs[j].replace(/ ([a-z]+)="[^"]*"/gi, "$1");
var nodeValue = attrs[j].replace(/ [a-z]+="([^"]*)"/gi, "$1").replace(/&quot;/, '"').replace(/&lt;/, '<').replace(/&gt;/, '>').replace(/&amp;/, '&');
var value
if (nodeName == 'class') {
value = String(tag.className).replace(/\s*wproGuide\s*/i,'').replace(/\s*wproFilePlugin\s*/i,'');
if (value.toLowerCase().trim() != nodeValue.toLowerCase().trim()) {
break;
}
} else if (nodeName == 'style') {
value = WPro.styleFormatting(tag.style.cssText);
nodeValue = WPro.styleFormatting(nodeValue);
nodeValue2 = nodeValue.replace(/#[0-9a-z]+/gi, function(x) {return WPro.hexToRGB(x);});
var styles1 = value.match(/([A-Za-z\-]*:[^;]*)/gi);
var styles2 = nodeValue.match(/([A-Za-z\-]*:[^;]*)/gi);
var styles3 = nodeValue2.match(/([A-Za-z\-]*:[^;]*)/gi);
if (styles1) {
var m = true
for (k=0;k<styles1.length;k++) {
m = false
if (styles2) {
for (l=0;l<styles2.length;l++) {
if (styles1[k] == styles2[l]) {
m = true
break;
}
}
}
if (!m && styles3) {
for (l=0;l<styles3.length;l++) {
if (styles1[k] == styles3[l]) {
m = true
break;
}
}
if (!m) {
break;
}
}
}
if (!m) {
break;
}
} else {
break;
}
} else if (value = tag.getAttribute(nodeName)) {
if (value.toLowerCase().trim() != nodeValue.toLowerCase().trim()) {
break;
}
} else {
break;
}
if (j == attrs.length-1) {
matches = true;
}
}
if (matches) {
return styles[style];
break;
}
} else if (WPro.getNodeAttributesString(tag) == '') {
return styles[style];
break;
}
}
}
return false;
}
wproEditor.prototype._setListText = function (list, value) {
var e;
if (e = document.getElementById(this._internalId + list)) {
if (!value) {
value = e.title;
}
value = value.toString().replace(/ /, '&nbsp;').replace(/</, '&lt;').replace(/>/, '&gt;');
if (e.firstChild.innerHTML != value) {
e.firstChild.innerHTML = value;
}
}
}
wproEditor.prototype._setSnippetStatus = function (str) {
if (str.search(/<body/gi) != -1) {
this.snippet = false;
} else {
this.snippet = true;
}
}
wproEditor.prototype._markSelection = function () {
this.focus();
this.history.disabled = true;
var range = this.selAPI.getRange();
var eRange = range.cloneRange();
var sRange = range.cloneRange();
var sNode = this.editDocument.createTextNode('|wproSelectionStart|');
var eNode = this.editDocument.createTextNode('|wproSelectionEnd|');
if (eRange.collapse(false)) {
eRange.insertNode(eNode);
sRange.collapse(true);
sRange.insertNode(sNode);
} else if (eRange.nodes[0]) {
var pNode = eRange.nodes[0].parentNode
pNode.insertBefore(eNode, eRange.nodes[0].nextSibling);
pNode.insertBefore(sNode, eRange.nodes[0]);
}
this.history.disabled = false;
}
wproEditor.prototype._selectSelection = function () {
var elm
if (this._inSource && !this._movingToDesign && !this._movingToSource && !this._movingToPreview) {
if (elm = this.editDocument.getElementById('selection')) {
var range = this.selAPI.getRange();
range.selectNodeContents(elm);
range.select();
var height = this.editFrame.offsetHeight;
var width = this.editFrame.offsetWidth;
this.editWindow.scrollTo(elm.offsetLeft-(width/4), elm.offsetTop-(height/4));
}
}
if (!this._loaded) {
this.triggerEditorEvent('load');
this._loaded = true;
}
}
wproEditor.prototype._initToolbar = function (toolbar) {
var b = toolbar.getElementsByTagName('BUTTON');
var n = b.length
for (var i=0;i<n;i++) {
if (b[i].className == 'wproReady') {
if (!b[i].onmousedown&&!WPro.isOpera) {
eval('b[i].onmousedown = function () {WPro.'+this._internalId+'._mDown(this)}')
}
if (!b[i].onmouseup&&!WPro.isOpera) {
eval('b[i].onmouseup = function () {WPro.'+this._internalId+'._mUp(this)}')
}
if (!b[i].onmouseover) {
eval('b[i].onmouseover = function () {if(typeof(WPro)!="undefined"){WPro.'+this._internalId+'._mOver(this)}}')
b[i].onfocus = b[i].onmouseover
}
if (!b[i].onmouseout) {
eval('b[i].onmouseout =function () {if(typeof(WPro)!="undefined"){WPro.'+this._internalId+'._mOut(this)}}')
b[i].onblur = b[i].onmouseout
}
if (b[i].onclick) {
var str = b[i].onclick.toString();
str = str.replace(/^[\s]*function[^{]+\{([\s\S]+)\}[\s]*$/i, "if(this.className=='wproDisabled')return;$1");
eval('b[i].onclick = function () {'+str+'}');
}
}
}
}
wproEditor.prototype._unloadToolbar = function (toolbar) {
var b = toolbar.getElementsByTagName('BUTTON');
var n = b.length
for (var i=0;i<n;i++) {
b[i].onmousedown = null;
b[i].onmouseup = null;
b[i].onmouseover = null;
b[i].onmouseout = null;
b[i].onclick = null;
b[i].onfocus = null;
b[i].onblur = null;
}
}
wproEditor.prototype._destroySession = function () {this._createSessTag("destroy");}
wproEditor.prototype._unload = function () {
this._destroySession();
this._removeEditDocumentEvents();
this._unloadToolbar (this.designToolbar);
this._unloadToolbar (this.sourceToolbar);
this._unloadToolbar (this.previewToolbar);
eval('WPro.'+this._internalId+'=null;');
}
function wproRedoBSH (EDITOR){
return EDITOR.history.levels[EDITOR.history.currentLevel+1]?'wproReady':'wproDisabled';
}
function wproUndoBSH (EDITOR){
if (EDITOR.history.reachedLimit) {
return 'wproDisabled';
}
return EDITOR.history.levels[EDITOR.history.currentLevel-1]?'wproReady':'wproDisabled';
}
function wproGuidelinesBSH (EDITOR){
return EDITOR._guidelines ? "wproLatched" : "wproReady";
}
function wproSFClearEmpty (EDITOR, html){
if ((EDITOR._inDesign && !WPro.hasContent(EDITOR.editDocument.body)) || (html.match(/^\s*(<(p|div)>(&nbsp;){0,}(\s*<br[^>]*>\s*){0,}(&nbsp;){0,}(\s*<br[^>]*>\s*){0,}(&nbsp;){0,}<\/(p|div)>|(&nbsp;){0,}\s*<br[^>]*>\s*(&nbsp;){0,})\s*$/i))) {
return '';
}else{
return html;
}
}
function wproDesignFilter(editor, str) {
str = str.replace(/<script([^>]*) type="([^"]*)"/gi, '<script$1 _wpro_type="$2"');
str = str.replace(/<script([^>]*)>/gi, '<script$1 type="text/wpro">');
while (str.match(/<([a-z]+[^>]*) on([a-z]+)="([^"]*)"/gi)) {
str = str.replace(/<([a-z]+[^>]*) on([a-z]+)="([^"]*)"/gi, '<$1 _wpro_on$2="$3"');
}
str = str.replace(/<([a-z]+[^>]*) (href|src|action)="([^"]*)"/gi, '<$1 $2="$3" _wpro_$2="$3"');
return str;
}
function wproColorPicker (editor) {
this.color = '';
this.editor = editor;
}
wproColorPicker.prototype.setColor = function (color) {
this.color = color;
this.set(color);
}
function wproE_fontColorSet (color) {
WPro.editors[this.editor].callFormatting('forecolor', color);
}
function wproE_fontColorPick () {
WPro.currentColorPicker = WPro.editors[this.editor].fontColor
WPro.editors[this.editor].openDialogPlugin('wproCore_colorPicker', 324, 400);
}
function wproE_highlightColorSet (color) {
WPro.editors[this.editor].callFormatting('backcolor', color);
}
function wproE_highlightColorPick () {
WPro.currentColorPicker = WPro.editors[this.editor].highlightColor
WPro.editors[this.editor].openDialogPlugin('wproCore_colorPicker', 324, 400);
}
function wproContextHandler (obj, evt) {
if (obj.contextMenu.length&&!evt.altKey) {
obj.showContextMenu(evt);
WPro.preventDefault(evt);
}
}
function wproClickHandler (obj, evt) {
obj.triggerEditorEvent('click', evt);
}
function wproDblClickHandler (obj, evt) {
obj.triggerEditorEvent('dblClick', evt);
}
function wproDropPasteHandler(obj, evt) {
var imgs = obj.editDocument.getElementsByTagName('IMG');
for (var i=0;i<imgs.length;i++) {
if (imgs[i].getAttribute('_wpro_src')) {
imgs[i].src = imgs[i].getAttribute('_wpro_src');
}
}
}
function wproHistory (editor) {
this.editor = editor;
this.levels = new Array();
this.levels[0] = new Array();
this.levels[0]['html'] = null;
this.currentLevel = 0;
this.baseLevel = 0;
this.limit = 20;
this.disabled = false;
this.reachedLimit = false;
this.keyPresses = 0;
this.lastKey = null;
}
wproHistory.prototype.reset = function() {
this.levels = new Array();
this.currentLevel = 0;
this.keyPresses=0;
}
wproHistory.prototype.add = function (ignoreState) {
var editor = WPro.editors[this.editor];
if ((!editor._inDesign&&!ignoreState)||this.disabled) return;
var str = editor.editDocument.body.innerHTML;
if (this.levels[this.currentLevel]) {
if (this.levels[this.currentLevel]['html'] == str) {
editor.setButtonStates();
return;
}
}
this.reachedLimit = false;
this.currentLevel ++;
var f = this.currentLevel;
while (this.levels[f]) {
this.levels[f] = null;
f++;
}
this.levels[this.currentLevel] = new Array;
this.levels[this.currentLevel]['html']=str;
this.levels[this.currentLevel]['scrollTop']=editor.editDocument.body.scrollTop + editor.editDocument.documentElement.scrollTop;
this.levels[this.currentLevel]['scrollLeft']=editor.editDocument.body.scrollLeft + editor.editDocument.documentElement.scrollLeft;
if (WPro.isIE) {
var range = editor.editDocument.selection.createRange();
this.levels[this.currentLevel]['offsetTop'] = range.offsetTop;
this.levels[this.currentLevel]['offsetLeft'] = range.offsetLeft;
}
this.keyPresses=0;
if (this.currentLevel>this.limit) {
this.levels[this.baseLevel]=null;
this.baseLevel++
}
editor.setButtonStates();
}
wproHistory.prototype.addKey = function (keyCode, mode) {
var editor = WPro.editors[this.editor];
if (!editor._inDesign) return;
this.reachedLimit = false;
var f = this.currentLevel+1;
while (this.levels[f]) {
this.levels[f] = null;
f++;
}
if (this.lastKey==keyCode&&this.keyPresses!=0){
return;
}
this.lastKey = keyCode;
if (this.keyPresses==0) {
this.add();
} else if (keyCode==13||keyCode==8||keyCode==46) {
this.add();
}
this.keyPresses++
}
wproHistory.prototype.undo = function () {
var editor = WPro.editors[this.editor];
if (!editor._inDesign) {
WPro.callCommand(editor.editDocument, 'undo', false, null);
} else {
this.add();
if (this.currentLevel-1>this.baseLevel) {
if (this.levels[this.currentLevel-1]!=null) {
this.currentLevel--;
this.reachedLimit = false;
WPro.setInnerHTML(editor.editDocument.body, this.levels[this.currentLevel]['html'])
editor.editWindow.scrollTo(this.levels[this.currentLevel]['scrollLeft'], this.levels[this.currentLevel]['scrollTop']);
if (WPro.isIE) {
try{var range = editor.editDocument.selection.createRange();
if (this.levels[this.currentLevel]['offsetLeft']&&this.levels[this.currentLevel]['offsetTop']) {
range.moveToPoint(this.levels[this.currentLevel]['offsetLeft'], this.levels[this.currentLevel]['offsetTop']);
range.select();
}}catch(e){}
}
if (this.currentLevel-1<=this.baseLevel) {
this.reachedLimit = true;
}
}
} else {
this.reachedLimit = true;
}
editor.setButtonStates();
}
}
wproHistory.prototype.redo = function () {
var editor = WPro.editors[this.editor];
if (!editor._inDesign) {
WPro.callCommand(editor.editDocument,'redo', false, null);
} else {
if (this.levels[this.currentLevel+1]) {
this.currentLevel++;
this.reachedLimit = false;
WPro.setInnerHTML(editor.editDocument.body, this.levels[this.currentLevel]['html']);
editor.editWindow.scrollTo(this.levels[this.currentLevel]['scrollLeft'], this.levels[this.currentLevel]['scrollTop']);
if (WPro.isIE) {
try{var range = editor.editDocument.selection.createRange();
if (this.levels[this.currentLevel]['offsetLeft']&&this.levels[this.currentLevel]['offsetTop']) {
range.moveToPoint(this.levels[this.currentLevel]['offsetLeft'], this.levels[this.currentLevel]['offsetTop']);
range.select();
}}catch(e){}
}
}
editor.setButtonStates();
}
}
wproHistory.prototype.pre = function() {
var editor=WPro.editors[this.editor];
this.add();
var d = this.disabled
this.disabled = true
return d;
}
wproHistory.prototype.post = function(d) {
var editor=WPro.editors[this.editor];
this.disabled = d;
this.add();
editor.redraw();
}
function wproRange(range, editor) {
this.type = 'text';
this.range = range;
this.nodes = [];
this.editor=editor;
}
wproRange.prototype.getContainer = function () {
if (this.nodes[0]) {
var node = this.nodes[0].parentNode;
while (!node.tagName) {
node = node.parentNode
}
return node;
}
return false;
}
wproRange.prototype.getCommonAncestorContainer = wproGetCommonAncestorContainer;
wproRange.prototype.getEndContainer = wproGetEndContainer;
wproRange.prototype.getStartContainer = wproGetStartContainer;
wproRange.prototype.getBlockContainer = function () {
var node = this.getStartContainer();
return WPro.getBlockParent(node);
}
wproRange.prototype.getContainerByTagName = function (tag) {
var container = this.getStartContainer();
return WPro.getParentNodeByTagName(container, tag);
}
wproRange.prototype.getHTMLText = wproGetHTMLText;
wproRange.prototype.select = wproSelect;
wproRange.prototype.selectNodeContents = wproSelectNodeContents
wproRange.prototype.collapse = function (toStart) {
if (this.type == 'control') return false;
this.range.collapse(toStart);
return true;
}
wproRange.prototype.cloneContents = wproCloneContents;
wproRange.prototype.deleteContents = wproDeleteContents;
wproRange.prototype.extractContents = wproExtractContents;
wproRange.prototype.pasteHTML = wproPasteHTML;
wproRange.prototype.insertNode = wproInsertNode;
wproRange.prototype.cloneRange = wproCloneRange;
wproRange.prototype.toString = wproToString;
wproRange.prototype.getText = wproRange.prototype.toString;
function wproSelAPI (editor) {
this.range = null;
this.editor = editor;
}
wproSelAPI.prototype.removeAllRanges = function () {
if (document.selection) {
WPro.editors[this.editor].selection.empty();
} else {
WPro.editors[this.editor].editWindow.getSelection().removeAllRanges()
}
}
wproSelAPI.prototype.toString = function () {
return this.getRange().toString();
}
wproSelAPI.prototype.getText = wproSelAPI.prototype.toString;
wproSelAPI.prototype.getSelectedNodes = wproGetSelectedNodes;
wproSelAPI.prototype.getRange = function () {
var nodes;
var type = 'text'
if (nodes = this.getSelectedNodes()) {
type='control';
}
var r = new wproRange(this.range, this.editor);
r.type=type;
r.nodes = nodes;
return r;
}
wproSelAPI.prototype.createRange = wproCreateRange;
if (!WPro) {
var WPro = new wproObj;
WPro.events.addEvent(window,'unload',function(){WPro._unload();});
};
function updateAllHTML(){WPro.updateAllHTML()};
function updateAllWysiwyg(){WPro.updateAllDesign()};
function wp_getEditorByName(str){return eval('WPro.'+str)};
function wp_e(str){return eval('WPro.'+str)};
function wp_prepare_submission(obj){WPro.editors[obj.id].prepareSubmission()};
function wp_reactivate(str){eval('WPro.'+str+'._reactivate()')};
function submit_form(){WPro._updateAll('prepareSubmission')};