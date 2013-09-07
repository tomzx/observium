<?php

global $agent_sensors;

if ($agent_data['hddtemp'] != '|')
{


  $disks = explode('||',trim($agent_data['hddtemp'],'|'));

  if (count($disks))
  {
    echo "hddtemp: ";
    foreach ($disks as $disk)
    {
      list($blockdevice,$descr,$value,$unit) = explode('|',$disk,4);
      $diskcount++;
      discover_sensor($valid['sensor'], 'temperature', $device, '', $diskcount, 'hddtemp', "$blockdevice: $descr", '1', '1', NULL, NULL, NULL, NULL, $value, 'agent');
      $agent_sensors['temperature']['hddtemp'][$blockdevice] = array('description' => "$blockdevice: $descr", 'current' => $value, 'index' => $diskcount);
    }
    echo "\n";
  }
}

?>
