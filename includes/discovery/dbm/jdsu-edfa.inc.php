<?php

/* Detection for JDSU OEM Erbium Dotted Fibre Aplifiers */

if ($device['os'] == "jdsu_edfa")
{

  $divisor=10;
  
  $oid  = ".1.3.6.1.4.1.17409.1.11.2.0";
  $current = snmp_get($device, $oid, "-Oqv", "NSCRTV-ROOT") / $divisor;
  discover_sensor($valid['sensor'], 'dbm', $device, '.1.3.6.1.4.1.17409.1.11.2.0', 0, 'jdsu-edfa-tx', 'Optical Output Power', $divisor, '1', 9, 10, 15, 16, $current);
  
  $oid  = ".1.3.6.1.4.1.17409.1.11.3.0";
  $current = snmp_get($device, $oid, "-Oqv", "NSCRTV-ROOT") / $divisor;
  discover_sensor($valid['sensor'], 'dbm', $device, '.1.3.6.1.4.1.17409.1.11.3.0', 0, 'jdsu-edfa-rx', 'Optical Input Power', $divisor, '1', -18, -14, -10, -9, $current);

}