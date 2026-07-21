<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols
/**
 * @package     HwdLinks.Site
 * @subpackage  com_hwdlinks
 *
 * @copyright   Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
//use HWD\Component\HwdLinks\Site\Helper\RouteHelper as HwdLinksHelperRoute;

//$listOrder = $this->escape($this->state->get('list.ordering'));
//$listDirn  = $this->escape($this->state->get('list.direction'));

?>

<?php $id = ''; ?>

<ul class="nav mod-list"<?php echo $id; ?>>
    <?php
    foreach ($this->items as $i => &$item) {
        $class = 'item-' . $item->id . ' level-' . $item->level;

        //Adding parent class for parents
        if ($item->parent === true) {
            $parent = ' parent';
        } else {
            $parent = '';
        }

        echo '<li class="' . $class . $parent . '">
            <div class="li-container-lv-' . $item->level . '">';

        // Setting target
        if ($item->browserNav == 1) {
            $target = 'target="_blank"';
        } else {
            $target = "";
        }

        // Creating links and titles
        switch ($item->type) :
            case 'document':
                $url  = '<a class="item-link item-document" href="' . Uri::base() . $item->document . '" ' . $target . ' title="' . $item->title . '">' . $item->title . '</a>';
                $icon = ' <i class="fa fa-file">&nbsp;</i> ';
                break;
            case 'link':
                $url  = '<a class="item-link item-link" href="' . $item->link . '" ' . $target . ' title="' . $item->title . '">' . $item->title . '</a>';
                $icon = ' <i class="fa fa-link">&nbsp;</i> ';
                break;
            case 'menu':
                $url  = '<a class="item-link item-menu" href="' . $item->menu . '" ' . $target . ' title="' . $item->title . '">' . $item->title . '</a>';
                $icon = ' <i class="fa fa-link">&nbsp;</i> ';
                break;
            case 'article':
                $url  = '<a class="item-link item-article" href="' . $item->article . '" ' . $target . ' title="' . $item->title . '">' . $item->title . '</a>';
                $icon = ' <i class="fa fa-link">&nbsp;</i> ';
                break;
            default:
                $url  = '<span class="item-link item-empty">' . $item->title . '</span>';
                $icon = "";
                break;
        endswitch;

        $date = '';

        // Setting date
        if ($item->modified !== '0000-00-00 00:00:00' && $item->modified !== null) {
            $date = HTMLHelper::date($item->modified, 'Y-m-d', false);
            $date = ' - <small><b>' . Text::_('COM_HWDLINKS_LIST_MODIFIED') . '</b>: ' . $date . '</small>';
        }

        // Constructing item row
        if ($item->parent === true) { // Adding elements to accordeon menu
            echo '<input class="cd-accordion__input" type="checkbox" name ="group-' . $item->id . '" id="group-' . $item->id . '">';
            echo '<div class="link-row link-row-lv-' . $item->level . '">';
            // Adding image if is set
            if ($item->image != null) :
                echo '<div class="item-image"><img src="' . $item->image . '"></div>';
            endif;
            echo '<label class="cd-accordion__label cd-accordion__label--icon-folder item-link-lv-' . $item->level . '" for="group-' . $item->id . '">
                <i class="fa fa-plus"></i>
                </label>';
        } else {
            echo '<div class="link-row link-row-lv-' . $item->level . '">';
        }
        echo '<span class="item-link-lv-' . $item->level . '">' . $url . ' ' . $date . $icon . '</span>';
        echo '</div>';

        // The next item is deeper.
        if ($item->deeper) {
            echo '<ul class="nav-child small cd-accordion__sub">';
        } elseif ($item->shallower) { // The next item is shallower.
            echo '</div></li>';
            echo str_repeat('</ul></li>', $item->level_diff);
        } else { // The next item is on the same level.
            echo '</li>';
        }
    }
    ?>
</ul>
