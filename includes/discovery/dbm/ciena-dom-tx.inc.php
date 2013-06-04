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


// Ciena DOM Tx

# WWP-LEOS-PORT-XCVR-MIB::wwpLeosPortXcvrTxDbmPower.11 = INTEGER: -10679 dBm

if ($device['os'] == "ciena" || $device['os_group'] == "ciena")
{
  echo("Ciena DOM (TX) ");

  echo("WWP-LEOS-PORT-XCVR-MIB (TX) ");
  $oids = snmpwalk_cache_oid($device, "wwpLeosPortXcvrTxDbmPower", array(), "WWP-LEOS-PORT-XCVR-MIB", mib_dirs(array('wwp', 'ciena')) );
  $oids = snmpwalk_cache_oid($device, "wwpLeosPortXcvrHighTxDbmPwAlarmThreshold", $oids, "WWP-LEOS-PORT-XCVR-MIB", mib_dirs(array('wwp', 'ciena')) );
  $oids = snmpwalk_cache_oid($device, "wwpLeosPortXcvrLowTxPwAlarmThreshold", $oids, "WWP-LEOS-PORT-XCVR-MIB", mib_dirs(array('wwp', 'ciena')) );
  $oids = snmpwalk_cache_oid($device, "wwpLeosPortXcvrHighTxPwAlarmThreshold", $oids, "WWP-LEOS-PORT-XCVR-MIB", mib_dirs(array('wwp', 'ciena')) );
  $oids = snmpwalk_cache_oid($device, "wwpLeosPortXcvrHighTxDbmPwAlarmThreshold", $oids, "WWP-LEOS-PORT-XCVR-MIB", mib_dirs(array('wwp', 'ciena')) );

  if (is_array($oids))
  {
    foreach ($oids as $index => $entry)
    {
      $entry['descr']   = dbFetchCell("SELECT ifDescr FROM `ports` WHERE `device_id` = ? AND `ifName` = ?", array($device['device_id'], $index)) . " Tx power";
      $entry['oid']	= ".1.3.6.1.4.1.6141.2.60.4.1.1.1.1.105." . $index;
      $entry['current']	= $entry['wwpLeosPortXcvrTxDbmPower']/100;
      $entry['low']	= $entry['wwpLeosPortXcvrLowTxPwAlarmThreshold']/100;
      $entry['loww']	= $entry['wwpLeosPortXcvrLowTxDbmPwAlarmThreshold']/100;
      $entry['high']	= $entry['wwpLeosPortXcvrHighTxPwAlarmThreshold']/100;
      $entry['highw']	= $entry['wwpLeosPortXcvrHighTxDbmPwAlarmThreshold']/100;

      discover_sensor($valid['sensor'], 'dbm', $device, $entry['oid'], $index, 'ciena-dom-tx', $entry['descr'], '10000', '1', $entry['low'], $entry['loww'], $entry['high'], $entry['highw'], $entry['current'],'snmp',NULL,NULL);
    }
  }
}

// EOF
