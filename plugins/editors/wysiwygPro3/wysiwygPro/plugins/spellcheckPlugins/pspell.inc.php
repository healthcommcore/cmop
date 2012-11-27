<?php
if (!defined('IN_WPRO')) exit;
class spellcheckerAPI extends spellcheckerBaseAPI {
	
	/* returns an array of available dictionaries */
	function getAvailableDictionaries() {
		if (empty($this->availDicts)) {
			global $SPELLCHECKER_DICTIONARIES;
			$this->availDicts = $SPELLCHECKER_DICTIONARIES;
		} 
		return $this->availDicts;		
	}
		
	/* returns an array of misspelt words and suggestions */
	function getSpellingResults ($data, $lang) {
	
		// set available dictionaries
		$this->getAvailableDictionaries();
		// set dictionary to use
		$lang = $this->getLang($lang);
		
		// strip out tags with contetn that shouldn't be checked
		$data = preg_replace('/<script[^>]*>.*?<\/script>/smi', '', $data);
		$data = preg_replace('/<style[^>]*>.*?<\/style>/smi', '', $data);
		$data = preg_replace('/<object[^>]*>.*?<\/object>/smi', '', $data);
		$data = preg_replace('/<embed[^>]*>.*?<\/embed>/smi', '', $data);
		$data = preg_replace('/<applet[^>]*>.*?<\/applet>/smi', '', $data);
		$data = preg_replace('/<iframe[^>]*>.*?<\/iframe>/smi', '', $data);
		$data = preg_replace('/<frame[^>]*>.*?<\/frame>/smi', '', $data);
		$data = preg_replace('/<!--.*?-->/si', '', $data);
		
		// strip remaining tags
		$data = ereg_replace('<[^>]*>', '', $data);
		
		// strip out any HTML-like entities
		$data = ereg_replace('&#[0-9]+;', ' ', $data);
		$data = ereg_replace('&[a-z]+;', ' ', $data);
		
		$data = preg_replace('/[^\w]/sm', ' ', $data);
		
		$words_elem = array();
		// get the list of misspelled words. 
		
		$wordlist = preg_split('/\s/',$data);
		
		// Filter words
		$words = array();
		for($i = 0; $i < count($wordlist); $i++) {
			$word = trim($wordlist[$i]);
			if(!in_array($word, $words, true) && !empty($word)) {
				$words[] = $word;
			}
		}
		//$misspelled = $return = array();
		$spelling = "";
		$jargon = "";
		// spelling
		if (preg_match("/^en[_\-]us/smi", $lang)) {
			$spelling = 'american';
		}
		if (preg_match("/^en[_\-]gb/smi", $lang)) {
			$spelling = 'british';
		}
		if (preg_match("/^en[_\-]ca/smi", $lang)) {
			$spelling = 'canadian';
		}
		// jargon
		if (preg_match("/^[a-z][a-z][_\-][a-z][a-z][_\-].*?$/smi", $lang)) {
			$jargon = preg_replace("/^[a-z][a-z][_\-][a-z][a-z][_\-](.*?)$/smi", "$1", $lang);
			$lang = preg_replace("/^([a-z][a-z][_\-][a-z][a-z])[_\-].*?$/smi", "$1", $lang);
		}
		
		//exit($lang.' | '.$spelling.' | '.$jargon);
		
		$int = pspell_new($lang, $spelling, $jargon, 'UTF-8');
		
		foreach ($words as $value) {
			if (!pspell_check($int, $value)) {
				$words_elem[$value] = implode(', ', @pspell_suggest($int, $value));
			}
		}

		return $words_elem;
	}


}
?>