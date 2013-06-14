<?php
// DeltaUPS-MIB
if ($device['os'] == "deltaups")
{
  echo("DeltaUPS-MIB ");

  $dupsHumidity = array( 
    array('OID' => "1.3.6.1.4.1.2254.2.4.10.2.0", 
          'descr' => "Environment",
          'divisor' => 1
         )
  );

  foreach ($dupsHumidity as $eachArray => $eachValue)
  {
    // DeltaUPS does not have tables, so no need to walk, only need snmpget
    $current = snmp_get($device, $eachValue['OID'], "-O vq");
    // Get index values from current OID
    $preIndex = strstr($eachValue['OID'], '2254.2.4');
    // Format and strip index to only include everything after 2254.2.4
    $index = substr($preIndex, 9);

    // Prevent NULL returned values from being added as sensors
    if($current != "NULL" )
    {
      discover_sensor($valid['sensor'], 'humidity', $device, $eachValue['OID'], $index, "DeltaUPS", $eachValue['descr'], $eachValue['divisor'], '1', NULL, NULL, NULL, NULL, $current);
    }
  }
}
