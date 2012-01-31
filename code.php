<?php	##################
	#
	#	rah_repeat-plugin for Textpattern
	#	version 0.6
	#	by Jukka Svahn
	#	http://rahforum.biz
	#
	###################

	function rah_repeat($atts,$thing='') {
		extract(lAtts(array(
			'delimiter' => ',',
			'value' => '',
			'limit' => '',
			'offset' => 0,
			'wraptag' => '',
			'break' => '',
			'class' => '',
			'duplicates' => 0,
			'sort' => '',
			'exclude' => ''
		),$atts));

		$values = explode($delimiter,$value);
		
		if($duplicates == 1)
			$values = array_unique($values);
		
		if(!empty($exclude)) {
			$exclude = explode($delimiter,$exclude);
			$values = array_diff($values,$exclude);
		}
		
		$count = count($values);
		
		if($count == 0)
			return;
		
		if(!empty($sort)) {
			$sort = explode(' ',$sort);
			switch($sort[0]) {
				case 'numeric':
					sort($values,SORT_NUMERIC);
					break;
				case 'string':
					sort($values,SORT_STRING);
					break;
				case 'locale':
					sort($values,SORT_STRING);
					break;
				default:
					sort($values,SORT_REGULAR);
			}
			if(isset($sort[1]) && $sort[1] == 'desc') 
				$values = array_reverse($values);
		}
		
		$i = 0;
		$out = array();
		
		global $rah_repeat;
		foreach($values as $string) {
			$i++;
			if($i <= $offset)
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