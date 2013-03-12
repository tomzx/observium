<?php

// JUNIPER-DOM-MIB

# jnxDomCurrentModuleTemperature[508] 35
# jnxDomCurrentModuleTemperatureHighAlarmThreshold[508] 100
# jnxDomCurrentModuleTemperatureLowAlarmThreshold[508] -25
# jnxDomCurrentModuleTemperatureHighWarningThreshold[508] 95
# jnxDomCurrentModuleTemperatureLowWarningThreshold[508] -20

if ($device['os'] == "junos" || $device['os_group'] == "junose")
{
  echo("JUNIPER-DOM-MIB ");
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentModuleTemperature", array(), "JUNIPER-DOM-MIB", $config['mib_dir'].":".$config['mib_dir']."/junos" );
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentModuleTemperatureHighAlarmThreshold", $oids, "JUNIPER-DOM-MIB", $config['mib_dir'].":".$config['mib_dir']."/junos" );
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentModuleTemperatureLowAlarmThreshold", $oids, "JUNIPER-DOM-MIB", $config['mib_dir'].":".$config['mib_dir']."/junos" );
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentModuleTemperatureHighWarningThreshold", $oids, "JUNIPER-DOM-MIB", $config['mib_dir'].":".$config['mib_dir']."/junos" );
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentModuleTemperatureLowWarningThreshold", $oids, "JUNIPER-DOM-MIB", $config['mib_dir'].":".$config['mib_dir']."/junos" );

  if (is_array($oids))
  {
    foreach ($oids as $index => $entry)
    {
      $entry['descr'] = snmp_get($device, "ifDescr.".$index,"-Oqv") . " DOM";
      $entry['oid'] = ".1.3.6.1.4.1.2636.3.60.1.1.1.1.8.".$index;
      $entry['current']   = $entry['jnxDomCurrentModuleTemperature'];
      $entry['low']       = $entry['jnxDomCurrentModuleTemperatureLowAlarmThreshold'];
      $entry['loww']  = $entry['jnxDomCurrentModuleTemperatureLowWarningThreshold'];
      $entry['high']      = $entry['jnxDomCurrentModuleTemperatureHighAlarmThreshold'];
      $entry['highw'] = $entry['jnxDomCurrentModuleTemperatureHighWarningThreshold'];
      $entry['port']  = get_port_by_index_cache($device['device_id'], $index);
      if (is_array($entry['port'])) { $entry['e_t'] = 'port'; $entry['e_e'] = $entry['port']['port_id']; }
      discover_sensor($valid['sensor'], 'temperature', $device, $entry['oid'], $index, 'juniper-dom', $entry['descr'], '1', '1', $entry['low'], $entry['loww'], $entry['high'], $entry['highw'], $entry['current'],'snmp',NULL,NULL,$entry['e_t'], $entry['e_e']);

    }
  }
}

?>

