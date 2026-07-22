<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_hwdlinks
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Access check: is this user allowed to access the backend of this component?
if (!JFactory::getUser()->authorise('core.manage', 'com_hwdlinks')) 
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Get an instance of the controller prefixed by HelloWorld
$controller = JControllerLegacy::getInstance('HwdLinks');

//Loading some extra language files
$lang = JFactory::getLanguage();
$lang->load('com_content');

/*if($date === null )
//{
  $date = JFactory::getDate();
  $tz = JFactory::getConfig()->get( 'offset' );

  var_dump($tz);
  die();

  $date->setTimezone(new DateTimeZone($tz)); //here!

  $date = $date->format( "Y-m-d" );
//}*/

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
