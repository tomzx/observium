<?php

echo("Q-BRIDGE-MIB FDB Tables\n");

/// Build ifIndex > port_id cache table
$port_ifIndex_table = array();
foreach(dbFetchRows("SELECT `ifIndex`,`port_id` FROM `ports` WHERE `device_id` = ?", array($device['device_id'])) as $port)
  {  $port_ifIndex_table[$port['ifIndex']] = $port['port_id']; }

/// Build table of existing vlan/mac table
$fdbs_db = array();
$fdbs_q = dbFetchRows("SELECT * FROM `vlans_fdb` WHERE `device_id` = ?", array($device['device_id']));
foreach ($fdbs_q as $fdb_db) { $fdbs_db[$fdb_db['vlan_id']][$fdb_db['mac_address']] = $fdb_db; }

/// Fetch data and build array of data for each vlan&mac
$data = snmp_walk($device, 'dot1qTpFdbEntry', '-OqsX', 'Q-BRIDGE-MIB');
foreach(explode("\n", $data) as $text) {
  list($oid, $value) = explode(" ", $text);
  preg_match('/(\w+)\[(\d+)\]\[([a-f0-9:]+)\]/', $text, $oid);
  if(!empty($value)) {
    list($m_a, $m_b, $m_c, $m_d, $m_e, $m_f) = explode(":", $oid[3]);
    $m_a = zeropad($m_a);$m_b = zeropad($m_b);$m_c = zeropad($m_c);$m_d = zeropad($m_d);$m_e = zeropad($m_e);$m_f = zeropad($m_f);
    $md_a = hexdec($m_a);$md_b = hexdec($m_b);$md_c = hexdec($m_c);$md_d = hexdec($m_d);$md_e = hexdec($m_e);$md_f = hexdec($m_f);
#    $mac['readable'] = $m_a.":".$m_b.":".$m_c.":".$m_d.":".$m_e.":".$m_f;
#    $mac['cisco'] = $m_a.$m_b.".".$m_c.$m_d.".".$m_e.$m_f;
    $mac = $m_a . $m_b . $m_c . $m_d . $m_e . $m_f;
    $fdbs[$oid[2]][$mac][$oid[1]] = $value;
  }
}

echo(str_pad("Vlan", 8) . " | " . str_pad("MAC",12) . " | " .  str_pad("Port",12) ." | ". str_pad("Status",16) . "\n".
str_pad("", 90, "-")."\n");

/// Loop vlans
foreach($fdbs as $vlan => $macs)
{
  /// Loop macs
  foreach($macs as $mac => $data)
  {
    $ifIndex = $data['dot1qTpFdbPort'];
    $port_id = $port_ifIndex_table[$ifIndex];
    echo(str_pad($vlan, 8) . " | " . str_pad($mac,12) . " | " .  str_pad($port_id,12) ." | ". str_pad($data['dot1qTpFdbPort'],16));

    /// if entry already exists
    if(!is_array($fdbs_db[$vlan][$mac]))
    {
      dbInsert(array('device_id' => $device['device_id'], 'vlan_id' => $vlan, 'port_id' => $port_id, 'mac_address' => $mac, 'fdb_status' => $data['dot1qTpFdbStatus']), 'vlans_fdb');
      echo("+");
    } else {
      unset($update_array);
      /// if port/status are different, build an update array and update the db
      if($fdbs_db[$vlan][$mac]['port_id'] != $port_id)                    { $update_array['port_id'] = $port_id; }
      if($fdbs_db[$vlan][$mac]['fdb_status'] != $data['dot1qTpFdbStatus']) { $update_array['fdb_status'] = $data['fdb_status']; }
      if(is_array($update_array))
      {
        dbUpdate($update_array, 'vlans_fdb', '`device_id` = ? AND `vlan_id` = ? AND `mac_address` = ?', array($device['device_id'], $vlan, $mac));
        echo("U");
      } else {
      }
      /// remove it from the existing list
      unset ($fdbs_db[$vlan][$mac]);
    }
    echo("\n");
  }
}

/// Loop the existing list and delete anything remaining
foreach ($fdbs_db as $vlan => $fdb_macs)
{
  foreach($fdb_macs as $mac => $data)
  {
    echo(str_pad($vlan, 8) . " | " . str_pad($mac,12) . " | " .  str_pad($data['port_id'],12) ." | ". str_pad($data['fdb_status'],16));
    echo("-\n");
    dbDelete('vlans_fdb', '`device_id` = ? AND `vlan_id` = ? AND `mac_address` = ?', array($device['device_id'], $vlan, $mac));
  }
}

?>
