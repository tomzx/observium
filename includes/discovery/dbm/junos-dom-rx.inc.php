<?php

// JUNIPER-DOM-MIB

# jnxDomCurrentRxLaserPower[508] -507 0.01 dbm

if ($device['os'] == "junos" || $device['os_group'] == "junose")
{
  echo("JUNIPER-DOM-MIB (RX) ");
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentRxLaserPower", array(), "JUNIPER-DOM-MIB", $config['mib_dir'].":".$config['mib_dir']."/junos" );
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentRxLaserPowerHighAlarmThreshold", $oids, "JUNIPER-DOM-MIB", $config['mib_dir'].":".$config['mib_dir']."/junos" );
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentRxLaserPowerLowAlarmThreshold", $oids, "JUNIPER-DOM-MIB", $config['mib_dir'].":".$config['mib_dir']."/junos" );
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentRxLaserPowerHighWarningThreshold", $oids, "JUNIPER-DOM-MIB", $config['mib_dir'].":".$config['mib_dir']."/junos" );
  $oids = snmpwalk_cache_oid($device, "jnxDomCurrentRxLaserPowerLowWarningThreshold", $oids, "JUNIPER-DOM-MIB", $config['mib_dir'].":".$config['mib_dir']."/junos" );

  if (is_array($oids))
  {
    foreach ($oids as $index => $entry)
    {
      $entry['descr'] = snmp_get($device, "ifDescr.".$index,"-Oqv") . " rx power";
      $entry['oid'] = ".1.3.6.1.4.1.2636.3.60.1.1.1.1.5.".$index;
      $entry['current']   = $entry['jnxDomCurrentRxLaserPower'];
      $entry['low']       = $entry['jnxDomCurrentRxLaserPowerLowAlarmThreshold']/100;
      $entry['loww']  = $entry['jnxDomCurrentRxLaserPowerLowWarningThreshold']/100;
      $entry['high']      = $entry['jnxDomCurrentRxLaserPowerHighAlarmThreshold']/100;
      $entry['highw'] = $entry['jnxDomCurrentRxLaserPowerHighWarningThreshold']/100;
      discover_sensor($valid['sensor'], 'dbm', $device, $entry['oid'], $index, 'juniper-dom-rx', $entry['descr'], '100', '1', $entry['low'], $entry['loww'], $entry['high'], $entry['highw'], $entry['current']);
    }
  }
}

?>

