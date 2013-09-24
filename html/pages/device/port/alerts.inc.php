<?php

$alert_rules = cache_alert_rules();
$alert_assoc = cache_alert_assoc();
$alert_table = cache_device_alert_table($device['device_id']);

$vars['pagination'] = TRUE;
if(!$vars['pagesize']) { $vars['pagesize'] = 50; }
if(!$vars['pageno']) { $vars['pageno'] = 1; }

print_alert_table(array('entity_type' => 'port', 'entity_id' => $port['port_id']));


?>
