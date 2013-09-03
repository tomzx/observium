<?php

if ($device['os'] == "wlc")
{
  echo(" AIRESPACE-WIRELESS-MIB ");

  $temp = snmpwalk_cache_multi_oid($device, "bsnSensorTemperature", array(),"AIRESPACE-WIRELESS-MIB");
  $low = snmpwalk_cache_multi_oid($device, "bsnTemperatureAlarmLowLimit", array(),"AIRESPACE-WIRELESS-MIB");
  $high = snmpwalk_cache_multi_oid($device, "bsnTemperatureAlarmHighLimit", array(),"AIRESPACE-WIRELESS-MIB");

  if (is_array($temp))
  {
    $cur_oid = '.1.3.6.1.4.1.14179.2.3.1.13.';
    foreach ($temp as $index => $entry)
    {
      $descr = "Unit Temperature ". $index;
      echo " $descr, ";
      discover_sensor($valid['sensor'], 'temperature', $device, $cur_oid.$index, $index, 'wlc', $descr, "1", '1', NULL, $low[$index]['bsnTemperatureAlarmLowLimit'], $high[$index]['bsnTemperatureAlarmHighLimit'], NULL, $temp[$index]['bsnSensorTemperature'], "snmp",$index);
    }
  }
}

echo("\n");

