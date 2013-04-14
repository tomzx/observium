<?php

// Devices
foreach (dbFetchRows("SELECT * FROM `devices` ORDER BY `hostname`") as $device)
{
  if (device_permitted($device))
  {

    // Process device and add all the human-readable stuff.
    humanize_device($device);

    $cache['devices']['hostname'][$device['hostname']] = $device['device_id'];
    $cache['devices']['id'][$device['device_id']] = $device;

    if ($device['disabled'] == 1 && !$config['web_show_disabled']) { continue; }

    $devices['count']++;

    if ($device['status'] == 0)   { $devices['down']++; }
    if ($device['status'] == 1)   { $devices['up']++; }

    $cache['devices']['timers']['polling'] += $device['last_polled_timetaken'];
    $cache['devices']['timers']['discovery'] += $device['last_discovered_timetaken'];

    $cache['device_types'][$device['type']]++;
  }
}

// Ports
foreach (dbFetchRows("SELECT port_id, ifAdminStatus, ifOperStatus FROM `ports`") as $port)
{
  if (port_permitted($port))
  {
    if ($port['ifAdminStatus'] == "down")
    {
      $ports['disabled']++;
    } else {
      if ($port['ifOperStatus'] == "up") { $ports['up']++; }
      if ($port['ifOperStatus'] == "down" || $port['ifOperStatus'] == "lowerLayerDown") { $ports['down']++; }
    }
  }
}

// Routing
// BGP
if (isset($config['enable_bgp']) && $config['enable_bgp'])
{
  $routing['bgp']['last_seen'] = $config['time']['now'];
  foreach (dbFetchRows('SELECT `device_id`,`bgpPeerState`,`bgpPeerAdminStatus`,`bgpPeerRemoteAs` FROM bgpPeers') as $bgp)
  {
    if (!$config['web_show_disabled'])
    {
      if ($cache['devices']['id'][$bgp['device_id']]['disabled']) { continue; }
    }
    if (device_permitted($bgp))
    {
      $routing['bgp']['all']++;
      if ($bgp['bgpPeerAdminStatus'] == 'start' || $bgp['bgpPeerAdminStatus'] == 'running')
      {
        $routing['bgp']['up']++;
        if ($bgp['bgpPeerState'] != 'established') { $routing['bgp']['alerted']++; }
      } else {
        $routing['bgp']['down']++;
      }
      if ($cache['devices']['id'][$bgp['device_id']]['bgpLocalAs'] == $bgp['bgpPeerRemoteAs'])
      {
        $routing['bgp']['internal']++;
      } else {
        $routing['bgp']['external']++;
      }
    }
  }
}

$routing['ospf']['all'] = dbFetchCell("SELECT COUNT(ospf_instance_id) FROM `ospf_instances` WHERE `ospfAdminStat` = 'enabled'");
$routing['cef']['all']  = dbFetchCell("SELECT COUNT(cef_switching_id) from `cef_switching`");
$routing['vrf']['all']  = dbFetchCell("SELECT COUNT(vrf_id) from `vrfs`");
$routing['cef']['all']  = dbFetchCell("SELECT COUNT(cef_switching_id) from `cef_switching`");

if (isset($config['enable_bgp']) && $config['enable_bgp'])
{
  $routing['bgp']['alerts'] = dbFetchCell("SELECT COUNT(bgpPeer_id) FROM bgpPeers AS B where (bgpPeerAdminStatus = 'start' OR bgpPeerAdminStatus = 'running') AND bgpPeerState != 'established'");
  $bgp_alerts = $routing['bgp']['alerts'];
}


$routing_count['bgp']  = $routing['bgp']['all'];
$routing_count['ospf'] = $routing['ospf']['all'];
$routing_count['cef']  = $routing['cef']['all'];
$routing_count['vrf']  = $routing['vrf']['all'];


?>
