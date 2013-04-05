<?php

// Mikrotik RouterOS

# MIKROTIK-MIB::mtxrHlTemperature.0 = INTEGER: 22.0

if ($device['os'] == "routeros")
{
  echo("Mikrotik RouterOS ");

  $oids = snmpwalk_cache_oid($device, "mtxrHlTemperature", array(), "MIKROTIK-MIB", mib_dirs('mikrotik'));

  if (is_array($oids))
  {
    foreach ($oids as $index => $entry)
    {
      $descr = "System ".$index;
      $divisor = 10;
      $oid  = "1.3.6.1.4.1.14988.1.1.3.10.".$index;
      $temperature = $entry['mtxrHlTemperature'] / $divisor;
      discover_sensor($valid['sensor'], 'temperature', $device, $oid, $index, 'routeros', $descr, $divisor, '1', NULL, NULL, NULL, NULL, $temperature);
    }
  }
}

?>
