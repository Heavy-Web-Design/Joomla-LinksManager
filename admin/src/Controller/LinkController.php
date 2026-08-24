<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols
/**
 * @package     HwdLinks.Administrator
 * @subpackage  com_hwdlinks
 *
 * @copyright   Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace HWD\Component\HwdLinks\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;



class LinkController extends FormController
{
    /**
     * AJAX task backing the remote search of the "Parent" (Linkparent) field.
     * Returns a JSON list of {value, text} pairs matching the typed term.
     *
     * @return  void
     */
    public function parentSearch()
    {
        $this->checkAccess();

        $app  = Factory::getApplication();
        $db   = Factory::getDbo();
        $term = trim((string) $app->getInput()->getString('term', ''));
        $id   = (int) $app->getInput()->getInt('id', 0);

        $query = $db->getQuery(true)
            ->select('DISTINCT(a.id) AS value, a.title AS text, a.level, a.lft')
            ->from($db->quoteName('#__hwdlinks_link', 'a'));

        // Prevent parenting to children of the edited record, or to itself.
        if ($id) {
            $query->join('LEFT', $db->quoteName('#__hwdlinks_link') . ' AS h ON h.id = ' . $id)
                ->where('NOT(a.lft >= h.lft AND a.rgt <= h.rgt)');
        }

        if ($term !== '') {
            $query->where('a.title LIKE ' . $db->quote('%' . $term . '%'));
        }

        $query->order('a.lft ASC');

        $db->setQuery($query);
        $options = $db->loadObjectList();

        $results = [];

        foreach ($options as $option) {
            $results[] = [
                'value' => $option->value,
                'text'  => str_repeat('- ', (int) $option->level) . strip_tags($option->text),
            ];
        }

        $this->sendJson($results);
    }

    /**
     * AJAX task backing the remote search of the "Ordering" (Linkordering) field.
     * Returns a JSON list of {value, text} pairs matching the typed term,
     * scoped to the siblings of the given parent.
     *
     * @return  void
     */
    public function orderingSearch()
    {
        $this->checkAccess();

        $app      = Factory::getApplication();
        $db       = Factory::getDbo();
        $term     = trim((string) $app->getInput()->getString('term', ''));
        $parentId = (int) $app->getInput()->getInt('parent_id', 0);

        $results = [];

        if (!$parentId) {
            $this->sendJson($results);

            return;
        }

        $first = Text::_('COM_HWDLINKS_ITEM_FIELD_ORDERING_VALUE_FIRST');
        $last  = Text::_('COM_HWDLINKS_ITEM_FIELD_ORDERING_VALUE_LAST');

        if ($term === '' || stripos($first, $term) !== false) {
            $results[] = ['value' => '-1', 'text' => $first];
        }

        $query = $db->getQuery(true)
            ->select('a.id AS value, a.title AS text')
            ->from($db->quoteName('#__hwdlinks_link', 'a'))
            ->where('a.parent_id = ' . $parentId);

        if ($term !== '') {
            $query->where('a.title LIKE ' . $db->quote('%' . $term . '%'));
        }

        $query->order('a.lft ASC');

        $db->setQuery($query);
        $options = $db->loadObjectList();

        foreach ($options as $option) {
            $results[] = ['value' => $option->value, 'text' => strip_tags($option->text)];
        }

        if ($term === '' || stripos($last, $term) !== false) {
            $results[] = ['value' => '-2', 'text' => $last];
        }

        $this->sendJson($results);
    }

    /**
     * Guards the AJAX search tasks: must be a logged-in backend user
     * with permission to create or edit links.
     *
     * @return  void
     */
    protected function checkAccess()
    {
        $user = Factory::getApplication()->getIdentity();

        if (!$user->authorise('core.edit', 'com_hwdlinks') && !$user->authorise('core.create', 'com_hwdlinks')) {
            $this->sendJson([]);
        }
    }

    /**
     * Sends a JSON response and closes the application, bypassing the
     * normal HTML document rendering.
     *
     * @param   array  $data  The data to encode as JSON.
     *
     * @return  void
     */
    protected function sendJson($data)
    {
        Factory::getApplication()->setHeader('Content-Type', 'application/json; charset=utf-8', true);
        echo json_encode($data);
        Factory::getApplication()->close();
    }
}
