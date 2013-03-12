<?php

// JUNIPER-DOM-MIB

# jnxDomCurrentTxLaserOutputPower[508] -507 0.01 dbm

if ($device['os'] == "junos" || $device['os_group'] == "junose")
{
  echo("JUNIPER-DOM-MIB (TX) ");
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentTxLaserOutputPower", array(), "JUNIPER-DOM-MIB", $config['mib_dir'].":".$config['mib_dir']."/junos" );
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentTxLaserOutputPowerHighAlarmThreshold", $oids, "JUNIPER-DOM-MIB", $config['mib_dir'].":".$config['mib_dir']."/junos" );
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentTxLaserOutputPowerLowAlarmThreshold", $oids, "JUNIPER-DOM-MIB", $config['mib_dir'].":".$config['mib_dir']."/junos" );
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentTxLaserOutputPowerHighWarningThreshold", $oids, "JUNIPER-DOM-MIB", $config['mib_dir'].":".$config['mib_dir']."/junos" );
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentTxLaserOutputPowerLowWarningThreshold", $oids, "JUNIPER-DOM-MIB", $config['mib_dir'].":".$config['mib_dir']."/junos" );

  if (is_array($oids))
  {
    foreach ($oids as $index => $entry)
    {
      $entry['descr'] = snmp_get($device, "ifDescr.".$index,"-Oqv") . " tx output power";
      $entry['oid'] = ".1.3.6.1.4.1.2636.3.60.1.1.1.1.7.".$index;
      $entry['current']   = $entry['jnxDomCurrentTxLaserOutputPower']/100;
      $entry['low']       = $entry['jnxDomCurrentTxLaserOutputPowerLowAlarmThreshold']/100;
      $entry['loww']  = $entry['jnxDomCurrentTxLaserOutputPowerLowWarningThreshold']/100;
      $entry['high']      = $entry['jnxDomCurrentTxLaserOutputPowerHighAlarmThreshold']/100;
      $entry['highw'] = $entry['jnxDomCurrentTxLaserOutputPowerHighWarningThreshold']/100;
      $entry['port']  = get_port_by_index_cache($device['device_id'], $index);
      if (is_array($entry['port'])) { $entry['e_t'] = 'port'; $entry['e_e'] = $entry['port']['port_id']; }
      discover_sensor($valid['sensor'], 'dbm', $device, $entry['oid'], $index, 'juniper-dom-tx', $entry['descr'], '100', '1', $entry['low'], $entry['loww'], $entry['high'], $entry['highw'], $entry['current'],'snmp',NULL,NULL,$entry['e_t'], $entry['e_e']);
    }
  }
}

?>

