<?php

/*
 * rah_repeat - Iterators tags for Textpattern CMS templates
 * https://github.com/gocom/rah_repeat
 *
 * Copyright (C) 2019 Jukka Svahn
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
 * Iterator.
 */
final class Rah_Repeat_Bag implements Iterator, Countable, ArrayAccess
{
    /**
     * Stores the items.
     *
     * @var array
     */
    private $items = [];

    /**
     * Stores current count of items.
     *
     * @var int
     */
    private $count = 0;

    /**
     * Stores the current index.
     *
     * @var int
     */
    private $index = 0;

    /**
     * Constructor.
     *
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
        $this->index = 0;
        $this->count = count($items);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->index++;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->count >= ($this->index + 1);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->count;
    }

    /**
     * Whether the item is the last one.
     *
     * @return bool
     */
    public function isLast()
    {
        return $this->count === ($this->index + 1);
    }

    /**
     * Whether the item is first one.
     *
     * @return bool
     */
    public function isFirst()
    {
        return $this->index === 0;
    }

    /**
     * Gets contents.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->items[$this->index];
    }
}
