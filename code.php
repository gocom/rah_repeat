<?php
	##################
	#
	#	rah_repeat-plugin for Textpattern
	#	version 0.3
	#	by Jukka Svahn
	#	http://rahforum.biz
	#
	###################

	function rah_repeat($atts,$thing='') {
		extract(lAtts(array(
			'delimiter' => ',',
			'value' => '',
			'limit' => '',
			'offset' => '',
			'wraptag' => '',
			'break' => '',
			'class' => ''
		),$atts));
		
		$values = explode($delimiter,$value);
		
		if(count($values) == 0)
			return '';
		
		$i = 0;
		$out = array();
		
		global $rah_repeat;
		foreach($values as $string) {
			$i++;
			if(!empty($offset) && $i <= $offset)
				continue;
			$rah_repeat = $string;
			$out[] = parse($thing);
			$rah_repeat = '';
			if(!empty($limit) && $i == $limit)
				break;
		}
		unset(
			$rah_repeat
		);
		return 
			doWrap($out,$wraptag,$break,$class)
		;
	}

	function rah_repeat_value($atts,$thing='') {
		global $rah_repeat;
		return $rah_repeat;
	}?>