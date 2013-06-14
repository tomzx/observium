<?php
// DeltaUPS-MIB
if ($device['os'] == "deltaups")
{
  echo("DeltaUPS-MIB ");

  $dupsVoltage = array(
    array('OID' => "1.3.6.1.4.1.2254.2.4.7.6.0",
          'descr' => "Battery",
          'divisor' => 10
         ),
    array('OID' => "1.3.6.1.4.1.2254.2.4.5.4.0",
          'descr' => "Output",
          'divisor' => 10
         ),
    array('OID' => "1.3.6.1.4.1.2254.2.4.4.3.0",
          'descr' => "Input",
          'divisor' => 10
         ),
    array('OID' => "1.3.6.1.4.1.2254.2.4.6.3.0",
          'descr' => "Bypass",
          'divisor' => 10
         )
  );

  foreach ($dupsVoltage as $eachArray => $eachValue)
  {
    // DeltaUPS does not have tables, so no need to walk, only need snmpget
    $current = snmp_get($device, $eachValue['OID'], "-O vq");
    // Get index values from current OID
    $preIndex = strstr($eachValue['OID'], '2254.2.4');
    // Format and strip index to only include everything after 2254.2.4
    $index = substr($preIndex, 9);

    // Prevent NULL returned values from being added as sensors
    if ($current != "NULL")
    {
        discover_sensor($valid['sensor'], 'voltage', $device, $eachValue['OID'], $index, "DeltaUPS", $eachValue['descr'], $eachValue['divisor'], '1', NULL, NULL, NULL, NULL, $current);
    }
  }
}
