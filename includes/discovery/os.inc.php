<?php

$os = get_device_os($device);

if ($os != $device['os'])
{
  dbUpdate(array('os' => $os), 'devices', '`device_id` = ?', array($device['device_id']));
  echo("Changed OS! : $os\n");
  log_event("Device OS changed ".$device['os']." => $os", $device, 'system');
  $device['os'] = $os;
}

?>
