<?php

/* Detection for JDSU OEM Erbium Dotted Fibre Aplifiers */

if ($device['os'] == "jdsu_edfa")
{

  $divisor=1;
  
  $oid  = ".1.3.6.1.4.1.17409.1.3.1.13.0";
  $current = snmp_get($device, $oid, "-Oqv", "NSCRTV-ROOT") / $divisor;
  discover_sensor($valid['sensor'], 'temperature', $device, $oid, 0, 'jdsu-edfa-temp', 'Enviroment Temperature', $divisor, '1', 5, 10, 40, 50, $current);
}
