<?php

// Mikrotik RouterOS

# MIKROTIK-MIB::mtxrHlVoltage.0 = INTEGER: 13.4

if ($device['os'] == "routeros")
{
  echo("Mikrotik RouterOS ");

  $oids = snmpwalk_cache_oid($device, "mtxrHlVoltage", array(), "MIKROTIK-MIB", $config['mib_dir'].":".$config['mib_dir'] );

  if (is_array($oids))
  {
    foreach ($oids as $index => $entry)
    {
      $descr = "System ".$index;
      $divisor = 10;
      $oid  = "1.3.6.1.4.1.14988.1.1.3.8.".$index;
      $voltage = $entry['mtxrHlVoltage'] / $divisor;
      discover_sensor($valid['sensor'], 'voltage', $device, $oid, $index, 'routeros', $descr, $divisor, '1', NULL, NULL, NULL, NULL, $voltage);
    }
  }
}
?>
