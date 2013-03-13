<?php

/// FIXME. Maybe it is necessary move to Poller? -- mike 2013/03

unset ($mac_table);

// IPv4 ARP table
echo("ARP Table : ");

$ipNetToMedia_data = snmp_walk($device, 'ipNetToMediaPhysAddress', '-Oq', 'IP-MIB');
$ipNetToMedia_data = str_replace("ipNetToMediaPhysAddress.", "", trim($ipNetToMedia_data));
$ipNetToMedia_data = str_replace("IP-MIB::", "", trim($ipNetToMedia_data));

// Caching old ARP table
/// FIXME. Need only 'mac_id', 'port_id', 'ifIndex', 'mac_address', 'ipv4_address'
$query = 'SELECT mac_id, M.port_id, mac_address, ipv4_address, ifIndex from ipv4_mac AS M
          LEFT JOIN ports as I ON M.port_id = I.port_id
          WHERE I.device_id = ?';
$cache_arp = dbFetchRows($query, array($device['device_id']));
foreach($cache_arp as $entry)
{
  $old_if = $entry['ifIndex'];
  $old_mac = $entry['mac_address'];
  $old_address = $entry['ipv4_address'];
  $old_table[$old_if][$old_address] = $old_mac;
  if(!isset($interface[$old_if])) { $interface[$old_if] = $entry['port_id']; }
}

foreach (explode("\n", $ipNetToMedia_data) as $data)
{
  list($oid, $mac) = explode(" ", $data);
  list($if, $first, $second, $third, $fourth) = explode(".", $oid);
  $ip = $first . "." . $second . "." . $third . "." . $fourth;
  if ($ip != '...')
  {
    list($m_a, $m_b, $m_c, $m_d, $m_e, $m_f) = explode(":", $mac);
    $m_a = zeropad($m_a);$m_b = zeropad($m_b);$m_c = zeropad($m_c);$m_d = zeropad($m_d);$m_e = zeropad($m_e);$m_f = zeropad($m_f);
    //$md_a = hexdec($m_a);$md_b = hexdec($m_b);$md_c = hexdec($m_c);$md_d = hexdec($m_d);$md_e = hexdec($m_e);$md_f = hexdec($m_f);
    $mac = "$m_a:$m_b:$m_c:$m_d:$m_e:$m_f";

    $clean_mac = $m_a . $m_b . $m_c . $m_d . $m_e . $m_f;
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
  $entry_port_id = $entry['port_id'];
  $entry_mac = $entry['mac_address'];
  $entry_ip = $entry['ipv4_address'];
  $entry_if  = $entry['ifIndex'];
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
