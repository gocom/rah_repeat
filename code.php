<?php
	##################
	#
	#	rah_repeat-plugin for Textpattern
	#	version 0.1
	#	by Jukka Svahn
	#	http://rahforum.biz
	#
	###################

	function rah_repeat($atts,$thing='') {
		extract(lAtts(array(
			'delimiter' => ',',
			'value' => ''
		),$atts));
		$values = explode($delimiter,$value);
		
		if(count($values) == 0)
			return '';
		
		$out = array();
		global $rah_repeat;
		foreach($values as $string) {
			$rah_repeat = $string;
			$out[] = parse($thing);
			$rah_repeat = '';
		}
		unset($rah_repeat);
		return implode('',$out);
	}

	function rah_repeat_value($atts,$thing='') {
		global $rah_repeat;
		return $rah_repeat;
	}?>