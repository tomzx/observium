<?php

// JUNIPER-DOM-MIB

if ($device['os'] == "junos" || $device['os_group'] == "junose")
{
  echo("JUNIPER-DOM-MIB ");
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentTxLaserBiasCurrent",                    array(), "JUNIPER-DOM-MIB", mib_dirs('junos'));
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentTxLaserBiasCurrentHighAlarmThreshold",    $oids, "JUNIPER-DOM-MIB", mib_dirs('junos'));
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentTxLaserBiasCurrentLowAlarmThreshold",     $oids, "JUNIPER-DOM-MIB", mib_dirs('junos'));
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentTxLaserBiasCurrentHighWarningThreshold",  $oids, "JUNIPER-DOM-MIB", mib_dirs('junos'));
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentTxLaserBiasCurrentLowWarningThreshold",   $oids, "JUNIPER-DOM-MIB", mib_dirs('junos'));

  if (is_array($oids))
  {
    foreach ($oids as $index => $entry)
    {
      $entry['descr']   = snmp_get($device, "ifDescr.".$index,"-Oqv") . " tx bias current";
      $entry['oid']     = ".1.3.6.1.4.1.2636.3.60.1.1.1.1.6.".$index;
      $entry['current'] = $entry['jnxDomCurrentTxLaserBiasCurrent'];
      $entry['low']     = $entry['jnxDomCurrentTxLaserBiasCurrentLowAlarmThreshold']/1000000;
      $entry['loww']    = $entry['jnxDomCurrentTxLaserBiasCurrentLowWarningThreshold']/1000000;
      $entry['high']    = $entry['jnxDomCurrentTxLaserBiasCurrentHighAlarmThreshold']/1000000;
      $entry['highw']   = $entry['jnxDomCurrentTxLaserBiasCurrentHighWarningThreshold']/1000000;
      $entry['port']    = get_port_by_index_cache($device['device_id'], $index);

      if (is_array($entry['port'])) { $entry['e_t'] = 'port'; $entry['e_e'] = $entry['port']['port_id']; }
      discover_sensor($valid['sensor'], 'current', $device, $entry['oid'], $index, 'juniper-dom', $entry['descr'], '1000000', '1', $entry['low'], $entry['loww'], $entry['high'], $entry['highw'], $entry['current'],'snmp',NULL,NULL,$entry['e_t'], $entry['e_e']);
    }
  }

# jnxDomCurrentModuleTemperature[508] 35
# jnxDomCurrentModuleTemperatureHighAlarmThreshold[508] 100
# jnxDomCurrentModuleTemperatureLowAlarmThreshold[508] -25
# jnxDomCurrentModuleTemperatureHighWarningThreshold[508] 95
# jnxDomCurrentModuleTemperatureLowWarningThreshold[508] -20

  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentModuleTemperature", array(), "JUNIPER-DOM-MIB", mib_dirs('junos'));
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentModuleTemperatureHighAlarmThreshold", $oids, "JUNIPER-DOM-MIB", mib_dirs('junos'));
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentModuleTemperatureLowAlarmThreshold", $oids, "JUNIPER-DOM-MIB", mib_dirs('junos'));
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentModuleTemperatureHighWarningThreshold", $oids, "JUNIPER-DOM-MIB", mib_dirs('junos'));
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentModuleTemperatureLowWarningThreshold", $oids, "JUNIPER-DOM-MIB", mib_dirs('junos'));

  if (is_array($oids))
  {
    foreach ($oids as $index => $entry)
    {
      $entry['descr']   = snmp_get($device, "ifDescr.".$index,"-Oqv") . " DOM";
      $entry['oid']     = ".1.3.6.1.4.1.2636.3.60.1.1.1.1.8.".$index;
      $entry['current'] = $entry['jnxDomCurrentModuleTemperature'];
      $entry['low']     = $entry['jnxDomCurrentModuleTemperatureLowAlarmThreshold'];
      $entry['loww']    = $entry['jnxDomCurrentModuleTemperatureLowWarningThreshold'];
      $entry['high']    = $entry['jnxDomCurrentModuleTemperatureHighAlarmThreshold'];
      $entry['highw']   = $entry['jnxDomCurrentModuleTemperatureHighWarningThreshold'];
      $entry['port']    = get_port_by_index_cache($device['device_id'], $index);

      if (is_array($entry['port'])) { $entry['e_t'] = 'port'; $entry['e_e'] = $entry['port']['port_id']; }
      discover_sensor($valid['sensor'], 'temperature', $device, $entry['oid'], $index, 'juniper-dom', $entry['descr'], '1', '1', $entry['low'], $entry['loww'], $entry['high'], $entry['highw'], $entry['current'],'snmp',NULL,NULL,$entry['e_t'], $entry['e_e']);
    }
  }

# jnxDomCurrentRxLaserPower[508] -507 0.01 dbm

  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentRxLaserPower",                  array(), "JUNIPER-DOM-MIB", mib_dirs('junos'));
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentRxLaserPowerHighAlarmThreshold",  $oids, "JUNIPER-DOM-MIB", mib_dirs('junos'));
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentRxLaserPowerLowAlarmThreshold",   $oids, "JUNIPER-DOM-MIB", mib_dirs('junos'));
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentRxLaserPowerHighWarningThreshold",$oids, "JUNIPER-DOM-MIB", mib_dirs('junos'));
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentRxLaserPowerLowWarningThreshold", $oids, "JUNIPER-DOM-MIB", mib_dirs('junos'));

  if (is_array($oids))
  {
    foreach ($oids as $index => $entry)
    {
      $entry['descr'] = snmp_get($device, "ifDescr.".$index,"-Oqv") . " rx power";
      $entry['oid'] = ".1.3.6.1.4.1.2636.3.60.1.1.1.1.5.".$index;
      $entry['current']   = $entry['jnxDomCurrentRxLaserPower']/100;
      $entry['low']       = $entry['jnxDomCurrentRxLaserPowerLowAlarmThreshold']/100;
      $entry['loww']  = $entry['jnxDomCurrentRxLaserPowerLowWarningThreshold']/100;
      $entry['high']      = $entry['jnxDomCurrentRxLaserPowerHighAlarmThreshold']/100;
      $entry['highw'] = $entry['jnxDomCurrentRxLaserPowerHighWarningThreshold']/100;
      $entry['port']  = get_port_by_index_cache($device['device_id'], $index);
      if (is_array($entry['port'])) { $entry['e_t'] = 'port'; $entry['e_e'] = $entry['port']['port_id']; }
      discover_sensor($valid['sensor'], 'dbm', $device, $entry['oid'], $index, 'juniper-dom-rx', $entry['descr'], '100', '1', $entry['low'], $entry['loww'], $entry['high'], $entry['highw'], $entry['current'],'snmp',NULL,NULL,$entry['e_t'], $entry['e_e']);
    }
  }

# jnxDomCurrentTxLaserOutputPower[508] -507 0.01 dbm

  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentTxLaserOutputPower",                  array(), "JUNIPER-DOM-MIB", mib_dirs('junos'));
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentTxLaserOutputPowerHighAlarmThreshold",  $oids, "JUNIPER-DOM-MIB", mib_dirs('junos'));
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentTxLaserOutputPowerLowAlarmThreshold",   $oids, "JUNIPER-DOM-MIB", mib_dirs('junos'));
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentTxLaserOutputPowerHighWarningThreshold",$oids, "JUNIPER-DOM-MIB", mib_dirs('junos'));
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentTxLaserOutputPowerLowWarningThreshold", $oids, "JUNIPER-DOM-MIB", mib_dirs('junos'));

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

// EOF
