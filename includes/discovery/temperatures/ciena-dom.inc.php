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

// WWP-LEOS-PORT-XCVR-MIB

# WWP-LEOS-PORT-XCVR-MIB::wwpLeosPortXcvrTemperature (Transceiver temp)
# WWP-LEOS-PORT-XCVR-MIB::wwpLeosPortXcvrHighTempAlarmThreshold
# WWP-LEOS-PORT-XCVR-MIB::wwpLeosPortXcvrLowTempAlarmThreshold


if ($device['os'] == "ciena" || $device['os_group'] == "ciena")
{
  echo("WWP-LEOS-PORT-XCVR-MIB ");
  $oids = snmpwalk_cache_oid($device, "wwpLeosPortXcvrTemperature", array(), "WWP-LEOS-PORT-XCVR-MIB", mib_dirs(array('wwp', 'ciena')) );
  $oids = snmpwalk_cache_oid($device, "wwpLeosPortXcvrHighTempAlarmThreshold", $oids, "WWP-LEOS-PORT-XCVR-MIB", mib_dirs(array('wwp', 'ciena')) );
  $oids = snmpwalk_cache_oid($device, "wwpLeosPortXcvrLowTempAlarmThreshold", $oids, "WWP-LEOS-PORT-XCVR-MIB", mib_dirs(array('wwp', 'ciena')) );

  if (is_array($oids))
  {
    foreach ($oids as $index => $entry)
    {
      $entry['descr']   = dbFetchCell("SELECT ifDescr FROM `ports` WHERE `device_id` = ? AND `ifName` = ?", array($device['device_id'], $index)) . " DegC";
      $entry['oid']	  = ".1.3.6.1.4.1.6141.2.60.4.1.1.1.1.16.".$index;
      $entry['current']   = $entry['wwpLeosPortXcvrTemperature'];
      $entry['low']       = $entry['wwpLeosPortXcvrLowTempAlarmThreshold'];
      $entry['high']      = $entry['wwpLeosPortXcvrHighTempAlarmThreshold'];

      discover_sensor($valid['sensor'], 'temperature', $device, $entry['oid'], $index, 'ciena-dom-temp', $entry['descr'], '1', '1', $entry['low'], NULL, $entry['high'], NULL, $entry['current'],'snmp',NULL,NULL);
    }
  }
}

// EOF
