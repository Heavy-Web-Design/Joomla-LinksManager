<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols
/**
 * Custom form field for ordering links in the edit form.
 */

namespace HWD\Component\HwdLinks\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Language\Text;

FormHelper::loadFieldClass('list');

class LinkorderingField extends ListField
{
    protected $type = 'Linkordering';

    /**
     * Use Joomla's core "fancy select" layout so the list renders as a
     * searchable (Choices.js powered) dropdown instead of a plain <select>.
     *
     * @var    string
     */
    protected $layout = 'joomla.form.field.list-fancy-select';

    /**
     * Method to return the options for ordering the hwdlinks record
     * This is the list of siblings the record's siblings - ie those records with the same parent.
     * The method requires that parent id be set.
     */
    protected function getOptions()
    {
        $options = [];

        $parentId = (int) $this->form->getValue('parent_id', 0);

        if (empty($parentId)) {
            return false;
        }

        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('a.id AS value, a.title AS text')
            ->from($db->quoteName('#__hwdlinks_link', 'a'))
            ->where('a.parent_id = ' . (int) $parentId);

        $query->order('a.lft ASC');
        $db->setQuery($query);

        try {
            $options = $db->loadObjectList();
        } catch (\RuntimeException $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
        }

        foreach ($options as $key => $option) {
            $options[$key]->text = strip_tags($option->text);
        }

        $options = array_merge(
            [[ 'value' => '-1', 'text' => Text::_('COM_HWDLINKS_ITEM_FIELD_ORDERING_VALUE_FIRST') ]],
            $options,
            [[ 'value' => '-2', 'text' => Text::_('COM_HWDLINKS_ITEM_FIELD_ORDERING_VALUE_LAST') ]]
        );

        // Merge any additional options in the XML definition.
        return array_merge(parent::getOptions(), $options);
    }

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     *
     * This method returns the input element except if a new record is being created,
     * in which case a text string is output
     */
    protected function getInput()
    {
        if ((int) $this->form->getValue('id', 0) === 0) {
            return '<span class="readonly">' . Text::_('COM_HWDLINKS_ITEM_FIELD_ORDERING_TEXT') . '</span>';
        }

        return parent::getInput();
    }
}
