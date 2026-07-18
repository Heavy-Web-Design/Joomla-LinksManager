<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols
/**
 * @package     LinksManager.Site
 * @subpackage  com_hwdlinks
 *
 * @copyright   Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace HWD\Component\LinksManager\Site\View\Links;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;



class HtmlView extends BaseHtmlView
{
    public function display($tpl = null)
    {
        // Assign data to the view
        $items = $this->get('Items');

        //TODO: Make menu item options to control this variables
        $start = 1; // Level to start. root is 1.
        $end   = 10; // End or max level.

        $hidden_parents = array();
        $lastitem       = 0;

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

                //$item->title = htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8', false);
            }

            if (isset($items[$lastitem])) {
                $items[$lastitem]->deeper     = (($start ?: 1) > $items[$lastitem]->level);
                $items[$lastitem]->shallower  = (($start ?: 1) < $items[$lastitem]->level);
                $items[$lastitem]->level_diff = ($items[$lastitem]->level - ($start ?: 1));
            }
        }

        $this->items = $items;

        // Display the view
        parent::display($tpl);
    }
}
