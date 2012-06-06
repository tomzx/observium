<?php

global $ipmi_sensors;

include_once("includes/discovery/functions.inc.php");

if ($ipmi['host'] = get_dev_attrib($device,'ipmi_hostname'))
{
  $ipmi['user'] = get_dev_attrib($device,'ipmi_username');
  $ipmi['password'] = get_dev_attrib($device,'ipmi_password');

  echo("IPMI: ");

  if ($config['own_hostname'] != $device['hostname'] || $ipmi['host'] != 'localhost')
  {
    $remote = " -H " . $ipmi['host'] . " -L USER -U " . $ipmi['user'] . " -P " . $ipmi['password'];
  }

  $results = external_exec($config['ipmitool'] . " -c " . $remote . " sdr 2>/dev/null");

  $index = 0;

  foreach (explode("\n",$results) as $row)
  {
    $index++;
    list($descr,$current,$unit,$status) = explode(',',$row);
    
    if (trim($current) != "na" && $config['ipmi_unit'][trim($unit)])
    {
      discover_sensor($valid['sensor'], $config['ipmi_unit'][$unit], $device, '', $index, 'ipmi', $descr, '1', '1', NULL, NULL, NULL, NULL, $current, 'ipmi');
      $ipmi_sensors[$config['ipmi_unit'][$unit]]['ipmi'][$index] = array('description' => $descr, 'current' => $current, 'index' => $index, 'unit' => $unit);
    }
  }

  foreach ($config['ipmi_unit'] as $type)
  {
    check_valid_sensors($device, $type, $valid['sensor'], 'ipmi');
  }
  echo("\n");
      
  unset($ipmi_sensor);
}

?>
