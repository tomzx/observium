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


// WWP-LEOS-PORT-XCVR-MIB::

if ($device['os'] == "ciena" || $device['os_group'] == "ciena")
{
  echo("wwpLeosPortXcvrBias ");
  $oids = snmpwalk_cache_oid($device, "wwpLeosPortXcvrBias", array(), "WWP-LEOS-PORT-XCVR-MIB", mib_dirs(array('wwp', 'ciena')) );
  $oids = snmpwalk_cache_oid($device, "wwpLeosPortXcvrHighBiasAlarmThreshold", $oids, "WWP-LEOS-PORT-XCVR-MIB", mib_dirs(array('wwp', 'ciena')) );
  $oids = snmpwalk_cache_oid($device, "wwpLeosPortXcvrLowBiasAlarmThreshold", $oids, "WWP-LEOS-PORT-XCVR-MIB",  mib_dirs(array('wwp', 'ciena')) );

  if (is_array($oids))
  {
    foreach ($oids as $index => $entry)
    {
	          $entry['descr']   = dbFetchCell("SELECT ifDescr FROM `ports` WHERE `device_id` = ? AND `ifName` = ?", array($device['device_id'], $index)) . " Bias mA";
      $entry['oid'] = "1.3.6.1.4.1.6141.2.60.4.1.1.1.1.18.".$index;
      $entry['current']   = $entry['wwpLeosPortXcvrBias'];
      $entry['low']       = $entry['wwpLeosPortXcvrLowBiasAlarmThreshold'];
      $entry['loww']  = $entry['wwpLeosPortXcvrLowBiasAlarmThreshold'];
      $entry['high']      = $entry['wwpLeosPortXcvrHighBiasAlarmThreshold'];
      $entry['highw'] = $entry['wwpLeosPortXcvrHighBiasAlarmThreshold'];
      discover_sensor($valid['sensor'], 'current', $device, $entry['oid'], $index, 'ciena-dom', $entry['descr'], '1', '1', $entry['low'], NULL, $entry['high'], NULL, $entry['current'],'snmp',NULL,NULL);
    }
  }
}

// EOF
