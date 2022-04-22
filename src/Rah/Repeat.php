<?php

/*
 * rah_repeat - Iterator tags for Textpattern CMS templates
 * https://github.com/gocom/rah_repeat
 *
 * Copyright (C) 2022 Jukka Svahn
 *
 * This file is part of rah_repeat.
 *
 * rah_repeat is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation, version 2.
 *
 * rah_repeat is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with rah_repeat. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Plugin class.
 */
final class Rah_Repeat
{
    /**
     * Stores the current item.
     *
     * @var Rah_Repeat_Bag|null
     */
    private ?Rah_Repeat_Bag $current = null;

    /**
     * Stores the previous item.
     *
     * @var Rah_Repeat_Bag|null
     */
    private ?Rah_Repeat_Bag $previous = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        \Txp::get('\Textpattern\Tag\Registry')
            ->register([$this, 'renderList'], 'rah_for')
            ->register([$this, 'renderItem'], 'rah_for_value')
            ->register([$this, 'renderCount'], 'rah_for_count')
            ->register([$this, 'renderIfLast'], 'rah_for_if_last')
            ->register([$this, 'renderIfFirst'], 'rah_for_if_first')
            ->register([$this, 'renderList'], 'rah_repeat')
            ->register([$this, 'renderItem'], 'rah_repeat_value')
            ->register([$this, 'renderCount'], 'rah_repeat_count')
            ->register([$this, 'renderIfLast'], 'rah_repeat_if_last')
            ->register([$this, 'renderIfFirst'], 'rah_repeat_if_first');
    }

    /**
     * Creates a list from the given values.
     *
     * @param array $atts Attributes
     * @param string $thing Contained statement
     *
     * @return string User markup
     */
    public function renderList($atts, $thing = null)
    {
        global $variable;

        extract(lAtts([
            'form' => '',
            'delimiter' => ',',
            'value' => '',
            'limit' => null,
            'offset' => 0,
            'wraptag' => '',
            'break' => '',
            'class' => '',
            'duplicates' => 0,
            'sort' => '',
            'exclude' => null,
            'trim' => 1,
            'range' => '',
            'assign' => null,
        ], $atts));

        if ($range) {
            if (strpos($range, ',')) {
                $values = call_user_func_array('range', do_list($range));
            } else {
                $values = [];

                foreach (do_list($value) as $v) {
                    if (strpos($v, '-')) {
                        $v = do_list($v, '-');
                        $values = array_merge($values, (array) range($v[0], $v[1]));
                    } else {
                        $values[] = $v;
                    }
                }
            }
        } else {
            $values = explode($delimiter, $value);
        }

        if ($trim) {
            $values = doArray($values, 'trim');
        }

        if ($duplicates) {
            $values = array_unique($values);
        }

        if ($exclude !== null) {
            $exclude = explode($delimiter, $exclude);

            if ($trim) {
                $exclude = doArray($exclude, 'trim');
            }

            $values = array_diff($values, $exclude);
        }

        if ($sort && $sort = doArray(doArray(explode(' ', trim($sort), 2), 'trim'), 'strtoupper')) {
            if (count($sort) == 2 && defined('SORT_'.$sort[0])) {
                sort($values, constant('SORT_'.$sort[0]));
            }

            if (end($sort) == 'DESC') {
                $values = array_reverse($values);
            }
        }

        $values = array_slice($values, $offset, $limit);

        if ($assign !== null) {
            foreach (do_list($assign) as $key => $var) {
                $value = isset($values[$key]) ? $values[$key] : '';
                $variable[$var] = $value;
            }
        }

        $items = new Rah_Repeat_Bag($values);

        if (!count($items) || ($thing === null && $form === '')) {
            $this->previous = $items;
            return '';
        }

        $out = [];

        foreach ($items as $item) {
            $parent = $this->current;
            $this->current = $item;

            if ($thing === null && $form !== '') {
                $out[] = parse_form($form);
            } else {
                $out[] = parse($thing);
            }

            $this->current = $parent;
        }

        $this->previous = $items;

        return doWrap($out, $wraptag, $break, $class);
    }

    /**
     * Returns the current value.
     *
     * @param array $atts Attributes
     *
     * @return string The value
     */
    public function renderItem($atts)
    {
        extract(lAtts([
            'escape' => 0,
            'index' => 0,
        ], $atts));

        if ($this->current) {
            if ($index) {
                return $this->current->key();
            }

            if ($escape) {
                return txpspecialchars($this->current->getValue());
            }

            return $this->current->getValue();
        }

        return '';
    }

    /**
     * Returns number of items in the last loop.
     *
     * @return int The number
     */
    public function renderCount()
    {
        if ($this->previous) {
            return count($this->previous);
        }

        return 0;
    }

    /**
     * Checks if the item is the first.
     *
     * @param array $atts Attributes
     * @param string $thing Contained statement
     *
     * @return string User markup
     */
    public function renderIfFirst($atts, $thing = null)
    {
        return parse(EvalElse($thing, $this->current && $this->current->isFirst()));
    }

    /**
     * Checks if the item is the last.
     *
     * @param array $atts Attributes
     * @param string $thing Contained statement
     *
     * @return string User markup
     */
    public function renderIfLast($atts, $thing = null)
    {
        return parse(EvalElse($thing, $this->current && $this->current->isLast()));
    }
}
