<?php

/**
 * Rah_repeat plugin for Textpattern CMS
 *
 * @author Jukka Svahn
 * @date 2009-
 * @license GNU GPLv2
 * @link http://rahforum.biz/plugins/rah_repeat
 *
 * Copyright (C) 2012 Jukka Svahn <http://rahforum.biz>
 * Licensed under GNU Genral Public License version 2
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

	function rah_repeat($atts, $thing='') {
		global $rah_repeat;
		
		extract(lAtts(array(
			'delimiter' => ',',
			'value' => '',
			'limit' => NULL,
			'offset' => 0,
			'wraptag' => '',
			'break' => '',
			'class' => '',
			'duplicates' => 0,
			'sort' => '',
			'exclude' => NULL,
			'trim' => 0,
			'range' => '',
		), $atts));
		
		if($range && strpos($range, ',')) {
			$r = array_merge(array(0, 10, 1), do_list($range));
			$values = range($r[0], $r[1], $r[2]);
		}
		
		else {
			$values = explode($delimiter, $value);
		}
		
		if($trim == 1)
			$values = doArray($values, 'trim');
		
		if($duplicates == 1)
			$values = array_unique($values);
		
		if($exclude !== NULL) {
			$exclude = explode($delimiter, $exclude);
			
			if($trim == 1)
				$exclude = doArray($exclude, 'trim');
			
			$values = array_diff($values, $exclude);
		}
		
		if(empty($values))
			return;
		
		if(!empty($sort)) {
			list($crit, $dir) = array_merge(array('', ''), explode(' ', $sort));
			
			if($crit == 'numeric')
				sort($values, SORT_NUMERIC);
			elseif($crit == 'string')
				sort($values, SORT_STRING);
			elseif($crit == 'locale')
				sort($values, SORT_LOCALE_STRING);
			else
				sort($values, SORT_REGULAR);
			
			if($dir == 'desc') 
				$values = array_reverse($values);
		}

		$values = array_slice($values, $offset, $limit);
		$count = count($values);

		$i = 0;
		$out = array();

		foreach($values as $string) {
			$i++;
			$parent = $rah_repeat;

			$rah_repeat = 
				array(
					'string' => $string,
					'first' => ($i == 1),
					'last' => ($count == $i),
				);

			$out[] = parse($thing);
			$rah_repeat = $parent;
		}

		unset($rah_repeat);
		return doWrap($out,$wraptag,$break,$class);
	}

/**
 * Returns current value
 * @return string
 */

	function rah_repeat_value($atts) {
		global $rah_repeat;
		
		extract(lAtts(array(
			'escape' => 1,
		), $atts));
		
		return $escape ? 
			htmlspecialchars($rah_repeat['string']) : $rah_repeat['string'];
	}

/**
 * Conditional tag for testing if the item is first
 * @return string User-markup
 */

	function rah_repeat_if_first($atts, $thing='') {
		global $rah_repeat;
		return parse(EvalElse($thing,$rah_repeat['first'] == true));
	}

/**
 * Conditional tag for testing if the item is last
 * @return string User-markup
 */

	function rah_repeat_if_last($atts, $thing='') {
		global $rah_repeat;
		return parse(EvalElse($thing,$rah_repeat['last'] == true));
	}
?>