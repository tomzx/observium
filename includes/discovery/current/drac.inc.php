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
      $oid = ".1.3.6.1.4.1.674.10892.2.4.1.1.14.".$index;
      discover_sensor($valid['sensor'], 'current', $device, $oid, $index, 'dell-rac', $descr, 1, 1, NULL, NULL, NULL, NULL, $entry['drsAmpsReading']);
    }
  }

  unset($oids);

  // table: CMC PSU info
  $oids = snmpwalk_cache_oid($device, "drsCMCPSUTable", array(), "DELL-RAC-MIB");

  if (is_array($oids))
  {
    foreach ($oids as $index => $entry)
    {
      $descr = "Chassis ".$entry['drsPSUChassisIndex']." ".$entry['drsPSULocation'];
      $oid = ".1.3.6.1.4.1.674.10892.2.4.2.1.6.".$index;
      discover_sensor($valid['sensor'], 'current', $device, $oid, $index, 'dell-rac', $descr, 1, 1, NULL, NULL, NULL, NULL, $entry['drsPSUAmpsReading']);
    }
  }

}

?>
