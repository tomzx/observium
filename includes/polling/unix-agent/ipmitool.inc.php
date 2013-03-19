<?php

global $agent_sensors;

if ($agent_data['ipmitool']['sensor'] != '|')
{
  echo "IPMI: ";

  // Parse function returns array, don't overwrite this array, other agent modules could also have filled this out, so merge!
  $agent_sensors = array_merge($agent_sensors,parse_ipmitool_sensor($device, $agent_data['ipmitool']['sensor'], 'agent'));
}

?>