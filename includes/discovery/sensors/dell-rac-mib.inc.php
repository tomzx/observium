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

      $oid = ".1.3.6.1.4.1.674.10892.2.4.1.1.13.".$index;
      discover_sensor($valid['sensor'], 'power', $device, $oid, $index, 'dell-rac', $descr, 1, 1, NULL, NULL, NULL, NULL, $entry['drsWattsReading']);
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

      $oid = ".1.3.6.1.4.1.674.10892.2.4.2.1.5.".$index;
      $low = NULL;
      $high = NULL;

      ## FIXME this type of inventing/calculating should be done in the Observium voltage function instead!
      if ($entry['drsPSUVoltsReading'] > 360 and $entry['drsPSUVoltsReading'] < 440)
      {
        // european 400V +/- 10%
        $low = 360;
        $high = 440;
      }
      if ($entry['drsPSUVoltsReading'] > 207 and $entry['drsPSUVoltsReading'] < 253)
      {
        // european 230V +/- 10%
        $low = 207;
        $high = 253;
      }
      if ($entry['drsPSUVoltsReading'] > 99 and $entry['drsPSUVoltsReading'] < 121)
      {
        // american 110V +/- 10%
        $low = 99;
        $high = 121;
      }

      discover_sensor($valid['sensor'], 'voltage', $device, $oid, $index, 'dell-rac', $descr, 1, 1, $low, NULL, NULL, $high, $entry['drsPSUVoltsReading']);
    }
  }

  // FIXME: temperatures could be rewritten to walk tables, like the other sensors above, perhaps? Unless these are all of them...
  $drac = array();
  $drac['front']['desc'] = "Chassis Front Panel Temperature";
  $drac['front']['oid'] = ".1.3.6.1.4.1.674.10892.2.3.1.10.0";

  $drac['cmcambient']['desc'] = "CMC Ambient Temperature";
  $drac['cmcambient']['oid'] = ".1.3.6.1.4.1.674.10892.2.3.1.11.0";

  $drac['cmccpu']['desc'] = "CMC Processor Temperature";
  $drac['cmccpu']['oid'] = ".1.3.6.1.4.1.674.10892.2.3.1.12.0";

  foreach ($drac as $index => $dsens)
  {
    $temp  = snmp_get($device, $dsens['oid'], "-Oqv");
  
    if ($dsens['desc'] != "" && is_numeric($temp) && $temp > "0")
    {
      discover_sensor($valid['sensor'], 'temperature', $device, $dsens['oid'], $index, 'dell-rac', $dsens['desc'], 1, 1, NULL, NULL, NULL, NULL, $temp);
    }
  }
}

// EOF