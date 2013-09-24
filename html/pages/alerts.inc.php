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

include($config['html_dir']."/includes/alerting-navbar.inc.php");

// Run actions

if($vars['action'] == 'update')
{
  foreach(dbFetchRows("SELECT * FROM `devices`") AS $device)
  {
    update_device_alert_table($device);
  }

  unset($vars['action']);

}

$navbar['class'] = "navbar-narrow";
$navbar['brand'] = "Alert Types";

$types = dbFetchRows("SELECT `entity_type` FROM `alert_table` GROUP BY `entity_type`");

$navbar['options']['all']['url'] = generate_url($vars, array('page' => 'alerts', 'entity_type' => 'all'));
$navbar['options']['all']['text'] = htmlspecialchars(nicecase('all'));
if ($vars['entity_type'] == 'all') {
  $navbar['options']['all']['class'] = "active";
  $navbar['options']['all']['url'] = generate_url($vars, array('page' => 'alerts', 'entity_type' => NULL));
}

foreach ($types as $thing)
{
  if (!$vars['entity_type']) { $vars['entity_type'] = $thing['entity_type']; }
  if ($vars['entity_type'] == $thing['entity_type'])
  {
    $navbar['options'][$thing['entity_type']]['class'] = "active";
    $navbar['options'][$thing['entity_type']]['url'] = generate_url($vars, array('page' => 'alerts', 'entity_type' => NULL));
  } else {
    $navbar['options'][$thing['entity_type']]['url'] = generate_url($vars, array('page' => 'alerts', 'entity_type' => $thing['entity_type']));
  }
  $navbar['options'][$thing['entity_type']]['text'] = htmlspecialchars(nicecase($thing['entity_type']));
}

$navbar['options_right']['alarmed']['url']  = generate_url($vars, array('page' => 'alerts', 'alerted' => '1'));
$navbar['options_right']['alarmed']['text'] = 'Alarmed Only';
$navbar['options_right']['alarmed']['icon'] = 'oicon-exclamation-red';
if ($vars['alerted'] == '1') { $navbar['options_right']['alarmed']['class'] = 'active';
$navbar['options_right']['alarmed']['url']  = generate_url($vars, array('page' => 'alerts', 'alerted' => NULL));}

// Print out the navbar defined above
print_navbar($navbar);

// Cache the alert_tests table for use later
$alert_rules = cache_alert_rules($vars);

// Print out a table of alerts matching $vars
$vars['pagination'] = 1;
print_alert_row($vars);

?>
