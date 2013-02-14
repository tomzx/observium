<?php

// FOUNDRY-SN-SWITCH-GROUP-MIB


if ($device['os'] == "ironware" || $device['os_group'] == "ironware")
{
  echo("FOUNDRY-SN-SWITCH-GROUP-MIB ");
  $oids = snmpwalk_cache_oid($device, "snIfOpticalMonitoringTxBiasCurrent", array(), "FOUNDRY-SN-SWITCH-GROUP-MIB", $config['mib_dir'] );

  if (is_array($oids))
  {
    foreach ($oids as $index => $entry)
    {
      $entry['descr'] = snmp_get($device, "ifDescr.".$index,"-Oqv") . " DOM TX Bias Current";
      $entry['oid'] = ".1.3.6.1.4.1.1991.1.1.3.3.6.1.4.".$index;
      $entry['current'] = $entry['snIfOpticalMonitoringTxBiasCurrent'];
      $entry['port']    = get_port_by_index_cache($device['device_id'], $index);

      if(is_array($entry['port'])) { $entry['e_t'] = 'port'; $entry['e_e'] = $entry['port']['port_id']; }
      if (!preg_match("|N/A|",$entry['current'])) {
        discover_sensor($valid['sensor'], 'current', $device, $entry['oid'], $index, 'brocade-dom', $entry['descr'], '1000', '1', NULL, NULL, NULL, NULL, NULL,'snmp',NULL,NULL,$entry['e_t'], $entry['e_e']);
      }
    }
  }
}

?>

