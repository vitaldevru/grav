<?php

declare(strict_types=1);

/**
 * @package    Grav\Framework\Flex
 *
 * @copyright  Copyright (C) 2015 - 2019 Trilby Media, LLC. All rights reserved.
 * @license    MIT License; see LICENSE file for details.
 */

namespace Grav\Framework\Flex\Pages;

use Grav\Common\Page\Interfaces\PageInterface;
use Grav\Framework\Flex\FlexCollection;
use Grav\Framework\Flex\Interfaces\FlexCollectionInterface;

/**
 * Class FlexPageCollection
 * @package Grav\Plugin\FlexObjects\Types\FlexPages
 */
class FlexPageCollection extends FlexCollection
{
    /**
     * @return array
     */
    public static function getCachedMethods(): array
    {
        return [
            'withPublished' => true,
            'withVisible' => true,
            'isFirst' => true,
            'isLast' => true,
            'currentPosition' => true,
            'getNextOrder' => false,
        ] + parent::getCachedMethods();
    }

    /**
     * @param bool $bool
     * @return FlexCollectionInterface|FlexPageCollection
     */
    public function withPublished(bool $bool = true)
    {
        $list = array_keys(array_filter($this->call('isPublished', [$bool])));

        return $this->select($list);
    }

    /**
     * @param bool $bool
     * @return FlexCollectionInterface|FlexPageCollection
     */
    public function withVisible(bool $bool = true)
    {
        $list = array_keys(array_filter($this->call('isVisible', [$bool])));

        return $this->select($list);
    }


    /**
     * Check to see if this item is the first in the collection.
     *
     * @param  string $path
     *
     * @return bool True if item is first.
     */
    public function isFirst($path): bool
    {
        $keys = $this->getKeys();
        $first = reset($keys);

        return $path === $first;
    }

    /**
     * Check to see if this item is the last in the collection.
     *
     * @param  string $path
     *
     * @return bool True if item is last.
     */
    public function isLast($path): bool
    {
        $keys = $this->getKeys();
        $last = end($keys);

        return $path === $last;
    }

    /**
     * Gets the previous sibling based on current position.
     *
     * @param  string $path
     *
     * @return PageInterface|false  The previous item.
     */
    public function prevSibling($path)
    {
        return $this->adjacentSibling($path, -1);
    }

    /**
     * Gets the next sibling based on current position.
     *
     * @param  string $path
     *
     * @return PageInterface|false The next item.
     */
    public function nextSibling($path)
    {
        return $this->adjacentSibling($path, 1);
    }

    /**
     * Returns the adjacent sibling based on a direction.
     *
     * @param  string  $path
     * @param  int $direction either -1 or +1
     *
     * @return PageInterface|false    The sibling item.
     */
    public function adjacentSibling($path, $direction = 1)
    {
        $keys = $this->getKeys();
        $pos = \array_search($path, $keys, true);

        if ($pos !== false) {
            $pos += $direction;
            if (isset($keys[$pos])) {
                return $this[$keys[$pos]];
            }
        }

        return null;
    }

    /**
     * Returns the item in the current position.
     *
     * @param  string $path the path the item
     *
     * @return int|null The index of the current page, null if not found.
     */
    public function currentPosition($path): ?int
    {
        $pos = \array_search($path, $this->getKeys(), true);

        return $pos !== false ? $pos : null;
    }

    /**
     * @return string
     */
    public function getNextOrder()
    {
        $directory = $this->getFlexDirectory();

        /** @var FlexPageObject $last */
        $collection = $directory->getIndex();
        $keys = $collection->getStorageKeys();

        // Assign next free order.
        $last = null;
        $order = 0;
        foreach ($keys as $folder => $key) {
            preg_match(FlexPageIndex::ORDER_PREFIX_REGEX, $folder, $test);
            $test = $test[0] ?? null;
            if ($test && $test > $order) {
                $order = $test;
                $last = $key;
            }
        }

        $last = $collection[$last];

        return sprintf('%d.', $last ? $last->value('order') + 1 : 1);
    }
}
