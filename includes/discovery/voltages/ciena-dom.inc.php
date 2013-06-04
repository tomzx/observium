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

# WWP-LEOS-PORT-XCVR-MIB::wwpLeosPortXcvrHighVccAlarmThreshold
# WWP-LEOS-PORT-XCVR-MIB::wwpLeosPortXcvrLowVccAlarmThreshold
# WWP-LEOS-PORT-XCVR-MIB::wwpLeosPortXcvrVcc


if ($device['os'] == "ciena" || $device['os_group'] == "ciena")
{
  echo("WWP-LEOS-PORT-XCVR-MIB ");
  $oids = snmpwalk_cache_oid($device, "wwpLeosPortXcvrVcc", array(), "WWP-LEOS-PORT-XCVR-MIB", mib_dirs(array('wwp', 'ciena')) );
  $oids = snmpwalk_cache_oid($device, "wwpLeosPortXcvrHighVccAlarmThreshold", $oids, "WWP-LEOS-PORT-XCVR-MIB", mib_dirs(array('wwp', 'ciena')) );
  $oids = snmpwalk_cache_oid($device, "wwpLeosPortXcvrLowVccAlarmThreshold", $oids, "WWP-LEOS-PORT-XCVR-MIB", mib_dirs(array('wwp', 'ciena')) );

  if (is_array($oids))
  {
    foreach ($oids as $index => $entry)
    {
      $entry['descr']   = dbFetchCell("SELECT ifDescr FROM `ports` WHERE `device_id` = ? AND `ifName` = ?", array($device['device_id'], $index)) . " Volts";
      $entry['oid'] 	  = ".1.3.6.1.4.1.6141.2.60.4.1.1.1.1.16.".$index;
      $entry['current']   = $entry['wwpLeosPortXcvrVcc'];
      $entry['low']       = $entry['wwpLeosPortXcvrLowVccAlarmThreshold'];
      $entry['high']      = $entry['wwpLeosPortXcvrHighVccAlarmThreshold'];

      discover_sensor($valid['sensor'], 'voltage', $device, $entry['oid'], $index, 'ciena-dom-volt', $entry['descr'], '1', '1', $entry['low'], NULL, $entry['high'], NULL, $entry['current'],'snmp',NULL,NULL);

    }
  }
}

// EOF
