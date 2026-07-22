<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_hwdlinks
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

use Joomla\Registry\Registry;

JHtml::_('formbehavior.chosen', 'select');

$listOrder     = $this->escape($this->state->get('list.ordering'));
$listDirn      = $this->escape($this->state->get('list.direction'));
$user = JFactory::getUser();

$saveOrder = ($listOrder == 'lft' && strtolower($listDirn) == 'asc');
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_hwdlinks&task=links.saveOrderAjax&tmpl=component';
	// pass true as parameter 7 to indicate that we have a nested set
  JHtml::_('sortablelist.sortable', 'hwdlinksList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
}

?>
<form action="index.php?option=com_hwdlinks&view=links" method="post" id="adminForm" name="adminForm">
	<div class="row-fluid">
		<div class="span12">
			<?php echo JText::_('COM_HWDLINKS_LINKS_FILTER'); ?>
			<?php
				echo JLayoutHelper::render(
					'joomla.searchtools.default',
					array('view' => $this)
				);
			?>
		</div>
	</div>
	<table class="table table-striped table-hover" id="hwdlinksList">
		<thead>
		<tr>
			<th width="4%">
      	<?php echo JHtml::_('searchtools.sort', '', 'lft', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
      </th>
			<th width="1%"><?php echo JText::_('COM_HWDLINKS_NUM'); ?></th>
			<th width="1%">
				<?php echo JHtml::_('grid.checkall'); ?>
			</th>
			<th>
				<?php echo JText::_('COM_HWDLINKS_LINKS_TYPE')?>
			</th>
			<!--<th width="83%">-->
			<th class="center">
				<?php echo JHtml::_('searchtools.sort', 'COM_HWDLINKS_LINKS_TITLE', 'title', $listDirn, $listOrder); ?>
			</th>
      <?php /*?><th width="5%">
      	<?php echo "lft"; ?>
      </th>
      <th width="5%">
      	<?php echo "rgt"; ?>
      </th>
      <th width="5%">
      	<?php echo "level"; ?>
      </th>
      <th width="5%">
      	<?php echo "parent"; ?>
      </th><?php */ ?>
			<th class="center">
				<?php echo JHtml::_('searchtools.sort', 'COM_HWDLINKS_CREATED', 'created', $listDirn, $listOrder); ?>
			</th>
			<th class="center">
				<?php echo JHtml::_('searchtools.sort', 'COM_HWDLINKS_MODIFIED', 'modified', $listDirn, $listOrder); ?>
			</th>
			<th width="7%">
				<?php echo JHtml::_('searchtools.sort', 'COM_HWDLINKS_PUBLISHED', 'published', $listDirn, $listOrder); ?>
			</th>
			<th width="4%">
				<?php echo JHtml::_('searchtools.sort', 'COM_HWDLINKS_ID', 'id', $listDirn, $listOrder); ?>
			</th>
		</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php if (!empty($this->items)) : ?>
				<?php foreach ($this->items as $i => $row) :
					$link = JRoute::_('index.php?option=com_hwdlinks&task=link.edit&id=' . $row->id);
					// create a list of the parents up the hierarchy to the root
          if ($row->level > 1)
          {
	          $parentsStr = '';
          	$_currentParentId = $row->parent_id;
          	$parentsStr = ' ' . $_currentParentId;
          	for ($j = 0; $j < $row->level; $j++)
            {
            	foreach ($this->ordering as $k => $v)
              {
              	$v = implode('-', $v);
                $v = '-' . $v . '-';
                if (strpos($v, '-' . $_currentParentId . '-') !== false)
                {
                	$parentsStr .= ' ' . $k;
                  $_currentParentId = $k;
                  break;
                }
              }
            }
          }
          else
          {
          	$parentsStr = '';
          }
				?>
					<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $row->parent_id; ?>"
						item-id="<?php echo $row->id; ?>" parents="<?php echo $parentsStr; ?>" level="<?php echo $row->level; ?>">
						<td><?php
                $iconClass = '';
                $canReorder  = $user->authorise('core.edit.state', 'com_hwdlinks.link.' . $row->id);
                if (!$canReorder)
                {
	              	$iconClass = ' inactive';
                }
                elseif (!$saveOrder)
                {
	                $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::_('tooltipText', 'JORDERINGDISABLED');
                }
                ?>
                <span class="sortable-handler<?php echo $iconClass ?>">
                	<span class="icon-menu" aria-hidden="true"></span>
                </span>
                <?php if ($canReorder && $saveOrder) : ?>
									<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $row->lft; ?>" class="width-20 text-area-order" />
                <?php endif; ?>
            </td>
						<td>
							<?php echo $this->pagination->getRowOffset($i); ?>
						</td>
						<td>
							<?php echo JHtml::_('grid.id', $i, $row->id); ?>
						</td>
						<td class="center">
							<?php
							if($row->type == 'link') {
								echo '<span class="hasTooltip icon-link" data-original-title="' . JText::_('COM_HWDLINKS_LINK_TYPE_LINK') . '"></span>';
							} elseif ($row->type == 'document') {
								echo '<span class="hasTooltip icon-file" data-original-title="' . JText::_('COM_HWDLINKS_LINK_TYPE_DOCUMENT') . '"></span>';
							} elseif ($row->type == 'menu') {
								echo '<span class="hasTooltip icon-list" data-original-title="' . JText::_('COM_HWDLINKS_LINK_TYPE_MENU') . '"></span>';
							} elseif ($row->type == 'article') {
								echo '<span class="hasTooltip icon-stack icon-article" data-original-title="' . JText::_('COM_HWDLINKS_LINK_TYPE_ARTICLE') . '"></span>';
							} else {
								//echo '<span class="hasTooltip icon-ban-circle" data-original-title="' . JText::_('COM_HWDLINKS_LINK_TYPE_EMPTY') . '"></span>';
							}
							?>
						</td>
						<td>
							<?php $prefix = JLayoutHelper::render('joomla.html.treeprefix', array('level' => $row->level)); ?>
            	<?php echo $prefix; ?>
							<a href="<?php echo $link; ?>" title="<?php echo JText::_('COM_HWDLINKS_EDIT_LINK'); ?>">
								<?php echo $row->title; ?>
							</a>
						</td>
						<?php /*?><td align="center">
            	<?php echo $row->lft; ?>
            </td>
            <td align="center">
            	<?php echo $row->rgt; ?>
            </td>
            <td align="center">
              <?php echo $row->level; ?>
            </td><?php */ ?>
            <td class="center">
              <?php echo $row->created; ?>
            </td>
						<td class="center">
              <?php echo $row->modified; ?>
            </td>
						<td class="center">
							<?php echo JHtml::_('jgrid.published', $row->published, $i, 'links.', true, 'cb'); ?>
						</td>
						<td center="center">
							<?php echo $row->id; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<?php echo JHtml::_('form.token'); ?>
</form>
