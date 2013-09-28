<?php

if ($device['os_group'] == "cisco")
{
  // Deprecated CISCO-ENVMON-MIB

  echo(" CISCO-ENVMON-MIB ");

  // Temperatures:
  echo(" Temp ");

  $oids = array();
  echo(" ciscoEnvMonTemperatureStatusDescr");
  $oids = snmpwalk_cache_multi_oid($device, "ciscoEnvMonTemperatureStatusDescr", $oids, "CISCO-ENVMON-MIB");
  echo(" ciscoEnvMonTemperatureStatusValue");
  $oids = snmpwalk_cache_multi_oid($device, "ciscoEnvMonTemperatureStatusValue", $oids, "CISCO-ENVMON-MIB");
  echo(" ciscoEnvMonTemperatureThreshold");
  $oids = snmpwalk_cache_multi_oid($device, "ciscoEnvMonTemperatureThreshold", $oids, "CISCO-ENVMON-MIB");

  foreach ($oids as $index => $entry)
  {
    $oid  = '.1.3.6.1.4.1.9.9.13.1.3.1.3.'.$index;
    discover_sensor($valid['sensor'], 'temperature', $device, $oid, $index, 'cisco-envmon', $entry['ciscoEnvMonTemperatureStatusDescr'], '1', '1',
                    NULL, NULL, $entry['ciscoEnvMonTemperatureThreshold'], NULL, $entry['ciscoEnvMonTemperatureStatusValue']);
  }

  // Voltages
  echo(" Volts ");

  $oids = array();
  echo(" ciscoEnvMonVoltageStatusDescr");
  $oids = snmpwalk_cache_multi_oid($device, "ciscoEnvMonVoltageStatusDescr", $oids, "CISCO-ENVMON-MIB");
  echo(" ciscoEnvMonVoltageStatusValue");
  $oids = snmpwalk_cache_multi_oid($device, "ciscoEnvMonVoltageStatusValue", $oids, "CISCO-ENVMON-MIB");
  echo(" ciscoEnvMonVoltageThresholdLow");
  $oids = snmpwalk_cache_multi_oid($device, "ciscoEnvMonVoltageThresholdLow", $oids, "CISCO-ENVMON-MIB");
  echo(" ciscoEnvMonVoltageThresholdHigh");
  $oids = snmpwalk_cache_multi_oid($device, "ciscoEnvMonVoltageThresholdHigh", $oids, "CISCO-ENVMON-MIB");

  foreach ($oids as $index => $entry)
  {
    $oid  = '.1.3.6.1.4.1.9.9.13.1.2.1.3.'.$index;
    discover_sensor($valid['sensor'], 'voltage', $device, $oid, $index, 'cisco-envmon', $entry['ciscoEnvMonVoltageStatusDescr'], '1', '1',
                    $entry['ciscoEnvMonVoltageThresholdLow'], NULL, $entry['ciscoEnvMonVoltageThresholdHigh'], NULL, $entry['ciscoEnvMonVoltageStatusValue']);
  }
}

// EOF
