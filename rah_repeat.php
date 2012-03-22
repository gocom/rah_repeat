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

	function rah_repeat($atts, $thing=NULL) {
		global $rah_repeat, $rah_repeat_var, $variable;
		
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
			'assign' => NULL,
		), $atts));
		
		if($range && strpos($range, ',')) {
			$r = array_merge(array(0, 10, 1), do_list($range));
			$values = range($r[0], $r[1], $r[2]);
		}
		
		else {
			$values = explode($delimiter, $value);
		}
		
		if($trim) {
			$values = doArray($values, 'trim');
		}
		
		if($duplicates) {
			$values = array_unique($values);
		}
		
		if($exclude !== NULL) {
			$exclude = explode($delimiter, $exclude);
			
			if($trim) {
				$exclude = doArray($exclude, 'trim');
			}
			
			$values = array_diff($values, $exclude);
		}
		
		if($sort && $sort = doArray(do_list($sort, ' '), 'strtoupper')) {
		
			if(defined('SORT_'.$sort[0])) {
				sort($values, constant('SORT_'.$sort[0]));
			}
			
			if(end($sort) == 'DESC') {
				$values = array_reverse($values);
			}
		}
		
		if($assign !== NULL) {
			$rah_repeat_var = array();
			
			foreach(do_list($assign) as $key => $var) {
				$rah_repeat_var[$var] = isset($values[$key]) ? $values[$key] : '';
				$variable[$var] = $rah_repeat_var[$var];
			}
		}
		
		if(empty($values) || $thing === NULL) {
			return;
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
		global $rah_repeat, $rah_repeat_var;
		
		extract(lAtts(array(
			'escape' => 1,
			'name' => NULL,
		), $atts));
		
		$value = $name !== NULL ? $rah_repeat_var[$name] : $rah_repeat['string'];
		return $escape ? htmlspecialchars($value) : $value;
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