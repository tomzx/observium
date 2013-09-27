<?php

if ($device['os'] == "seos")
{
  $descr_data = snmp_walk($device, ".1.3.6.1.4.1.2352.2.4.1.6.1.2", "-Oqv", "");
  $oid_value_data = snmp_walk($device, ".1.3.6.1.4.1.2352.2.4.1.6.1.3", "-Osqn", "");
  $descr_values = array_map(NULL, explode("\n", $descr_data), explode("\n", $oid_value_data));
  if ($descr_values)
  {
    echo("SmartEdge OS: ");
    foreach ($descr_values as $index => $descr_value)
    {
      $descr = $descr_value[0];
      $descr = str_replace("Temperature sensor on", "", $descr);
      #oid_value[0] = oid
      #oid_value[1] = temperature value
      $oid_value = explode(" ", $descr_value[1]);
      if ($descr != "")
      {
        discover_sensor($valid['sensor'], 'temperature', $device, $oid_value[0], $index, 'seos', $descr, '1', '1', NULL, NULL, NULL, NULL, $oid_value[1]);
      }
    }
  }
}

/* End of file seos.inc.php */
