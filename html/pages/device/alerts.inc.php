<?php

$alert_rules = cache_alert_rules();
$alert_assoc = cache_alert_assoc();
$alert_table = cache_device_alert_table($device['device_id']);

// Build Navbar

$navbar['class'] = "navbar-narrow";
$navbar['brand'] = "Alert Types";

foreach ($alert_table as $entity_type => $thing)
{

  if (!$vars['entity_type']) { $vars['entity_type'] = $entity_type; }
  if ($vars['entity_type'] == $entity_type) { $navbar['options'][$entity_type]['class'] = "active"; }

  $navbar['options'][$entity_type]['url'] = generate_url(array('page' => 'device', 'device' => $device['device_id'], 
                                                  'tab' => 'alerts', 'entity_type' => $entity_type));
  $navbar['options'][$entity_type]['text'] = htmlspecialchars(nicecase($entity_type));
}

$navbar['options_right']['update']['url']  = generate_url(array('page' => 'device', 'device' => $device['device_id'], 'tab' => 'alerts', 'action'=>'update'));
$navbar['options_right']['update']['text'] = 'Regenerate';
if ($vars['action'] == 'update') { $navbar['options_right']['update']['class'] = 'active'; }

print_navbar($navbar);

// Run actions

if($vars['action'] == 'update')
{
  echo '<div class="well">';
  update_device_alert_table($device);
  $alert_table = cache_device_alert_table($device['device_id']);
  echo '</div>';
}

  $vars['pagination'] = TRUE;
  if(!$vars['pagesize']) { $vars['pagesize'] = 50; }
  if(!$vars['pageno']) { $vars['pageno'] = 1; }

  print_alert_table($vars);

?>
