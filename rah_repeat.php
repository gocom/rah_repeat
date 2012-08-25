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
		global $rah_repeat, $variable;
		
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
			'trim' => 1,
			'range' => '',
			'assign' => NULL,
		), $atts));
		
		if($range && strpos($range, ',')) {
			$values = call_user_func_array('range', do_list($range));
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
		
		if($sort && $sort = doArray(doArray(explode(' ', trim($sort), 2), 'trim'), 'strtoupper')) {
		
			if(count($sort) == 2 && defined('SORT_'.$sort[0])) {
				sort($values, constant('SORT_'.$sort[0]));
			}
			
			if(end($sort) == 'DESC') {
				$values = array_reverse($values);
			}
		}
		
		$values = array_slice($values, $offset, $limit);
		
		if($assign !== NULL) {
			foreach(do_list($assign) as $key => $var) {
				$value = isset($values[$key]) ? $values[$key] : '';
				$variable[$var] = $value;
			}
		}
		
		if(empty($values) || $thing === NULL) {
			return;
		}

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
					'index' => $i,
				);

			$out[] = parse($thing);
			$rah_repeat = $parent;
		}

		unset($rah_repeat);
		return doWrap($out, $wraptag, $break, $class);
	}

/**
 * Returns the current value
 * @return string
 */

	function rah_repeat_value($atts) {
		global $rah_repeat;
		
		extract(lAtts(array(
			'escape' => 0,
			'index' => 0,
		), $atts));
		
		if(!isset($rah_repeat['string'])) {
			return;
		}
		
		if($index) {
			return $rah_repeat['index'];
		}
		
		if($escape) {
			return htmlspecialchars($rah_repeat['string']);
		}

		return $rah_repeat['string'];
	}

/**
 * Checks if the item is the first
 * @return string User-markup
 */

	function rah_repeat_if_first($atts, $thing='') {
		global $rah_repeat;
		return parse(EvalElse($thing, $rah_repeat['first'] == true));
	}

/**
 * Checks if the item is the last
 * @return string User-markup
 */

	function rah_repeat_if_last($atts, $thing='') {
		global $rah_repeat;
		return parse(EvalElse($thing, $rah_repeat['last'] == true));
	}
?>