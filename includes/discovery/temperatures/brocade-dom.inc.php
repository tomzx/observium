<?php

// FOUNDRY-SN-SWITCH-GROUP-MIB

if ($device['os'] == "ironware" || $device['os_group'] == "ironware")
{
  echo("FOUNDRY-SN-SWITCH-GROUP-MIB ");
  $oids = snmpwalk_cache_oid($device, "snIfOpticalMonitoringTemperature", array(), "FOUNDRY-SN-SWITCH-GROUP-MIB", mib_dirs('foundry'));

  if (is_array($oids))
  {
    foreach ($oids as $index => $entry)
    {
      $entry['descr'] = snmp_get($device, "ifDescr.".$index,"-Oqv") . " DOM Temperature";
      $entry['oid'] = ".1.3.6.1.4.1.1991.1.1.3.3.6.1.1.".$index;
      $entry['temperature'] = $entry['snIfOpticalMonitoringTemperature'];
      $entry['port']        = get_port_by_index_cache($device['device_id'], $index);
      if (is_array($entry['port'])) { $entry['e_t'] = 'port'; $entry['e_e'] = $entry['port']['port_id']; }
      if (!preg_match("|N/A|",$entry['temperature'])) {
        discover_sensor($valid['sensor'], 'temperature', $device, $entry['oid'], $index, 'brocade-dom', $entry['descr'], '1', '1', NULL, NULL, NULL, NULL, NULL,'snmp',NULL,NULL,$entry['e_t'], $entry['e_e']);
      }
    }
  }
}

?>

