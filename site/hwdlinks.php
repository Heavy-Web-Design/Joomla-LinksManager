<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_hwdlinks
 *
 * @copyright   Copyright (C) 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Get an instance of the controller prefixed by HelloWorld
$controller = JControllerLegacy::getInstance('HwdLinks');

// Loading some helpers
JLoader::register('ContentHelperRoute', JPATH_BASE . '/components/com_content/helpers/route.php');

//if($date === null )
//{
  $date = JFactory::getDate();
  $tz = JFactory::getConfig()->get( 'offset' );

  //var_dump($tz);
  //die();

  //$date->setTimezone(new DateTimeZone($tz)); //here!
  date_default_timezone_set($tz);
//var_dump(date_default_timezone_get());
//die();
  //$date = $date->format( "Y-m-d" );
//}

// Perform the Request task
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));

// Redirect if set by the controller
$controller->redirect();
