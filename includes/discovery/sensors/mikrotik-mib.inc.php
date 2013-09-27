<?php

// Mikrotik RouterOS


if ($device['os'] == "routeros")
{
  echo(" MIKROTIK-MIB ");

  # MIKROTIK-MIB::mtxrHlTemperature.0 = INTEGER: 22.0
  $oids = snmpwalk_cache_oid($device, "mtxrHlTemperature", array(), "MIKROTIK-MIB", mib_dirs('mikrotik'));

  if (is_array($oids))
  {
    foreach ($oids as $index => $entry)
    {
      $descr = "System ".$index;
      $oid  = "1.3.6.1.4.1.14988.1.1.3.10.".$index;
      $temperature = $entry['mtxrHlTemperature'] / 10;
      discover_sensor($valid['sensor'], 'temperature', $device, $oid, $index, 'routeros', $descr, 10, 1, NULL, NULL, NULL, NULL, $temperature);
    }
  }

  # MIKROTIK-MIB::mtxrHlVoltage.0 = INTEGER: 13.4
  $oids = snmpwalk_cache_oid($device, "mtxrHlVoltage", array(), "MIKROTIK-MIB", mib_dirs('mikrotik'));

  if (is_array($oids))
  {
    foreach ($oids as $index => $entry)
    {
      $descr = "System ".$index;
      $oid  = "1.3.6.1.4.1.14988.1.1.3.8.".$index;
      $voltage = $entry['mtxrHlVoltage'] / 10;
      discover_sensor($valid['sensor'], 'voltage', $device, $oid, $index, 'routeros', $descr, 10, 1, NULL, NULL, NULL, NULL, $voltage);
    }
  }
}
?>
