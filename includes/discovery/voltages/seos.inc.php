<?php

if ($device['os'] == "seos")
{
  $descr_data = snmp_walk($device, ".1.3.6.1.4.1.2352.2.4.1.3.1.2", "-Oqv", "");
  $oid_value_data = snmp_walk($device, ".1.3.6.1.4.1.2352.2.4.1.3.1.4", "-Osqn", "");
  $desired_voltages = snmp_walk($device, ".1.3.6.1.4.1.2352.2.4.1.3.1.3", "-Oqv", "");
  $descr_values = array_map(NULL, explode("\n", $descr_data), explode("\n", $oid_value_data), explode("\n", $desired_voltages));
  if ($descr_values)
  {
    echo("SmartEdge OS: ");
    foreach ($descr_values as $index => $descr_value)
    {
      $descr = $descr_value[0];
      list($oid, $value) = explode(" ", $descr_value[1]);
      $desired = $descr_value[2];
      $low_limit = ($desired * 0.85) / 1000;
      $high_limit = ($desired * 1.15) / 1000;
      if ($descr != "" and $value)
      {
        discover_sensor($valid['sensor'], 'voltage', $device, $oid, $index, 'seos', $descr, '1000', '1', $low_limit, NULL, $high_limit, NULL, $value);
      }
    }
  }
}

/* End of file seos.inc.php */
