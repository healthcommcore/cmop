<?php

/* 
* WysiwygPro 3.0.3, 3 March 2008.
* (c) Copyright 2007 Chris Bolt and ViziMetrics Inc.
*/

/*
Functions you can use to post-process code 
These functions can be used outside of WP
*/

class wproUtilities {	
	
	// function longwordbreak
	// This is an optional function that you can call before saving HTNL data sent from WYSIWYG PRO
	// this breaks up words that are too long and might damage the page layout such as excessive use of tabs
	// it does not cut through html tags
	// call it before saving your code like this: $myCode = wproUtilities::longWordBreak($myCode);
	// $str = required, your html code
	// $cols = optional, words over this length will be cut (the default is 40, how many real words can you think of over this length?)
	// $cut = optional, how would you like your excessively long words cut sir? (the default is a space, other options would be a hyphen or carriage return)
	function longWordBreak($str, $cols=40, $cut=' ') {
	   $len = strlen($str);
	   $tag = 0;
		 $result = '';
		 $wordlen = 0;
	   for ($i = 0; $i < $len; $i++) {
		   $chr = $str[$i];
		   if ($chr == '<') {
			  $tag++;
		   } elseif ($chr == '>') {
			  $tag--;
		   } elseif ((!$tag) && (wproUtilities::_isWhitespace($chr))) {
			  $wordlen = 0;
		   } elseif (!$tag) {
			  $wordlen++;
		   }
		   if ((!$tag) && ($wordlen) && (!($wordlen % $cols))) {
			   $chr .= $cut;
		  }
		   $result .= $chr;
	   }
	   return $result;
	}
	function _isWhitespace($chr) {
		if ($chr == " ") return true;
		if ($chr == "\r") return true;
		if ($chr == "\n") return true;
		if ($chr == "\t") return true;
		return false;
	}
	
	// function remove_tags
	// This is an optional function that you can call before saving HTNL data sent from WYSIWYG PRO
	// allows you to remove unwanted tags from the code
	// $code = the html code to be processed
	// $tags = an associative array of tags to remove where the key is the tag name and the value is a boolean,
	// this should be true to remove the tag AND its contents or false to remove the tag but keep its contents.
	function removeTags($code, $tags) {
		if (!empty($code)) {
			if (!is_array($tags)) {
				die('<p><b>WYSIWYGPRO Paramater Error:</b> Your list of tags is not an array!</p>');
			} else {
				foreach($tags as $k => $v) {
					if (!empty($k)) {
						if ($v) {
							// remove tags and all code contained within the tags
							$code = preg_replace("/<".quotemeta($k)."[^>]*?>.*?<\/".quotemeta($k).">/smi",  "", $code);
							$code = preg_replace("/<".quotemeta($k)."[^>]*?>/smi",  "", $code);
						} else {
							// remove tags but leave code within the tags
							$code = preg_replace("/<".quotemeta($k)."[^>]*?>(.*?)<\/".quotemeta($k).">/smi",  "\$1", $code);
							$code = preg_replace("/<".quotemeta($k)."[^>]*?>/smi",  "", $code);
						}
					}
				}
			}
		}
		return $code;
	}
	
	// function removeAttributes
	// This is an optional function that you can call before saving HTNL data sent from WYSIWYG PRO
	// allows you to remove unwanted attributes from tags
	// $code = the html code to be processed
	// $attributes = an array of attributes to remove
	// You can use pattern matching in your attribute names, e.g. this will remove all event handlers:
	//$myCode =  wproUtilities::removeAttributes($myCode, array("on[A-Z]+"));
	function removeAttributes($code, $attrs) {
		if (!empty($code)) {
			if (!is_array($attrs)) {
				die('<p><b>WYSIWYGPRO Paramater Error:</b> Your list of attributes is not an array!</p>');
			} else {
				$num = count($attrs);
				for ($i=0; $i<$num; $i++) {
					if (!empty($attrs[$i])) {
						// remove attributes
						// dbl quotes
						$code = preg_replace("/<([^>]*?) ".$attrs[$i]."=\"[^\"]*\"([^>]*?)>/smi",  "<\$1\$2>", $code); 
						// single quotes
						$code = preg_replace("/<([^>]*?) ".$attrs[$i]."='[^']*'([^>]*?)>/smi",  "<\$1\$2>", $code); 
						// no quotes
						$code = preg_replace("/<([^>]*?) ".$attrs[$i]."=[^ ]* ([^>]*?)>/smi",  "<\$1\$2>", $code); 
						// boolean with no values
						$code = preg_replace("/<([^>]*?) ".$attrs[$i]." ([^>]*?)>/smi",  "<\$1\$2>", $code); 
					}
				}
			}
		}
		return $code;
	}
		
	// function email_encode
	// Requires email_encode2
	// encode email addresses to prevent spam bots from finding them 
	function emailEncode($code, $only_links=true) {
		
		if ($only_links) {
			// match only email links
			$matches = array();
			preg_match_all("/<a .*?href=\"mailto:[a-zA-Z0-9!#$%*\/?|^{}`~&'+\-=_.]+@[a-zA-Z0-9\.-]+\.[a-zA-Z]{2,4}.*?\".*?>.*?<\/a>/", $code, $matches);
			for ($i=0;$i<count($matches[0]);$i++) {
				$original = $matches[0][$i];
				$matches[0][$i] = preg_replace("/((mailto:|)[a-zA-Z0-9!#$%*\/?|^{}`~&'+\-=_.]+@[a-zA-Z0-9\.-]+\.[a-zA-Z]{2,4})/e", "wproUtilities::_emailEncode2('$1')",$matches[0][$i]);
				$code = str_replace($original, $matches[0][$i], $code);
			}
		} else {
			// match all email addresses in all tags, attributes and text
			$code = preg_replace("/((mailto:|)[a-zA-Z0-9!#$%*\/?|^{}`~&'+\-=_.]+@[a-zA-Z0-9\.-]+\.[a-zA-Z]{2,4})/e", "wproUtilities::_emailEncode2('$1')",$code);
		}
		
		return $code;
	}
	function _emailEncode2 ($email_address) {
		static $trans_array = array();
		if (empty($trans_array)) {
			for ($i=1; $i<255; $i++) {
				$trans_array[chr($i)] = "&#" . $i . ";";
			}
		}
		return strtr($email_address, $trans_array);    
	}
	
	// Closes any tags left open.
	function closeTags ($html) {
	
		// put all opened tags into an array
		preg_match_all ( "#<([a-z0-9:]+)( .*)?(?!/)>#iU", $html, $result );
		$openedtags = $result[1];
		
		// put all closed tags into an array
		preg_match_all ( "#</([a-z0-9:]+)>#iU", $html, $result );
		$closedtags = $result[1];
		$len_opened = count ( $openedtags );
				
		// all tags are closed
		if( count ( $closedtags ) == $len_opened ) {
			return $html;
		}
		$openedtags = array_reverse ( $openedtags );
		
		// tags that are allowed open
		$allowed = array('area','bgsound','base','basefont','br','comment','col','frame','hr','input','img','isindex','link','meta','param','spacer','wbr');
		
		// close tags
		for( $i = 0; $i < $len_opened; $i++ ) {
			if ( !in_array ( $openedtags[$i], $closedtags ) && !in_array($openedtags[$i], $allowed) ) {
				$html .= "</" . $openedtags[$i] . ">";
			} else {
				unset ( $closedtags[array_search ( $openedtags[$i], $closedtags)] );
			}
		}
		
		// return
		return $html;
	}
	
	// cuts an HTML string to the desired length and closes any tags left open at the cut point.
	// cut length does not include tags.
	function cutHTML( $html, $len, $cut='' ) {
		
		// find the cut point ( do not include html tags )
		$c = 0;
		$c2 = 0;
		
		$parts = explode(">", $html);
		
		foreach($parts as $key=>$part) {
			$pL = "";
			$pR = "";
				
			if (($pos = strpos($part, "<")) === false) {
				$pL = $part;
				
			} else/*if ($pos > 0)*/ {
				$pL = substr($part, 0, $pos);
				$pR = substr($part, $pos, strlen($part));
			}
			
			if (($c2+strlen($pL))>=$len) {
				$c += $len-$c2;
				break;
			}
			
			$c+=strlen($pL) + strlen($pR)+1;
			
			$c2+=strlen($pL);
			
		}
		
		$snippet = substr ( $html, 0, $c );
		
		$snippet = strrpos ( $snippet, "<" ) > strrpos ( $snippet, ">" ) ? rtrim ( substr ( $str, 0, strrpos ( $snippet, "<" ) ) ) . $cut : rtrim ( $snippet ) . $cut;
		
		$snippet = wproUtilities::closeTags ( $snippet );
		
		return $snippet;
	}

	
}
?>