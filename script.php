<?php

\defined('_JEXEC') or die;

/*
 * @package     \HwdLinks\.Script
 * @subpackage  com_hwdlinks
 *
 * @copyright   Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;



return new class () implements InstallerScriptInterface
{
    /**
     * Runs just before any installation action is preformed on the component.
     * Verifications and pre-requisites should run in this function.
     *
     * @param  string    $type   - Type of PreFlight action. Possible values are:
     *                           - * install
     *                           - * update
     *                           - * discover_install
     * @param  \stdClass $parent - Parent object calling object.
     *
     * @return void
     */
    public function preflight(string $type, InstallerAdapter $parent): bool
    {
        // Check for minimum PHP version
        if (version_compare(PHP_VERSION, '7.4.0', '<')) {
            throw new \RuntimeException('This component requires PHP 7.4 or later.');
        }

        // Check for minimum Joomla version
        if (version_compare(JVERSION, '4.0.0', '<')) {
            throw new \RuntimeException('This component requires Joomla 4.0 or later.');
        }

        echo '<p>' . Text::_('COM_HWDLINKS_PREFLIGHT_' . $type . '_TEXT') . '</p>';

        return true;
    }

    /**
     * This method is called after a component is installed.
     *
     * @param  \stdClass $parent - Parent object calling this method.
     *
     * @return void
     */
    public function install(InstallerAdapter $parent): bool
    {
        $app = Factory::getApplication();
        $app->redirect(Route::_('index.php?option=com_hwdlinks', false));
        //$parent->getParent()->setRedirectURL('index.php?option=com_hwdlinks');
        return true;
    }

    /**
     * This method is called after a component is updated.
     *
     * @param  \stdClass $parent - Parent object calling object.
     *
     * @return void
     */
    public function update(InstallerAdapter $parent): bool
    {
        echo '<p>' . Text::sprintf('COM_HWDLINKS_UPDATE_TEXT', $parent->getManifest()->version) . '</p>';
        return true;
    }

    /**
     * Runs right after any installation action is preformed on the component.
     *
     * @param  string    $type   - Type of PostFlight action. Possible values are:
     *                           - * install
     *                           - * update
     *                           - * discover_install
     * @param  \stdClass $parent - Parent object calling object.
     *
     * @return void
     */
    public function postflight(string $type, InstallerAdapter $parent): bool
    {
        $db = Factory::getDbo();

        echo '<p>Checking if the root record is already present ...</p>';

        $query = $db->getQuery(true);
        $query->select('id');
        $query->from('#__hwdlinks_link');
        $query->where('id = 1');
        //$query->where('alias = "hwdlinks-root-alias"');
        $db->setQuery($query);
        $id = $db->loadResult();

        if ($id == '1') {   // assume tree structure already built
            echo '<p>Root record already present, install program exiting ...</p>';
            return true;
        }

        echo '<p>Checking if there is a record with id = 1 ...</p>';

        $query = $db->getQuery(true);
        $query->select('id');
        $query->from('#__hwdlinks_link');
        $query->where('id = 1');
        $db->setQuery($query);
        $id = $db->loadResult();

        if ($id) {
            echo '<p>Record with id = 1 found</p>';

            // get new id
            $query = $db->getQuery(true)
                ->select('max(id) + 1')
                ->from('#__hwdlinks_link');
            $db->setQuery($query);
            $newid = $db->loadResult();
            echo "<p>Changing id to $newid</p>";

            // update id in hwdlinks_link table
            $query = $db->getQuery(true)
                ->update('#__hwdlinks_link')
                ->set("id = $newid")
                ->where("id = $id");
            $db->setQuery($query);
            $result = $db->execute();

            if ($result) {
                $nrows = $db->getAffectedRows();
                echo "<p>Id in helloworld table changed, records updated: $nrows</p>";
            } else {
                echo "<p>Error: Id in hwdlinks_link table not changed</p>";
                var_dump($result);
            }

            // update id in the associations table
            /*$query = $db->getQuery(true)
                ->update('#__associations')
                ->set("id = $newid")
                ->where("id = $id")
                ->where('context = "com_helloworld.item"');
            $db->setQuery($query);
            $result = $db->execute();
            if ($result)
            {
                $nrows = $db->getAffectedRows();
                echo "<p>Id in associations table changed, records updated: $nrows</p>";
            }
            else
            {
                echo "<p>Error: Id in associations table not changed</p>";
                var_dump($result);
            }*/

            // update id in the assets table
            $query = $db->getQuery(true)
                ->update('#__assets')
                ->set('name = "com_hwdlinks.link.' . $newid . '"')
                ->where('name = "com_hwdlinks.link.' . $id . '"');
            $db->setQuery($query);
            $result = $db->execute();
            if ($result) {
                $nrows = $db->getAffectedRows();
                echo "<p>Id in assets table changed, records updated: $nrows</p>";
            } else {
                echo "<p>Error: Id in assets table not changed</p>";
                var_dump($result);
            }
        } else {
            echo '<p>No record with id = 1 found</p>';
        }

        // find number of records in helloworld table
        $query = $db->getQuery(true)
            ->select('count(*)')
            ->from('#__hwdlinks_link');
        $db->setQuery($query);
        $total = $db->loadResult();

        // insert root record
        $columns = ['id', 'title', 'published', 'parent_id', 'rgt', 'alias'];
        $values = [1, 'links root', 1, 0, 2 * (int)$total + 1, ''];

        $query = $db->getQuery(true)
            ->insert('#__hwdlinks_link')
            ->columns($db->quoteName($columns))
            ->values(implode(',', $db->quote($values)));
        $db->setQuery($query);
        $result = $db->execute();
        if ($result) {
            $nrows = $db->getAffectedRows();
            echo "<p>$nrows inserted into hwdlinks_link table</p>";
        } else {
            echo "<p>Error creating root record</p>";
            var_dump($result);
        }

        // update lft and rgt for each of the other records (ie not root)
        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__hwdlinks_link')
            ->where('id > 1');
        $db->setQuery($query);
        $ids = $db->loadColumn();
        for ($i = 0; $i < $total; $i++) {
            $lft = 2 * (int)$i + 1;
            $rgt = 2 * (int)$i + 2;
            $query = $db->getQuery(true)
                ->update('#__hwdlinks_link')
                ->set("lft = {$lft}")
                ->set("rgt = {$rgt}")
                ->where("id = {$ids[$i]}");
            $db->setQuery($query);
            $result = $db->execute();
            if ($result) {
                $nrows = $db->getAffectedRows();
                echo "<p>$nrows updated in hwdlinks_link table, for id = {$ids[$i]}</p>";
            } else {
                echo "<p>Error updating record</p>";
                var_dump($result);
            }
        }

        return true;
    }

    /**
     * This method is called after a component is uninstalled.
     *
     * @param  \stdClass $parent - Parent object calling this method.
     *
     * @return void
     */
    public function uninstall(InstallerAdapter $parent): bool
    {
        echo '<p>' . Text::_('COM_HWDLINKS_UNINSTALL_TEXT') . '</p>';
        return true;
    }
};
