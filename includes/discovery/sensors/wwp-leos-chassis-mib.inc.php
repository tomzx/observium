<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    obserium
 * @subpackage discovery
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */

// WWP-LEOS-CHASSIS-MIB::wwpLeosChassisTempSensorTable

if ($device['os'] == 'ciena')
{
  echo(" WWP-LEOS-CHASSIS-MIB ");

  $current = snmp_get($device, ".1.3.6.1.4.1.6141.2.60.11.1.1.5.1.1.2.1", "-Oqv");
  $high = snmp_get($device, ".1.3.6.1.4.1.6141.2.60.11.1.1.5.1.1.3.1", "-Oqv");
  $low = snmp_get($device, ".1.3.6.1.4.1.6141.2.60.11.1.1.5.1.1.4.1", "-Oqv");
  $descr = "Chassis Temp";
  $oid = ".1.3.6.1.4.1.6141.2.60.11.1.1.5.1.1.2.1";
  
  # FIXME type should not have caps! (fixing this requires a database UPDATE query + RRD renames) -TL
  discover_sensor($valid['sensor'], 'temperature', $device, $oid, 1, 'Ciena', $descr, 1, 1, NULL, NULL, NULL, NULL, $current);
}

// EOF
