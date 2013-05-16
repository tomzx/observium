<?php

if ($device['os'] == 'drac')
{
  echo(" DELL-RAC-MIB ");

  // table: CMC power information
  $oids = snmpwalk_cache_oid($device, "drsCMCPowerTable", array(), "DELL-RAC-MIB");

  if (is_array($oids))
  {
    foreach ($oids as $index => $entry)
    {
      $descr = "Chassis ".$entry['drsChassisIndex'];
      $oid = ".1.3.6.1.4.1.674.10892.2.4.1.1.13.".$index;
      discover_sensor($valid['sensor'], 'power', $device, $oid, $index, 'dell-rac', $descr, 1, 1, NULL, NULL, NULL, NULL, $entry['drsWattsReading']);
    }
  }

}

?>
