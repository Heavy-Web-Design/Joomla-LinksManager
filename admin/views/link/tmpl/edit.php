<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_hwdlinks
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('formbehavior.chosen','select');

JHtml::_('behavior.core');
JHtml::_('behavior.tabstate');
JHtml::_('behavior.formvalidator');
//JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

?>

<form action="<?php echo JRoute::_('index.php?option=com_hwdlinks&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="adminForm" class="form-validate">

    <div class="form-inline form-inline-header">
      <?php echo $this->form->renderField('title');?>
      <hr />
    </div>

    <div class="form-horizontal">

      <div class="row-fluid">

        <div class="span8">
          <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

          <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_HWDLINKS_LINK_CONTENT')); ?>
              <fieldset class="adminform">
                  <!--<legend><?php echo JText::_('COM_HELLOWORLD_LEGEND_CONTENT') ?></legend>-->
                  <div class="row-fluid">
                      <div class="span9">
                        <?php echo $this->form->renderFieldset('content'); ?>
                      </div>
                  </div>
              </fieldset>
          <?php echo JHtml::_('bootstrap.endTab'); ?>

          <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'image', JText::_('COM_HWDLINKS_LINK_IMAGE')); ?>
              <fieldset class="adminform">
                  <div class="row-fluid">
                      <div class="span9">
                        <?php echo $this->form->renderFieldset('image'); ?>
                      </div>
                  </div>
              </fieldset>
          <?php echo JHtml::_('bootstrap.endTab'); ?>

          <?php echo JHtml::_('bootstrap.endTabSet'); ?>
        </div>

        <div class="span4">
          <fieldset class="form-vertical">
            <?php echo $this->form->renderFieldset('publish'); ?>
          </fieldset>
        </div>

      </div>


    </div>

    <input type="hidden" name="task" value="link.edit" />
    <?php echo JHtml::_('form.token'); ?>

</form>
