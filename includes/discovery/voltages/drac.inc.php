<?php

if ($device['os'] == 'drac')
{
  echo(" DELL-RAC-MIB ");

  // table: CMC PSU info
  $oids = snmpwalk_cache_oid($device, "drsCMCPSUTable", array(), "DELL-RAC-MIB");

  if (is_array($oids))
  {
    foreach ($oids as $index => $entry)
    {
      $descr = "Chassis ".$entry['drsPSUChassisIndex']." ".$entry['drsPSULocation'];
      $oid = ".1.3.6.1.4.1.674.10892.2.4.2.1.5.".$index;
      $low = NULL;
      $high = NULL;
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

}

?>
