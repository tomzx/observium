<?php

foreach (dbFetchRows("SELECT * FROM `devices` ORDER BY `hostname`") as $device)
{

  if (device_permitted($device))
  {

    // Process device and add all the human-readable stuff.
    humanize_device($device);

    $cache['devices']['hostname'][$device['hostname']] = $device['device_id'];
    $cache['devices']['id'][$device['device_id']] = $device;

    if ($device['disabled'] == 1 && !$config['web_show_disabled']) { continue; }

    $devices['count']++;

    if ($device['status'] == 0)   { $devices['down']++; }
    if ($device['status'] == 1)   { $devices['up']++; }

    $cache['devices']['timers']['polling'] += $device['last_polled_timetaken'];
    $cache['devices']['timers']['discovery'] += $device['last_discovered_timetaken'];

    $cache['device_types'][$device['type']]++;
  }
}

foreach (dbFetchRows("SELECT port_id, ifAdminStatus, ifOperStatus FROM `ports`") as $port)
{
  if (port_permitted($port))
  {
    if ($port['ifAdminStatus'] == "down")
    {
      $ports['disabled']++;
    } else {
      if ($port['ifOperStatus'] == "up") { $ports['up']++; }
      if ($port['ifOperStatus'] == "down" || $port['ifOperStatus'] == "lowerLayerDown") { $ports['down']++; }
    }
  }
}

?>
