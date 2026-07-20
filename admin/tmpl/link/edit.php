<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols
/**
 * @package     HwdLinks.Administrator
 * @subpackage  com_hwdlinks
 *
 * @copyright   Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('behavior.core');
HTMLHelper::_('behavior.tabstate');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

?>

<form action="<?php echo Route::_('index.php?option=com_hwdlinks&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="adminForm" class="form-validate">

    <div class="d-flex flex-column gap-2">
      <?php echo $this->form->renderField('title'); ?>
      <hr />
    </div>

    <div class="form-horizontal">
      <div class="row g-3">
        <div class="col-md-8">
          <?php echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', ['active' => 'general']); ?>

          <?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'general', Text::_('COM_HWDLINKS_LINK_CONTENT')); ?>
              <fieldset class="adminform">
                  <div class="row">
                      <div class="col-md-9">
                        <?php echo $this->form->renderFieldset('content'); ?>
                      </div>
                  </div>
              </fieldset>
          <?php echo HTMLHelper::_('bootstrap.endTab'); ?>

          <?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'image', Text::_('COM_HWDLINKS_LINK_IMAGE')); ?>
              <fieldset class="adminform">
                  <div class="row">
                      <div class="col-md-9">
                        <?php echo $this->form->renderFieldset('image'); ?>
                      </div>
                  </div>
              </fieldset>
          <?php echo HTMLHelper::_('bootstrap.endTab'); ?>

          <?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>
        </div>

        <div class="col-md-4">
          <fieldset class="form-vertical">
            <?php echo $this->form->renderFieldset('publish'); ?>
          </fieldset>
        </div>
      </div>
    </div>

    <input type="hidden" name="task" value="link.edit" />
    <?php echo HTMLHelper::_('form.token'); ?>

</form>
