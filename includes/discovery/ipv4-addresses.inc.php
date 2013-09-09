<?php

global $debug, $cache;

$device_id = $device['device_id'];
// Caching ifIndex
//FIXME. Need common caching
$query = 'SELECT `port_id`, `ifIndex` FROM `ports` WHERE `device_id` = ? GROUP BY `port_id`';
foreach(dbFetchRows($query, array($device_id)) as $entry)
{
  $entry_if = $entry['ifIndex'];
  if (is_numeric($entry['port_id'])) { $cache['port_index'][$device_id][$entry_if] = $entry['port_id']; }
}

echo("IPv4 Addresses : ");

$ip_version = 'ipv4';

// Get IP addresses from IP-MIB
$oids_ip = array('ipAdEntIfIndex', 'ipAdEntNetMask');
//ipAdEntIfIndex.10.0.0.130 = 193
//ipAdEntNetMask.10.0.0.130 = 255.255.255.252
$oid_data = array();
foreach ($oids_ip as $oid)
{
  $oid_data = snmpwalk_cache_oid($device, $oid, $oid_data, 'IP-MIB', mib_dirs());
}

// Rewrite IP-MIB array
$ip_data = array();
foreach ($oid_data as $ip_address => $entry)
{
  $ifIndex = $entry['ipAdEntIfIndex'];
  if (!is_numeric($entry['ipAdEntNetMask'])) { $entry['ipAdEntNetMask'] = '32'; }
  if (is_ipv4_valid($ip_address, $entry['ipAdEntNetMask']) === FALSE) { continue; }
  $ip_data[$ifIndex][$ip_address] = $entry;
}
if ($debug && $ip_data) { echo "IP-MIB\n"; print_vars($ip_data); }

// Caching old IPv4 addresses table
$query = 'SELECT * FROM `ipv4_addresses` AS A
          LEFT JOIN `ports` AS I ON A.`port_id` = I.`port_id`
          WHERE I.`device_id` = ?';
foreach(dbFetchRows($query, array($device_id)) as $entry)
{
  $old_table[$entry['ifIndex']][$entry['ipv4_address']] = $entry;
}

// Process founded IPv4 addresses
$valid[$ip_version] = array();
$check_networks = array();
if (count($ip_data))
{
  foreach ($ip_data as $ifIndex => $addresses)
  {
    if (!isset($cache['port_index'][$device_id][$ifIndex])) { continue; } // continue if ifIndex not found
    $port_id = $cache['port_index'][$device_id][$ifIndex];
    foreach ($addresses as $ipv4_address => $entry)
    {
      $update_array = array();
      $ipv4_mask = $entry['ipAdEntNetMask'];
      $addr = Net_IPv4::parseAddress($ipv4_address.'/'.$ipv4_mask);
      $ipv4_prefixlen = $addr->bitmask;
      $ipv4_network = $addr->network . '/' . $ipv4_prefixlen;
      $full_address = $ipv4_address . '/' . $ipv4_prefixlen;

      // First check networks
      $ipv4_network_id = dbFetchCell('SELECT `ipv4_network_id` FROM `ipv4_networks` WHERE `ipv4_network` = ?', array($ipv4_network));
      if (empty($ipv4_network_id))
      {
        $ipv4_network_id = dbInsert(array('ipv4_network' => $ipv4_network), 'ipv4_networks');
        echo('N');
      }
      // Check IPs in DB
      if (isset($old_table[$ifIndex][$ipv4_address]))
      {
        foreach(array('ipv4_prefixlen', 'ipv4_network_id', 'port_id') as $param)
        {
          if ($old_table[$ifIndex][$ipv4_address][$param] != $$param) { $update_array[$param] = $$param; }
        }
        if (count($update_array))
        {
          // Updated
          dbUpdate($update_array, 'ipv4_addresses', '`ipv4_address_id` = ?', array($old_table[$ifIndex][$ipv4_address]['ipv4_address_id']));
          if (isset($update_array['port_id']))
          {
            log_event("IPv4 removed: $ipv4_address/".$old_table[$ifIndex][$ipv4_address]['ipv4_prefixlen'], $device, 'interface', $old_table[$ifIndex][$ipv4_address]['port_id']);
            log_event("IPv4 added: $full_address", $device, 'interface', $port_id);
          } else {
            log_event("IPv4 changed: $ipv4_address/".$old_table[$ifIndex][$ipv4_address]['ipv4_prefixlen']." -> $full_address", $device, 'interface', $port_id);
          }
          echo('U');
          $check_networks[$ipv4_network_id] = 1;
        } else {
          // Not changed
          echo('.');
        }
      } else {
        // New IP
        foreach(array('ipv4_address', 'ipv4_prefixlen', 'ipv4_network_id', 'port_id') as $param)
        {
          $update_array[$param] = $$param;
        }
        dbInsert($update_array, 'ipv4_addresses');
        log_event("IPv4 added: $full_address", $device, 'interface', $port_id);
        echo('+');
      }
      $valid_address = $full_address . '-' . $port_id;
      $valid[$ip_version][$valid_address] = 1;
    }
  }
}

// Refetch and clean IP addresses from DB
foreach(dbFetchRows($query, array($device_id)) as $entry)
{
  $full_address = $entry['ipv4_address'] . '/' . $entry['ipv4_prefixlen'];
  $port_id = $entry['port_id'];
  $valid_address = $full_address  . '-' . $port_id;
  if (!isset($valid[$ip_version][$valid_address]))
  {
    // Delete IP
    dbDelete('ipv4_addresses', '`ipv4_address_id` = ?', array($entry['ipv4_address_id']));
    log_event("IPv4 removed: $full_address", $device, 'interface', $port_id);
    echo('-');
    $check_networks[$entry['ipv4_network_id']] = 1;
  }
}
// Clean networks
if (count($check_networks))
{
  foreach ($check_networks as $network_id => $n)
  {
    $count = dbFetchCell('SELECT COUNT(*) FROM `ipv4_addresses` WHERE `ipv4_network_id` = ?', array($network_id));
    if (empty($count))
    {
      dbDelete('ipv4_networks', '`ipv4_network_id` = ?', array($network_id));
      echo('n');
    }
  }
}

echo(PHP_EOL);

?>