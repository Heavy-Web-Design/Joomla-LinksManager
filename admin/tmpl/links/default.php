<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols
/**
 * @package     HwdLinks.Administrator
 * @subpackage  com_hwdlinks
 *
 * @copyright   Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

//HTMLHelper::_('formbehavior.chosen', 'select');

$state = $this->state ?? null;
$listOrder = $state ? $state->get('list.ordering', '') : '';
$listDirn  = $state ? $state->get('list.direction', 'asc') : 'asc';
$showSearchTools = !empty($this->filterForm);

$user = Factory::getUser();

$saveOrder = ($listOrder == 'lft' && strtolower($listDirn) == 'asc');

if ($saveOrder) {
    $saveOrderingUrl = 'index.php?option=com_hwdlinks&task=links.saveOrderAjax&tmpl=component';
    // pass true as parameter 7 to indicate that we have a nested set
    HTMLHelper::_('sortablelist.sortable', 'hwdlinksList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
}

?>

<form action="<?php echo Route::_('index.php?option=com_hwdlinks&view=links'); ?>" 
    method="post" id="adminForm" name="adminForm">
    <div class="row">
        <div class="col-12">
            <?php echo Text::_('COM_HWDLINKS_LINKS_FILTER'); ?>
            <?php
            if ($showSearchTools) {
                echo LayoutHelper::render(
                    'joomla.searchtools.default',
                    ['view' => $this]
                );
            }
            ?>
        </div>
    </div>
    <table class="table table-striped table-hover" id="hwdlinksList">
        <thead>
        <tr>
            <th width="4%">
          <?php echo HTMLHelper::_('searchtools.sort', '', 'lft', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
      </th>
            <th width="1%"><?php echo Text::_('COM_HWDLINKS_NUM'); ?></th>
            <th width="1%">
                <?php echo HTMLHelper::_('grid.checkall'); ?>
            </th>
            <th>
                <?php echo Text::_('COM_HWDLINKS_LINKS_TYPE'); ?>
            </th>
            <th class="text-center">
                <?php echo HTMLHelper::_('searchtools.sort', 'COM_HWDLINKS_LINKS_TITLE', 'title', $listDirn, $listOrder); ?>
            </th>
            <th class="text-center">
                <?php echo HTMLHelper::_('searchtools.sort', 'COM_HWDLINKS_CREATED', 'created', $listDirn, $listOrder); ?>
            </th>
            <th class="text-center">
                <?php echo HTMLHelper::_('searchtools.sort', 'COM_HWDLINKS_MODIFIED', 'modified', $listDirn, $listOrder); ?>
            </th>
            <th width="7%">
                <?php echo HTMLHelper::_('searchtools.sort', 'COM_HWDLINKS_PUBLISHED', 'published', $listDirn, $listOrder); ?>
            </th>
            <th width="4%">
                <?php echo HTMLHelper::_('searchtools.sort', 'COM_HWDLINKS_ID', 'id', $listDirn, $listOrder); ?>
            </th>
        </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="9">
                    <?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
        <?php
        if (!empty($this->items)) : ?>
            <?php
            foreach ($this->items as $i => $row) :
                $link = Route::_('index.php?option=com_hwdlinks&task=link.edit&id=' . $row->id);
                // create a list of the parents up the hierarchy to the root
                if ($row->level > 1) {
                    $parentsStr = '';
                    $_currentParentId = $row->parent_id;
                    $parentsStr = ' ' . $_currentParentId;
                    for ($j = 0; $j < $row->level; $j++) {
                        foreach ($this->ordering as $k => $v) {
                            $v = implode('-', $v);
                            $v = '-' . $v . '-';
                            if (strpos($v, '-' . $_currentParentId . '-') !== false) {
                                $parentsStr .= ' ' . $k;
                                $_currentParentId = $k;
                                break;
                            }
                        }
                    }
                } else {
                    $parentsStr = '';
                }
                ?>
                    <tr 
                        class="row<?php echo $i % 2; ?>" 
                        sortable-group-id="<?php echo $row->parent_id; ?>"
                        item-id="<?php echo $row->id; ?>" 
                        parents="<?php echo $parentsStr; ?>" 
                        level="<?php echo $row->level; ?>"
                    >
                        <td>
                            <?php
                            $iconClass = '';
                            $canReorder  = $user->authorise('core.edit.state', 'com_hwdlinks.link.' . $row->id);
                            if (!$canReorder) {
                                $iconClass = ' inactive';
                            } elseif (!$saveOrder) {
                                $iconClass = ' inactive tip-top hasTooltip" title="' . HTMLHelper::_('tooltipText', 'JORDERINGDISABLED');
                            }
                            ?>
                            <span class="sortable-handler<?php echo $iconClass ?>">
                                <span class="icon-menu" aria-hidden="true"></span>
                            </span>
                            <?php if ($canReorder && $saveOrder) : ?>
                                <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $row->lft; ?>" class="width-20 text-area-order" />
                            <?php endif; ?>
                        </td>
                        <td><?php echo $this->pagination->getRowOffset($i); ?></td>
                        <td><?php echo HTMLHelper::_('grid.id', $i, $row->id); ?></td>
                        <td class="text-center">
                            <?php
                            if ($row->type == 'link') {
                                echo '<span class="hasTooltip icon-link" data-original-title="' . Text::_('COM_HWDLINKS_LINK_TYPE_LINK') . '"></span>';
                            } elseif ($row->type == 'document') {
                                echo '<span class="hasTooltip icon-file" data-original-title="' . Text::_('COM_HWDLINKS_LINK_TYPE_DOCUMENT') . '"></span>';
                            } elseif ($row->type == 'menu') {
                                echo '<span class="hasTooltip icon-list" data-original-title="' . Text::_('COM_HWDLINKS_LINK_TYPE_MENU') . '"></span>';
                            } elseif ($row->type == 'article') {
                                echo '<span class="hasTooltip icon-stack icon-article" data-original-title="' . Text::_('COM_HWDLINKS_LINK_TYPE_ARTICLE') . '"></span>';
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            $prefix = LayoutHelper::render('joomla.html.treeprefix', ['level' => $row->level]);
                            echo $prefix;
                            ?>
                            <a href="<?php echo $link; ?>" title="<?php echo Text::_('COM_HWDLINKS_EDIT_LINK'); ?>">
                                <?php echo $row->title; ?>
                            </a>
                        </td>
                        <td class="text-center"><?php echo $row->created; ?></td>
                        <td class="text-center"><?php echo $row->modified; ?></td>
                        <td class="text-center">
                            <?php echo HTMLHelper::_('jgrid.published', $row->published, $i, 'links.', true, 'cb'); ?>
                        </td>
                        <td class="text-center"><?php echo $row->id; ?></td>
                    </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="boxchecked" value="0"/>
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
