<?php

// FOUNDRY-SN-SWITCH-GROUP-MIB


if ($device['os'] == "ironware" || $device['os_group'] == "ironware")
{
  echo("FOUNDRY-SN-SWITCH-GROUP-MIB ");
  $oids = snmpwalk_cache_oid($device, "snIfOpticalMonitoringTxPower", $oids, "FOUNDRY-SN-SWITCH-GROUP-MIB", $config['mib_dir'] );

  if (is_array($oids))
  {
    foreach ($oids as $index => $entry)
    {
      $entry['descr'] = snmp_get($device, "ifDescr.".$index,"-Oqv") . " DOM TX Power";
      $entry['oid']   = ".1.3.6.1.4.1.1991.1.1.3.3.6.1.2.".$index;
      $entry['dbm']   =  $entry['snIfOpticalMonitoringTxPower'];
      $entry['port']  = get_port_by_index_cache($device['device_id'], $index);
      if(is_array($entry['port'])) { $entry['e_t'] = 'port'; $entry['e_e'] = $entry['port']['port_id']; }
      if (!preg_match("|N/A|",$entry['dbm'])) {
        discover_sensor($valid['sensor'], 'dbm', $device, $entry['oid'], $index, 'brocade-dom-tx', $entry['descr'], '1', '1', NULL, NULL, NULL, NULL, NULL,'snmp',NULL,NULL,$entry['e_t'], $entry['e_e']);
      }
    }
  }

  $oids = snmpwalk_cache_oid($device, "snIfOpticalMonitoringRxPower", $oids, "FOUNDRY-SN-SWITCH-GROUP-MIB", $config['mib_dir'] );
  if (is_array($oids))
  {
    foreach ($oids as $index => $entry)
    {
      $entry['descr'] = snmp_get($device, "ifDescr.".$index,"-Oqv") . " DOM RX Power";
      $entry['oid'] = ".1.3.6.1.4.1.1991.1.1.3.3.6.1.3.".$index;
      $entry['dbm'] = $entry['snIfOpticalMonitoringRxPower'];
      $entry['port']        = get_port_by_index_cache($device['device_id'], $index);
      
      if(is_array($entry['port'])) { $entry['e_t'] = 'port'; $entry['e_e'] = $entry['port']['port_id']; }
      if (!preg_match("|N/A|",$entry['dbm'])) {
        discover_sensor($valid['sensor'], 'dbm', $device, $entry['oid'], $index, 'brocade-dom-rx', $entry['descr'], '1', '1', NULL, NULL, NULL, NULL, NULL,'snmp',NULL,NULL,$entry['e_t'], $entry['e_e']);
      }
    }
  }
}

?>

