/*
 * (c) Copyright Chris Bolt 2003-2007, All Rights Reserved.
 * Unlicensed distribution, copying, reverse engineering, re-purposing or otherwise stealing of this code is a violation of copyright law.
 * Get yourself a license at www.wysiwygpro.com
 */
wproAjaxLoadNeeded = {};
wproAjaxLoaded = [];
function wproAjaxRecordLoad (s) {
	wproAjaxLoaded.push(s.toLowerCase());
	for (var x in wproAjaxLoadNeeded) {
		var not = false;
		if (wproAjaxLoadNeeded[x]==null) {
			continue;	
		}
		var l = wproAjaxLoadNeeded[x].length;
		for (var i=0; i<l;i++) {
			if (!wproAjaxInArray(wproAjaxLoadNeeded[x][i], wproAjaxLoaded) ) {
				not = true;
				break;
			}
		}
		if (!not) {
			wproAjaxParseScripts(x);	
		}
	}
}
function wproAjaxInArray(n, arr) {
	for (var x in arr) {
		if (arr[x] == n) {
			return true;	
		}
	}
	return false;
}
function wproAjaxDisplay (editorCode, node, hsc) {	
	if (hsc) {
		editorCode = editorCode.replace(/&lt;/gi, '<').replace(/&gt;/gi, '>').replace(/&quot;/gi, '"').replace(/&amp;/gi, '&')
	}
	if (typeof(node).toString().toLowerCase() == 'string') {
		var node = document.getElementById(node);
	}
	node.innerHTML = editorCode;
	wproAjaxLoadNeeded[node.id] = [];
	var scripts = node.getElementsByTagName('SCRIPT');
	var l = scripts.length;
	for (var i=0; i<l;i++) {
		var s = scripts.item(i)
		var a = s.getAttribute('src')
		if (a) {
			// strip domain:
			var ss = s.src.toString().toLowerCase()
			ss = ss.replace(/http(|s):\/\/[^\/]+/gi, '');
			//if (!wproAjaxInArray(ss, wproAjaxLoaded)) {
				var n = document.createElement('SCRIPT');
				n.setAttribute('type', 'text/javascript');
				s.parentNode.insertBefore(n, s);
				n.src = s.src;
				wproAjaxLoadNeeded[node.id].push(ss)
				s.parentNode.removeChild(s);
			//}
		}
	}
	// show the load message...
	var divs = node.getElementsByTagName('DIV')
	var l = divs.length;
	for (var i=0; i<l;i++) {
		if (divs[i].className == 'wproLoadMessageHolder') {
			divs[i].style.display = 'block';
			break;
		}
	}
	// hide the textarea...
	if (WPro.browserType!='unsupported') {
		var textarea = node.getElementsByTagName('TEXTAREA')
		var l = textarea.length;
		for (var i=0; i<l;i++) {
			if (textarea[i].className == 'wproHTML') {
				textarea[i].style.display = 'none';
				break;
			}
		}
	}
}
function wproAjaxParseScripts (node, ignore) {
	if (typeof(node).toString().toLowerCase() == 'string') {
		var node = document.getElementById(node);
	}
	wproAjaxLoadNeeded[node.id] = null;
	var scripts = node.getElementsByTagName('SCRIPT');
	var l = scripts.length;
	for (var i=0; i<l;i++) {
		var str = scripts.item(i).text;
		if (str.length > 1 && !str.match(/document\.write/gi)) {
			eval(str);
		}
	}
}