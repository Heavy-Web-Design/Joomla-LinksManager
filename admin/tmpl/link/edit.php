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

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

?>

<form action="<?php echo Route::_('index.php?option=com_hwdlinks&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="adminForm" class="form-validate">

    <div class="row title-alias form-vertical mb-3">
        <div class="col-md-8">
            <?php echo $this->form->renderField('title'); ?>
        </div>
    </div>

    <div class="main-card">

        <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'general', 'recall' => true, 'breakpoint' => 768]); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'general', Text::_('COM_HWDLINKS_LINK_CONTENT')); ?>
        <div class="row">
            <div class="col-lg-9">
                <fieldset class="adminform">
                    <?php echo $this->form->renderFieldset('content'); ?>
                </fieldset>
            </div>
            <div class="col-lg-3">
                <?php echo $this->form->renderFieldset('publish'); ?>
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'image', Text::_('COM_HWDLINKS_LINK_IMAGE')); ?>
            <div class="row">
                <div class="col-md-9">
                    <?php echo $this->form->renderFieldset('image'); ?>
                </div>
            </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.endTabSet'); ?>

    </div>

    <!--<input type="hidden" name="task" value="link.edit" />-->
    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); ?>

</form>
