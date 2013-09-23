<?php

/**
 * Observium Network Management and Monitoring System
 * Copyright (C) 2006-2013, Adam Armstrong - http://www.observium.org
 *
 * @package    observium
 * @subpackage webui
 * @author     Adam Armstrong <adama@memetic.org>
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */


$navbar['class'] = 'navbar-narrow';
$navbar['brand'] = 'Alerting';

$pages = array('alerts' => 'Alerts', 'alert_checks' => 'Alert Checkers');

foreach ($pages as $page_name => $page_desc)
{
  if ($vars['page'] == $page_name)
  {
    $navbar['options'][$page_name]['class'] = "active";
  }
  $navbar['options'][$page_name]['url'] = generate_url($vars, array('page' => $page_name));
  $navbar['options'][$page_name]['text'] = htmlspecialchars($page_desc);
}

$navbar['options_right']['update']['url']  = generate_url($vars, array('page' => 'alert_checks', 'action'=>'update'));
$navbar['options_right']['update']['text'] = 'Regenerate';
$navbar['options_right']['update']['icon'] = 'oicon-arrow-circle';
// We don't really need to highlight Regenerate, as it's not a display option, but an action.
// if ($vars['action'] == 'update') { $navbar['options_right']['update']['class'] = 'active'; }

$navbar['options_right']['add']['url']  = generate_url(array('page' => 'add_alert_check'));
$navbar['options_right']['add']['text'] = 'Add Checker';
$navbar['options_right']['add']['icon'] = 'oicon-plus-circle';

// Print out the navbar defined above
print_navbar($navbar);
unset($navbar);

// Run Actions

if(($_SESSION['userlevel'] < 10))
{
 // No editing for you!
}
elseif($vars['action'] == 'update')
{
  echo '<div class="well">';
  foreach(dbFetchRows("SELECT * FROM `devices`") AS $device)
  {
    update_device_alert_table($device);
  }
  echo '</div>';

  unset($vars['action']);
}
elseif($vars['submit'] == "delete_alert_checker" && $vars['confirm'] == "confirm")
{
    // Maybe expand this to output more info.

    dbDelete('alert_tests', '`alert_test_id` = ?', array($vars['alert_test_id']));
    dbDelete('alert_table', '`alert_test_id` = ?', array($vars['alert_test_id']));
    dbDelete('alert_assoc', '`alert_test_id` = ?', array($vars['alert_test_id']));
    print_message("Deleting all traces of alert checker ".$vars['alert_test_id']);
}

//EOF
