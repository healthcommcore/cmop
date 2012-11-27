
/*
 * WysiwygPro 3.0.3.20080303 (c) Copyright Chris Bolt and ViziMetrics Inc. All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */

function wproPreventDefault(evt) {
evt.stopPropagation();
evt.preventDefault();
}
function wproWriteDocument (html) {
var doc = this.editDocument;
doc.open('text/html', 'replace');
doc.write(html);
doc.close();
WPro.callCommand(doc, "LiveResize", false, true)
WPro.callCommand(doc, "MultipleSelection", false, true)
}
function wproLineReturn (evt) {
var range = this.selAPI.getRange();
if (range.getContainerByTagName('LI')) {
return;
}
var parentTagName = range.getBlockContainer().tagName
if (parentTagName=='LI') {
return;
}
if (this.lineReturns == 'div') {
if (parentTagName == "TD"|| parentTagName == "TH"|| parentTagName == "BODY"|| parentTagName == "HTML" || parentTagName == "P") {
this.callFormatting("FormatBlock", "<div>")
}
} else if (this.lineReturns == 'p') {
if (parentTagName == "DIV") {
this.callFormatting("FormatBlock", "<p>")
}
} else if (this.lineReturns == 'br') {
range.pasteHTML('<br>');
WPro.preventDefault(evt);
range.collapse(false);
range.select();
}
}
function wproKeyDownHandler (obj, evt) {
var keyCode = evt.keyCode;
var doRD = true;
if (evt.ctrlKey) {
if (keyCode == 90) {
doRD = false;
obj.callFormatting("Undo", false, null);
WPro.preventDefault(evt);
}
if (keyCode == 89) {
doRD = false;
obj.callFormatting("Redo", false, null);
WPro.preventDefault(evt);
}
} else if (evt.shiftKey && keyCode == 9) {
if (obj._inSource) {
var b = obj.sourceToolbar.getElementsByTagName('BUTTON')
if (b.length) {
b.item(b.length-1).focus();
}
} else if (obj._inDesign) {
var b = obj.designToolbar.getElementsByTagName('BUTTON')
if (b.length) {
b.item(b.length-1).focus();
}
}
} else if (!evt.shiftKey) {
if (keyCode == 13) {
obj.lineReturn(evt)
} else if (keyCode == 9) {
var sel = obj.editDocument.selection.createRange()
sel.pasteHTML(' &nbsp;&nbsp; ')
WPro.preventDefault(evt);
}
}
if (doRD) obj.history.addKey(keyCode);
}
function wproKeyUpHandler (obj, evt) {
var keyCode = evt.keyCode;
if (keyCode == 39 || keyCode == 37 || keyCode == 38 || keyCode == 40) {
obj.setButtonStates();
}
}
function wproMouseDownHandler (obj, evt) {
if (WPro.browserVersion>=7) {
obj._enableDesignMode();
}
wp_current_obj = obj;
WPro.currentEditor = obj;
}
function wproMouseUpHandler(obj, evt) {
obj.setButtonStates();
wp_current_obj = obj;
WPro.currentEditor = obj;
WPro.updateAll('closePMenu');
obj.history.keyPresses=0;
}
function wproGetCommonAncestorContainer () {
if (this.type == 'control' && this.nodes) {
return this.getContainer();
} else {
var n = this.range.commonAncestorContainer;
while (n.nodeType!=1 && n.parentNode) {
n = n.parentNode;
}
return n;
}
}
function wproGetEndContainer () {
if (this.type == 'control' && this.nodes) {
return this.getContainer();
} else {
return this.range.endContainer;
}
}
function wproGetStartContainer () {
if (this.type == 'control' && this.nodes) {
return this.getContainer();
} else {
return this.range.startContainer;
}
}
function wproGetHTMLText () {
if (this.type == 'control' && this.nodes[0]) {
var div = WPro.editors[this.editor].editDocument.createElement('div');
div.appendChild(this.nodes[0].cloneNode(true));
return div.innerHTML;
} else {
var clonedSelection = this.range.cloneContents();
var div = WPro.editors[this.editor].editDocument.createElement('div');
div.appendChild(clonedSelection);
return div.innerHTML;
}
}
function wproSelect () {
var sel = WPro.editors[this.editor].editWindow.getSelection()
sel.removeAllRanges()
sel.addRange(this.range)
WPro.editors[this.editor].focus();
}
function wproSelectNodeContents (referenceNode) {
if (this.type == 'control') return false;
this.range.selectNodeContents(referenceNode);
}
function wproCloneContents () {
return this.range.cloneContents();
}
function wproDeleteContents () {
var editor = WPro.editors[this.editor];
editor.history.add();
this.range.deleteContents();
editor.history.add();
}
function wproExtractContents () {
var editor = WPro.editors[this.editor];
editor.history.add();
var df = this.range.extractContents();
editor.history.add();
return df;
}
function wproPasteHTML (html) {
var editor = WPro.editors[this.editor];
html = editor.triggerHTMLFilter('design', html);
var div = editor.editDocument.createElement("DIV")
WPro.setInnerHTML(div, html);
var cn = div.childNodes;
var num = cn.length;
editor.history.add();
var bd = editor.history.disabled;
editor.history.disabled = true;
for (var i=0; i < num; i++) {
this.insertNode(cn[0]);
}
editor.history.disabled = bd;
editor.history.add();
}
function wproInsertNode (insertNode, selectNode) {
var editor = WPro.editors[this.editor];
editor.history.add();
if (selectNode) editor.selAPI.removeAllRanges();
this.range.deleteContents();
var container = this.range.startContainer
var pos = this.range.startOffset
if (container.nodeType==3 && insertNode.nodeType==3) {
container.insertData(pos, insertNode.nodeValue)
this.range.setEnd(container, pos+insertNode.length)
this.range.setStart(container, pos+insertNode.length)
} else {
var afterNode
if (container.nodeType==3) {
var textNode = container
container = textNode.parentNode
var text = textNode.nodeValue
var textBefore = text.substr(0,pos)
var textAfter = text.substr(pos)
var beforeNode = editor.editDocument.createTextNode(textBefore)
afterNode = editor.editDocument.createTextNode(textAfter)
container.insertBefore(afterNode, textNode)
container.insertBefore(insertNode, afterNode)
container.insertBefore(beforeNode, insertNode)
container.removeChild(textNode)
} else {
afterNode = container.childNodes[pos]
container.insertBefore(insertNode, afterNode)
}
this.range.selectNode(insertNode);
if (!selectNode) {
this.range.collapse(false);
}
}
editor.history.add();
}
function wproCloneRange () {
var r;
if (this.range.cloneRange) {
r = this.range.cloneRange();
}
var nr = new wproRange(r, this.editor);
nr.type = this.type;
nr.nodes = this.nodes;
return nr;
}
function wproToString () {
return this.range.toString();
}
function wproGetSelectedNodes () {
this.range = null;
var nodes = [];
var sel = WPro.editors[this.editor].editWindow.getSelection();
var range
var num = sel.rangeCount
var j = 0;
for (var i=0; i < num; i++) {
range = sel.getRangeAt(i);
if (i == 0) this.range = range
var container = range.startContainer
var endContainer = range.endContainer
var pos = range.startOffset;
if (container == endContainer) {
if (range.endOffset == (pos+1)) {
if (container.tagName) {
var cn = container.childNodes
if (cn[pos]) {
if (cn[pos].tagName) {
nodes[j] = cn[pos];
j++;
} else {
}
}
}
}
}
}
if (j > 0) {
return nodes;
}
if (!this.range) {
this.range = WPro.editors[this.editor].editDocument.createRange();
}
return false;
}
function wproCreateRange () {
var range = WPro.editors[this.editor].editDocument.createRange();
var r = new wproRange(range, this.editor);
return r;
}