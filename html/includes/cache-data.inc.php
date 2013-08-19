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

    if ($device['disabled'] && !$config['web_show_disabled']) { continue; }

    $devices['count']++;

    if ($device['status']) { $devices['up']++; }
    else { $devices['down']++; }

    $cache['devices']['timers']['polling'] += $device['last_polled_timetaken'];
    $cache['devices']['timers']['discovery'] += $device['last_discovered_timetaken'];

    $cache['device_types'][$device['type']]++;
    $cache['device_locations'][$device['location']]++;

    $cache['locations']['entries'][$device['location_country']]['count']++;
    $cache['locations']['entries'][$device['location_country']]['level'] = 'location_country';

    $cache['locations']['entries'][$device['location_country']]['entries'][$device['location_state']]['count']++;
    $cache['locations']['entries'][$device['location_country']]['entries'][$device['location_state']]['level'] = 'location_state';

    $cache['locations']['entries'][$device['location_country']]['entries'][$device['location_state']]['entries'][$device['location_county']]['count']++;
    $cache['locations']['entries'][$device['location_country']]['entries'][$device['location_state']]['entries'][$device['location_county']]['level'] = 'location_county';

    $cache['locations']['entries'][$device['location_country']]['entries'][$device['location_state']]['entries'][$device['location_county']]['entries'][$device['location_city']]['count']++;
    $cache['locations']['entries'][$device['location_country']]['entries'][$device['location_state']]['entries'][$device['location_county']]['entries'][$device['location_city']]['level'] = 'location_city';

    $cache['locations']['entries'][$device['location_country']]['entries'][$device['location_state']]['entries'][$device['location_county']]['entries'][$device['location_city']]['entries'][$device['location']]['count']++;
    $cache['locations']['entries'][$device['location_country']]['entries'][$device['location_state']]['entries'][$device['location_county']]['entries'][$device['location_city']]['entries'][$device['location']]['level'] = 'location';

  }
}

#echo("<pre>");
#print_r($cache['locations']);
#echo("</pre>");

// Ports
$ports_array = dbFetchRows("SELECT device_id, ports.port_id, ifAdminStatus, ifOperStatus, `deleted`, `ignore`, `ifOutErrors_delta`, `ifInErrors_delta` FROM `ports` LEFT JOIN `ports-state` ON  `ports`.`port_id` =  `ports-state`.`port_id`");

port_permitted_array($ports_array);

foreach($ports_array as $port)
{
  if (!$config['web_show_disabled'])
  {
    if ($cache['devices']['id'][$port['device_id']]['disabled']) { continue; }
  }
  if (port_permitted($port))
  {
    $ports['count']++;
    if ($port['ifAdminStatus'] == "down")
    {
      $ports['disabled']++;
    } else {
      if ($port['ifOperStatus'] == "up") { $ports['up']++; }
      if ($port['ifOperStatus'] == "down" || $port['ifOperStatus'] == "lowerLayerDown") {
        $ports['down']++;
        if (!$port['ignore']) { $ports['alerts']++; }
      }
    }
    if ($port['ignore']) { $ports['ignored']++; }
    if ($port['deleted']) { $ports['deleted']++; }
    if ($port['ifOutErrors_delta'] > 0 || $port['ifInErrors_delta'] > 0 )
    {
       $ports['errored']++;
       $cache['ports_errored'][] = $port['port_id'];
    }
  }
}

unset($ports_array);

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
      $routing['bgp']['count']++;
      if ($bgp['bgpPeerAdminStatus'] == 'start' || $bgp['bgpPeerAdminStatus'] == 'running')
      {
        $routing['bgp']['up']++;
        if ($bgp['bgpPeerState'] != 'established')
        {
          $routing['bgp']['alerts']++;
        }
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

// OSPF
if (isset($config['enable_ospf']) && $config['enable_ospf'])
{
  $routing['ospf']['last_seen'] = $config['time']['now'];
  foreach (dbFetchRows('SELECT `device_id`,`ospfAdminStat` FROM `ospf_instances`') as $ospf)
  {
    if (!$config['web_show_disabled'])
    {
      if ($cache['devices']['id'][$ospf['device_id']]['disabled']) { continue; }
    }
    if (device_permitted($ospf))
    {
      $routing['ospf']['count']++;
      if ($ospf['ospfAdminStat'] == 'enabled')
      {
        $routing['ospf']['up']++;
      } else {
        $routing['ospf']['down']++;
      }
    }
  }
}

// CEF
$routing['cef']['count']  = dbFetchCell("SELECT COUNT(cef_switching_id) from `cef_switching`");
// VRF
$routing['vrf']['count']  = dbFetchCell("SELECT COUNT(vrf_id) from `vrfs`");

?>
