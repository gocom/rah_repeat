<?php

/**
 * Rah_repeat plugin for Textpattern CMS.
 *
 * @author  Jukka Svahn
 * @license GNU GPLv2
 * @link    http://rahforum.biz/plugins/rah_repeat
 *
 * Copyright (C) 2013 Jukka Svahn http://rahforum.biz
 * Licensed under GNU General Public License version 2
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Creates a list from the given values.
 *
 * @param  array  $atts
 * @param  string $thing
 * @return string
 */

    function rah_repeat($atts, $thing = null)
    {
        global $rah_repeat, $rah_repeat_count, $variable;

        extract(lAtts(array(
            'form'       => '',
            'delimiter'  => ',',
            'value'      => '',
            'limit'      => null,
            'offset'     => 0,
            'wraptag'    => '',
            'break'      => '',
            'class'      => '',
            'duplicates' => 0,
            'sort'       => '',
            'exclude'    => null,
            'trim'       => 1,
            'range'      => '',
            'assign'     => null,
        ), $atts));

        if ($range)
        {
            if (strpos($range, ','))
            {
                $values = call_user_func_array('range', do_list($range));
            }
            else
            {
                $values = array();

                foreach (do_list($value) as $v)
                {
                    if (strpos($v, '-'))
                    {
                        $v = do_list($v, '-');
                        $values = array_merge($values, (array) range($v[0], $v[1]));
                    }
                    else
                    {
                        $values[] = $v;
                    }
                }
            }
        }
        else
        {
            $values = explode($delimiter, $value);
        }

        if ($trim)
        {
            $values = doArray($values, 'trim');
        }

        if ($duplicates)
        {
            $values = array_unique($values);
        }

        if ($exclude !== null)
        {
            $exclude = explode($delimiter, $exclude);

            if ($trim)
            {
                $exclude = doArray($exclude, 'trim');
            }

            $values = array_diff($values, $exclude);
        }

        if ($sort && $sort = doArray(doArray(explode(' ', trim($sort), 2), 'trim'), 'strtoupper'))
        {
            if (count($sort) == 2 && defined('SORT_'.$sort[0]))
            {
                sort($values, constant('SORT_'.$sort[0]));
            }

            if (end($sort) == 'DESC')
            {
                $values = array_reverse($values);
            }
        }

        $values = array_slice($values, $offset, $limit);

        if ($assign !== null)
        {
            foreach (do_list($assign) as $key => $var)
            {
                $value = isset($values[$key]) ? $values[$key] : '';
                $variable[$var] = $value;
            }
        }

        $rah_repeat_count = $count = count($values);

        if (!$values || ($thing === null && $form === ''))
        {
            return '';
        }

        $i = 0;
        $out = array();

        foreach ($values as $string)
        {
            $i++;
            $parent = $rah_repeat;
            $parent_count = $rah_repeat_count;

            $rah_repeat = array(
                'string' => $string,
                'first'  => ($i == 1),
                'last'   => ($count == $i),
                'index'  => $i - 1,
            );

            if ($thing === null && $form !== '')
            {
                $out[] = parse_form($form);
            }
            else
            {
                $out[] = parse($thing);
            }

            $rah_repeat = $parent;
            $rah_repeat_count = $parent_count;
        }

        unset($rah_repeat);
        return doWrap($out, $wraptag, $break, $class);
    }

/**
 * Returns the current value.
 *
 * @param  array  $atts
 * @return string
 */

    function rah_repeat_value($atts)
    {
        global $rah_repeat;

        extract(lAtts(array(
            'escape' => 0,
            'index'  => 0,
        ), $atts));

        if (!isset($rah_repeat['string']))
        {
            return '';
        }

        if ($index)
        {
            return $rah_repeat['index'];
        }

        if ($escape)
        {
            return txpspecialchars($rah_repeat['string']);
        }

        return $rah_repeat['string'];
    }

/**
 * Returns number of items in the last loop.
 *
 * @return int
 */

    function rah_repeat_count()
    {
        return (int) $rah_repeat_count;
    }

/**
 * Checks if the item is the first.
 *
 * @param  array  $atts
 * @param  string $thing
 * @return string
 */

    function rah_repeat_if_first($atts, $thing = '')
    {
        global $rah_repeat;
        return parse(EvalElse($thing, $rah_repeat['first'] == true));
    }

/**
 * Checks if the item is the last.
 *
 * @param  array  $atts
 * @param  string $thing
 * @return string
 */

    function rah_repeat_if_last($atts, $thing = '')
    {
        global $rah_repeat;
        return parse(EvalElse($thing, $rah_repeat['last'] == true));
    }