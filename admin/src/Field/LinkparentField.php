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

class LinkparentField extends ListField
{
    protected $type = 'Linkparent';

    /**
     * Use Joomla's core "fancy select" layout so the list renders as a
     * searchable (Choices.js powered) dropdown instead of a plain <select>.
     *
     * @var    string
     */
    protected $layout = 'joomla.form.field.list-fancy-select';

    /**
     * Prepend the component's own layouts directory so our override of
     * joomla.form.field.list-fancy-select (which adds remote-search
     * support) is found before the core one.
     *
     * @return  array
     */
    protected function getLayoutPaths()
    {
        $paths = parent::getLayoutPaths();

        array_unshift($paths, JPATH_COMPONENT_ADMINISTRATOR . '/layouts');

        return $paths;
    }

    /**
     * Add the data needed to enable remote (server-side) search, so the
     * Choices.js dropdown isn't limited by Fuse.js's client-side ~32
     * character search pattern cap.
     *
     * @return  array
     */
    protected function getLayoutData()
    {
        $data = parent::getLayoutData();

        $id = (int) $this->form->getValue('id', 0);

        $data['remoteSearch'] = true;
        $data['remoteUrl']    = 'index.php?option=com_hwdlinks&task=link.parentSearch&format=json&id=' . $id;

        return $data;
    }

    /**
     * Method to return the options for ordering the hwdlinks record
     * This is the list of siblings the record's siblings - ie those records with the same parent.
     * The method requires that parent id be set.
     */
    protected function getOptions()
    {
        $options = array();

        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('DISTINCT(a.id) AS value, a.title AS text, a.level, a.lft')
            ->from('#__hwdlinks_link AS a');

        // Prevent parenting to children of this record, or to itself
        // If this record has lft = x and rgt = y, then its children have lft > x and rgt < y
        if ($id = $this->form->getValue('id')) {
            $query->join('LEFT', $db->quoteName('#__hwdlinks_link') . ' AS h ON h.id = ' . (int) $id)
                ->where('NOT(a.lft >= h.lft AND a.rgt <= h.rgt)');
        }

        $query->order('a.lft ASC');

        $db->setQuery($query);

        try {
            $options = $db->loadObjectList();
        } catch (\RuntimeException $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
        }

        // Pad the option text with spaces using depth level as a multiplier.
        for ($i = 0; $i < count($options); $i++) {
            $options[$i]->text = str_repeat('- ', $options[$i]->level) . strip_tags($options[$i]->text);
        }

        // Merge any additional options in the XML definition.
        return array_merge(parent::getOptions(), $options);
    }
}
