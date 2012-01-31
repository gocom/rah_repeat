<?php	##################
	#
	#	rah_repeat-plugin for Textpattern
	#	version 0.4
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
		$count = count($values);

		if($count == 0)
			return '';
		
		$i = 0;
		$out = array();
		
		global $rah_repeat;
		foreach($values as $string) {
			$i++;
			if(!empty($offset) && $i <= $offset)
				continue;

			$first = (!isset($first)) ? true : false;
			$last = ($count == $i or $limit == $i) ? true : false;
			$old = $rah_repeat;

			$rah_repeat = 
				array(
					'string' => $string,
					'first' => $first,
					'last' => $last,
				)
			;

			$out[] = parse($thing);
			$rah_repeat = $old;
			if($last == true)
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
		return $rah_repeat['string'];
	}

	function rah_repeat_if_first($atts,$thing='') {
		global $rah_repeat;
		return parse(EvalElse($thing,$rah_repeat['first'] == true));
	}

	function rah_repeat_if_last($atts,$thing='') {
		global $rah_repeat;
		return parse(EvalElse($thing,$rah_repeat['last'] == true));
	} ?>