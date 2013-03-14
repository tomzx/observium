<?php

/// FIXME. Maybe it is necessary move to Poller? -- mike 2013/03

unset ($mac_table);

// Caching ifIndex
$query = 'SELECT port_id, ifIndex FROM ports WHERE device_id = ? GROUP BY port_id';
foreach(dbFetchRows($query, array($device['device_id'])) as $entry)
{
  $entry_if = $entry['ifIndex'];
  $interface[$entry_if] = $entry['port_id'];
}

// IPv4 ARP table
echo("ARP Table : ");

// GENERIC (-OXqs):
//ipNetToMediaPhysAddress[213][10.0.0.162] 70:81:5:ec:f9:bf
$oid_data = snmp_walk($device, 'ipNetToMediaPhysAddress', '-OXqs', 'IP-MIB');
$oid_data = trim($oid_data);

// Caching old ARP table
$query = 'SELECT mac_id, mac_address, ipv4_address, ifIndex FROM ipv4_mac AS M
          LEFT JOIN ports AS I ON M.port_id = I.port_id
          WHERE I.device_id = ?';
$cache_arp = dbFetchRows($query, array($device['device_id']));
foreach($cache_arp as $entry)
{
  $old_if = $entry['ifIndex'];
  $old_mac = $entry['mac_address'];
  $old_address = $entry['ipv4_address'];
  $old_table[$old_if][$old_address] = $old_mac;
}

foreach (explode("\n", $oid_data) as $data)
{
  $ipv4_pattern = '/\[(\d+)\]\[([\d\.]+)\]\s+([[:xdigit:]]+):([[:xdigit:]]+):([[:xdigit:]]+):([[:xdigit:]]+):([[:xdigit:]]+):([[:xdigit:]]+)/i';
  preg_match($ipv4_pattern, $data, $matches);
  $if = $matches[1];
  $ip = $matches[2];
  if ($ip)
  {
    $mac = zeropad($matches[3]);
    for ($i = 4; $i <= 8; $i++) { $mac .= ':' . zeropad($matches[$i]); }
    $clean_mac = str_replace(':', '', $mac);

    $mac_table[$if][$ip] = $clean_mac;
    $port_id = $interface[$if];

    if (isset($old_table[$if][$ip]))
    {
      $old_mac = $old_table[$if][$ip];

      if ($clean_mac != $old_mac && $clean_mac != '' && $old_mac != '')
      {
        if ($debug) { echo("Changed MAC address for $ip from $old_mac to $clean_mac\n"); }
        log_event("MAC change: $ip : " . mac_clean_to_readable($old_mac) . " -> " . mac_clean_to_readable($clean_mac), $device, "interface", $port_id);
        dbUpdate(array('mac_address' => $clean_mac) , 'ipv4_mac', 'port_id = ? AND ipv4_address = ?', array($port_id, $ip));
        echo(".");
      }
    } else {
      $params = array(
                      'port_id' => $port_id,
                      'mac_address' => $clean_mac,
                      'ipv4_address' => $ip);
      dbInsert($params, 'ipv4_mac');
      if ($debug) { echo("Add MAC $clean_mac\n"); }
      //log_event("MAC add: $ip : " . mac_clean_to_readable($clean_mac), $device, "interface", $port_id);
      echo("+");
    }
  }
}

// Remove expired ARP entries
foreach($cache_arp as $entry)
{
  $entry_mac_id = $entry['mac_id'];
  $entry_mac = $entry['mac_address'];
  $entry_ip = $entry['ipv4_address'];
  $entry_if  = $entry['ifIndex'];
  $entry_port_id = $interface[$entry_if];
  if (!isset($mac_table[$entry_if][$entry_ip]))
  {
    dbDelete('ipv4_mac', 'mac_id = ?', array($entry_mac_id));
    if ($debug) { echo("Removing MAC address $entry_mac for $entry_ip\n"); }
    //log_event("MAC remove: $entry_ip : " . mac_clean_to_readable($entry_mac), $device, "interface", $entry['port_id']);
    echo("-");
  }
}

echo(PHP_EOL);
// End IPv4 ARP table

// IPv6 Neighbors table
//echo("IPv6 Neighbors Table : ");

//echo(PHP_EOL);
// End IPv6 Neighbors table

unset($mac, $interface);
?>
