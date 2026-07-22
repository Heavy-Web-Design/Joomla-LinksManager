<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols
/**
 * @package     HwdLinks.Site
 * @subpackage  com_hwdlinks
 *
 * @copyright   Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace HWD\Component\HwdLinks\Site\Helper;

\defined('_JEXEC') or die;

class LinksHelper
{
    /**
     * Flags a flat, ordered tree of link items with the parent/deeper/shallower/level_diff
     * properties used by the frontend layouts, and trims it to the given level range.
     *
     * @param   array  $items  Flat, ordered items as returned by LinkTable::getTree()
     * @param   int    $start  Level to start at. Root is 1.
     * @param   int    $end    Max level.
     *
     * @return  array
     */
    public static function prepareTree($items, $start = 1, $end = 10)
    {
        $lastitem = 0;

        if ($items) {
            foreach ($items as $i => $item) {
                $item->parent = false;

                if (isset($items[$lastitem]) && $items[$lastitem]->id == $item->parent_id) {
                    $items[$lastitem]->parent = true;
                }

                if (($start && $start > $item->level) || ($end && $item->level > $end)) {
                    unset($items[$i]);
                    continue;
                }

                $item->deeper     = false;
                $item->shallower  = false;
                $item->level_diff = 0;

                if (isset($items[$lastitem])) {
                    $items[$lastitem]->deeper     = ($item->level > $items[$lastitem]->level);
                    $items[$lastitem]->shallower  = ($item->level < $items[$lastitem]->level);
                    $items[$lastitem]->level_diff = ($items[$lastitem]->level - $item->level);
                }

                $lastitem     = $i;
                $item->active = false;
                $item->flink  = $item->link;
            }

            if (isset($items[$lastitem])) {
                $items[$lastitem]->deeper     = (($start ?: 1) > $items[$lastitem]->level);
                $items[$lastitem]->shallower  = (($start ?: 1) < $items[$lastitem]->level);
                $items[$lastitem]->level_diff = ($items[$lastitem]->level - ($start ?: 1));
            }
        }

        return $items;
    }
}
