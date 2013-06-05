<?php

echo("Q-BRIDGE-MIB VLANs : ");

$vlanversion = snmp_get($device, "dot1qVlanVersionNumber.0", "-Oqv", "Q-BRIDGE-MIB");

if ($vlanversion == 'version1')
{
  echo("VLAN $vlanversion ");

  $vtpdomain_id = "1";
  $vlans = snmpwalk_cache_oid($device, "dot1qVlanStaticEntry", array(), "Q-BRIDGE-MIB");

  foreach ($vlans as $vlan_id => $vlan)
  {
    if($device['os'] == 'ftos')
    {
      $vlan_id = rewrite_ftos_vlanid($device, $vlan_id);
    }
    unset ($vlan_update);

    if (is_array($vlans_db[$vtpdomain_id][$vlan_id]) && $vlans_db[$vtpdomain_id][$vlan_id]['vlan_name'] != $vlan['dot1qVlanStaticName'])
    {
      $vlan_update['vlan_name'] = $vlan['dot1qVlanStaticName'];
    }

    if (is_array($vlans_db[$vtpdomain_id][$vlan_id]) && $vlans_db[$vtpdomain_id][$vlan_id]['vlan_status'] != $vlan['dot1qVlanStaticRowStatus'])
    {
      $vlan_update['vlan_status'] = $vlan['dot1qVlanStaticRowStatus'];
    }

    echo(" $vlan_id");
    if (is_array($vlan_update))
    {
      dbUpdate($vlan_update, 'vlans', 'vlan_id = ?', array($vlans_db[$vtpdomain_id][$vlan_id]['vlan_id']));
      echo("U");
    } elseif (is_array($vlans_db[$vtpdomain_id][$vlan_id]))
    {
      echo(".");
    } else {
      dbInsert(array('device_id' => $device['device_id'], 'vlan_domain' => $vtpdomain_id, 'vlan_vlan' => $vlan_id, 'vlan_name' => $vlan['dot1qVlanStaticName'], 'vlan_type' => array('NULL')), 'vlans');
      echo("+");
    }
    $device['vlans'][$vtpdomain_id][$vlan_id] = $vlan_id;
  }

}

echo("\n");

?>
