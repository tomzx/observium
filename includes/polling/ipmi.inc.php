<?php

global $ipmi_sensors;

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

  $index = 0;

  foreach (explode("\n",$results) as $row)
  {
    $index++;

    # BB +1.1V IOH     | 1.089      | Volts      | ok    | na        | 1.027     | 1.054     | 1.146     | 1.177     | na
    list($desc,$current,$unit,$state,$low_nonrecoverable,$low_limit,$low_warn,$high_warn,$high_limit,$high_nonrecoverable) = explode('|',$row);

    if (trim($current) != "na" && $config['ipmi_unit'][trim($unit)])
    {
      discover_sensor($valid['sensor'], $config['ipmi_unit'][trim($unit)], $device, '', $index, 'ipmi', trim($desc), '1', '1',
        (trim($low_limit) == 'na' ? NULL : trim($low_limit)), (trim($low_warn) == 'na' ? NULL : trim($low_warn)),
        (trim($high_warn) == 'na' ? NULL : trim($high_warn)), (trim($high_limit) == 'na' ? NULL : trim($high_limit)),
        $current, 'ipmi');

      $ipmi_sensors[$config['ipmi_unit'][trim($unit)]]['ipmi'][$index] = array('description' => $desc, 'current' => $current, 'index' => $index, 'unit' => $unit);
    }
  }

  unset($ipmi_sensor);
}

foreach ($config['ipmi_unit'] as $type)
{
  check_valid_sensors($device, $type, $valid['sensor'], 'ipmi');
}

echo("\n");

?>
