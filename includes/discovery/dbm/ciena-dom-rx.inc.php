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

// Ciena DOM Rx

# WWP-LEOS-PORT-XCVR-MIB::wwpLeosPortXcvrRxDbmPower.11 = INTEGER: -10679 dBm

if ($device['os'] == "ciena" || $device['os_group'] == "ciena")
{
  echo("Ciena DOM (RX) ");

  echo("WWP-LEOS-PORT-XCVR-MIB (RX) ");
  $oids = snmpwalk_cache_oid($device, "wwpLeosPortXcvrRxDbmPower", array(), "WWP-LEOS-PORT-XCVR-MIB", mib_dirs(array('wwp', 'ciena')) );
  $oids = snmpwalk_cache_oid($device, "wwpLeosPortXcvrHighRxDbmPwAlarmThreshold", $oids, "WWP-LEOS-PORT-XCVR-MIB", mib_dirs(array('wwp', 'ciena')) );
  $oids = snmpwalk_cache_oid($device, "wwpLeosPortXcvrLowRxPwAlarmThreshold", $oids, "WWP-LEOS-PORT-XCVR-MIB", mib_dirs(array('wwp', 'ciena')) );
  $oids = snmpwalk_cache_oid($device, "wwpLeosPortXcvrHighRxPwAlarmThreshold", $oids, "WWP-LEOS-PORT-XCVR-MIB", mib_dirs(array('wwp', 'ciena')) );
  $oids = snmpwalk_cache_oid($device, "wwpLeosPortXcvrHighRxDbmPwAlarmThreshold", $oids, "WWP-LEOS-PORT-XCVR-MIB", mib_dirs(array('wwp', 'ciena')) );

  if (is_array($oids))
  {
	  foreach ($oids as $index => $entry)
	  {
          $entry['descr']   = dbFetchCell("SELECT ifDescr FROM `ports` WHERE `device_id` = ? AND `ifName` = ?", array($device['device_id'], $index)) . " Rx power";
      $entry['oid']	= ".1.3.6.1.4.1.6141.2.60.4.1.1.1.1.105." . $index;
      $entry['current']	= $entry['wwpLeosPortXcvrRxDbmPower']/100;
      $entry['low']	= $entry['wwpLeosPortXcvrLowRxPwAlarmThreshold']/100;
      $entry['loww']	= $entry['wwpLeosPortXcvrLowRxDbmPwAlarmThreshold']/100;
      $entry['high']	= $entry['wwpLeosPortXcvrHighRxPwAlarmThreshold']/100;
      $entry['highw']	= $entry['wwpLeosPortXcvrHighRxDbmPwAlarmThreshold']/100;

     discover_sensor($valid['sensor'], 'dbm', $device, $entry['oid'], $index, 'ciena-dom-rx', $entry['descr'], '10000', '1', $entry['low'], $entry['loww'], $entry['high'], $entry['highw'], $entry['current'],'snmp',NULL,NULL);

    }
  }
}

// EOF
