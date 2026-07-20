<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols
/**
 * @package     HwdLinks.Administrator
 * @subpackage  com_hwdlinks
 *
 * @copyright   Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace HWD\Component\HwdLinks\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;


class LinksModel extends ListModel
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id',
                'title',
                'lft',
                'published'
            ];
        }

        parent::__construct($config);
    }


    protected function populateState($ordering = 'lft', $direction = 'asc')
    {

        $app = Factory::getApplication();

        // Adjust the context to support modal layouts.
        if ($layout = $app->input->get('layout')) {
            $this->context .= '.' . $layout;
        }
        parent::populateState($ordering, $direction);
    }


    protected function getListQuery()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        // Create the base select statement.
        $query->select('*')
                ->from($db->quoteName('#__hwdlinks_link'));

        // Filter: like / search
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            $like = $db->quote('%' . $search . '%');
            $query->where('title LIKE ' . $like);
        }

        // Filter by published state
        $published = $this->getState('filter.published');

        if (is_numeric($published)) {
            $query->where('published = ' . (int) $published);
        } elseif ($published === '') {
            $query->where('(published IN (0, 1))');
        }

        // exclude root hwdlinks record
        $query->where('id > 1');

        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering', 'lft');
        $orderDirn = $this->state->get('list.direction', 'asc');

        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }
}
