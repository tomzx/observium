<?php

// JUNIPER-DOM-MIB

if ($device['os'] == "junos" || $device['os_group'] == "junose")
{
  echo("JUNIPER-DOM-MIB ");
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentTxLaserBiasCurrent", array(), "JUNIPER-DOM-MIB", $config['mib_dir'].":".$config['mib_dir']."/junos" );
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentTxLaserBiasCurrentHighAlarmThreshold", $oids, "JUNIPER-DOM-MIB", $config['mib_dir'].":".$config['mib_dir']."/junos" );
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentTxLaserBiasCurrentLowAlarmThreshold", $oids, "JUNIPER-DOM-MIB", $config['mib_dir'].":".$config['mib_dir']."/junos" );
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentTxLaserBiasCurrentHighWarningThreshold", $oids, "JUNIPER-DOM-MIB", $config['mib_dir'].":".$config['mib_dir']."/junos" );
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentTxLaserBiasCurrentLowWarningThreshold", $oids, "JUNIPER-DOM-MIB", $config['mib_dir'].":".$config['mib_dir']."/junos" );

  if (is_array($oids))
  {
    foreach ($oids as $index => $entry)
    {
      $entry['descr'] = snmp_get($device, "ifDescr.".$index,"-Oqv") . " tx bias current";
      $entry['oid'] = ".1.3.6.1.4.1.2636.3.60.1.1.1.1.6.".$index;
      $entry['current']   = $entry['jnxDomCurrentTxLaserBiasCurrent'];
      $entry['low']       = $entry['jnxDomCurrentTxLaserBiasCurrentLowAlarmThreshold']/1000000;
      $entry['loww']  = $entry['jnxDomCurrentTxLaserBiasCurrentLowWarningThreshold']/1000000;
      $entry['high']      = $entry['jnxDomCurrentTxLaserBiasCurrentHighAlarmThreshold']/1000000;
      $entry['highw'] = $entry['jnxDomCurrentTxLaserBiasCurrentHighWarningThreshold']/1000000;
      $entry['port']  = get_port_by_index_cache($device['device_id'], $index);
      if (is_array($entry['port'])) { $entry['e_t'] = 'port'; $entry['e_e'] = $entry['port']['port_id']; }
      discover_sensor($valid['sensor'], 'current', $device, $entry['oid'], $index, 'juniper-dom', $entry['descr'], '1000000', '1', $entry['low'], $entry['loww'], $entry['high'], $entry['highw'], $entry['current'],'snmp',NULL,NULL,$entry['e_t'], $entry['e_e']);
    }
  }
}

?>

