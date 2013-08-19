<?php

global $debug, $ipmi_sensors;

include_once("includes/polling/functions.inc.php");
/// FIXME. From this uses only check_valid_sensors(), maybe need move to global functions or copy to polling. --mike
include_once("includes/discovery/functions.inc.php");

echo("IPMI: ");

if ($ipmi['host'] = get_dev_attrib($device,'ipmi_hostname'))
{
  $ipmi['user'] = get_dev_attrib($device,'ipmi_username');
  $ipmi['password'] = get_dev_attrib($device,'ipmi_password');

  if ($config['own_hostname'] != $device['hostname'] || $ipmi['host'] != 'localhost')
  {
    $remote = " -H " . $ipmi['host'] . " -L USER -U " . $ipmi['user'] . " -P " . $ipmi['password'];
  }

  $ipmi_start = utime();

  $results = external_exec($config['ipmitool'] . $remote . " sensor 2>/dev/null");

  $ipmi_end = utime(); $ipmi_time = round(($ipmi_end - $ipmi_start) * 1000);

  echo('(' . $ipmi_time . 'ms) ');

  $ipmi_sensors = parse_ipmitool_sensor($device, $results);
}

if ($debug)
{
  print_vars($ipmi_sensors);
}

foreach ($config['ipmi_unit'] as $type)
{
  check_valid_sensors($device, $type, $ipmi_sensors, 'ipmi');
}

echo("\n");

?>
