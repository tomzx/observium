<?php

if ($device['os'] == "drac")
{
  echo("Dell Remote Access Controller: ");

  $drac = array();
  $drac['front']['desc'] = "Chassis Front Panel Temperature";
  $drac['front']['oid'] = ".1.3.6.1.4.1.674.10892.2.3.1.10.0";

  $drac['cmcambient']['desc'] = "CMC Ambient Temperature";
  $drac['cmcambient']['oid'] = ".1.3.6.1.4.1.674.10892.2.3.1.11.0";

  $drac['cmccpu']['desc'] = "CMC Processor Temperature";
  $drac['cmccpu']['oid'] = ".1.3.6.1.4.1.674.10892.2.3.1.12.0";

  foreach ($drac as $index => $dsens) {
    $temp  = snmp_get($device, $dsens['oid'], "-Oqv");
  
    if ($dsens['desc'] != "" && is_numeric($temp) && $temp > "0")
    {
      discover_sensor($valid['sensor'], 'temperature', $device, $dsens['oid'], $index, 'dell-rac', $dsens['desc'], '1', '1', NULL, NULL, NULL, NULL, $temp);
    }
  }
}

?>
