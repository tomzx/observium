<?php

foreach (dbFetchRows("SELECT * FROM `devices` ORDER BY `hostname`") as $device)
{
  if (get_dev_attrib($device,'override_sysLocation_bool'))
  {
    $device['real_location'] = $device['location'];
    $device['location'] = get_dev_attrib($device,'override_sysLocation_string');
  }

  $devices['count']++;

  $cache['devices']['hostname'][$device['hostname']] = $device['device_id'];
  $cache['devices']['id'][$device['device_id']] = $device;

  $cache['device_types'][$device['type']]++;
}

?>
