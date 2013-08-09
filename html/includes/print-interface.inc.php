<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 *   This file prints a table row for each interface
 *   Various port properties are processed by humanize_port(), generating class and description.
 *
 * @package    observium
 * @subpackage webinterface
 * @author     Adam Armstrong <adama@memetic.org>
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */

$port['device_id'] = $device['device_id'];
$port['hostname'] = $device['hostname'];

// Process port properties and generate printable values
if(!isset($port['humanized'])) { humanize_port($port); }

$port_adsl = dbFetchRow("SELECT * FROM `ports_adsl` WHERE `port_id` = ?", array($port['port_id']));

if ($port['ifInErrors_delta'] > 0 || $port['ifOutErrors_delta'] > 0)
{
  $port['tags'] .= generate_port_link($port, '<span class="label label-important">Errors</span>', 'port_errors');
}

if($port['deleted'] == '1')
{
  $port['tags'] .= '<a href="'.generate_url(array('page' => 'deleted-ports')).'"><span class="label label-important">Deleted</span></a>';
}

if (dbFetchCell("SELECT COUNT(*) FROM `ports_cbqos` WHERE `port_id` = ?", array($port['port_id'])))
{
  $port['tags'] .= '<a href="' . generate_port_url($port, array('view' => 'cbqos')) . '"><span class="label label-info">CBQoS</span></a>';
}

if (dbFetchCell("SELECT COUNT(*) FROM `mac_accounting` WHERE `port_id` = ?", array($port['port_id'])))
{
  $port['tags'] .= '<a href="' . generate_port_url($port, array('view' => 'macaccounting')) . '"><span class="label label-info">MAC</span></a>';
}

echo('<tr class="'.$port['row_class'].'" valign=top onclick="location.href=\'" . generate_port_url($port) . "/\'" style="cursor: pointer;">
         <td style="width: 1px; background-color: '.$port['table_tab_colour'].'; margin: 0px; padding: 0px; width: 10px;"></td>
         <td style="width: 1px;"></td>
         <td valign="top" width="350">');

echo("        <span class='entity-title'>
              " . generate_port_link($port, $port['ifIndex_FIXME'] . "".$port['label']) . " ".$port['tags']."
           </span><br /><span class=small>".$port['ifAlias']."</span>");

if ($port['ifAlias']) { echo("<br />"); }

unset ($break);

if ($port_details)
{
  foreach (dbFetchRows("SELECT * FROM `ipv4_addresses` WHERE `port_id` = ?", array($port['port_id'])) as $ip)
  {
    echo($break ."<a class=small href=\"javascript:popUp('/netcmd.php?cmd=whois&amp;query=".$ip['ipv4_address']."')\">".$ip['ipv4_address']."/".$ip['ipv4_prefixlen']."</a>");
    $break = "<br />";
  }
  foreach (dbFetchRows("SELECT * FROM `ipv6_addresses` WHERE `port_id` = ?", array($port['port_id'])) as $ip6)
  {
    echo($break ."<a class=small href=\"javascript:popUp('/netcmd.php?cmd=whois&amp;query=".$ip6['ipv6_address']."')\">".Net_IPv6::compress($ip6['ipv6_address'])."/".$ip6['ipv6_prefixlen']."</a>");
    $break = "<br />";
  }
}

echo("</span>");

echo("</td><td width=147>");

if ($port_details)
{
  $port['graph_type'] = "port_bits";
  echo(generate_port_link($port, "<img src='graph.php?type=port_bits&amp;id=".$port['port_id']."&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=100&amp;height=20&amp;legend=no'>"));
  $port['graph_type'] = "port_upkts";
  echo(generate_port_link($port, "<img src='graph.php?type=port_upkts&amp;id=".$port['port_id']."&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=100&amp;height=20&amp;legend=no'>"));
  $port['graph_type'] = "port_errors";
  echo(generate_port_link($port, "<img src='graph.php?type=port_errors&amp;id=".$port['port_id']."&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=100&amp;height=20&amp;legend=no'>"));
}

echo('</td><td width=120 class="small" nowrap>');

if ($port['ifOperStatus'] == "up")
{
  $port['in_rate'] = $port['ifInOctets_rate'] * 8;
  $port['out_rate'] = $port['ifOutOctets_rate'] * 8;
  $in_icon  = 'icon-circle-arrow-left';
  $in_style = ($port['in_rate'] == 0) ? '' : 'style="color: ' . percent_colour(round($port['in_rate']/$port['ifSpeed']*100)) . '"';
  $out_icon  = 'icon-circle-arrow-right';
  $out_style = ($port['out_rate'] == 0) ? '' : 'style="color: ' . percent_colour(round($port['out_rate']/$port['ifSpeed']*100)) . '"';
  $pkts_in_icon  = 'icon-circle-arrow-left';
  $pkts_in_style = ($port['ifInUcastPkts_rate'] == 0) ? '' : 'style="color: #6B5DBB"';
  $pkts_out_icon  = 'icon-circle-arrow-right';
  $pkts_out_style = ($port['ifOutUcastPkts_rate'] == 0) ? '' : 'style="color: #AC5C35"';

  echo("<i class='$in_icon' $in_style></i>  <span $in_style>" . formatRates($port['in_rate'])  . "</span><br />
        <i class='$out_icon' $out_style></i> <span $out_style>". formatRates($port['out_rate']) . "</span><br />
        <i class='$pkts_in_icon' $pkts_in_style></i>  <span $pkts_in_style>" . format_bi($port['ifInUcastPkts_rate']) ."pps</span><br />
        <i class='$pkts_out_icon' $pkts_out_style></i> <span $pkts_out_style>". format_bi($port['ifOutUcastPkts_rate'])."pps</span>");
}

echo("</td><td width=75>");
if ($port['ifSpeed']) { echo("<span class=small>".humanspeed($port['ifSpeed'])."</span>"); }
echo("<br />");

if ($port['ifDuplex'] != "unknown") { echo("<span class=small>" . $port['ifDuplex'] . "</span>"); } else { echo("-"); }

if ($device['os'] == "ios" || $device['os'] == "iosxe")
{
  if ($port['ifTrunk']) {
    if ($port['ifVlan']) {
      // Native VLAN
      $native_state = dbFetchCell('SELECT `state` FROM `ports_vlans` WHERE `port_id` = ? AND `device_id` = ?', array($port['port_id'], $device['device_id']));
      $native_name = dbFetchCell('SELECT `vlan_name` FROM vlans WHERE `device_id` = ? AND `vlan_vlan` = ?;', array($device['device_id'], $port['ifVlan']));
      switch ($vlan_state)
      {
        case 'blocking':   $class = 'text-error'; break;
        case 'forwarding': $class = 'text-success';  break;
        default:           $class = 'muted';
      }
      if (empty($native_name)) {$native_name = 'VLAN'.str_pad($port['ifVlan'], 4, '0', STR_PAD_LEFT); }
      $native_tooltip = 'NATIVE: <strong class='.$class.'>'.$port['ifVlan'].' ['.$native_name.']</strong><br />';
    }

    $vlans = dbFetchRows('SELECT * FROM `ports_vlans` AS PV
                         LEFT JOIN vlans AS V ON PV.`vlan` = V.`vlan_vlan` AND PV.`device_id` = V.`device_id`
                         WHERE PV.`port_id` = ? AND PV.`device_id` = ? ORDER BY PV.`vlan`;', array($port['port_id'], $device['device_id']));
    $vlans_count = count($vlans);
    $rel = ($vlans_count || $native_tooltip) ? 'tooltip' : ''; // Hide tooltip for empty
    echo('<p class="small"><a rel="'.$rel.'" data-tooltip="<div class=\'small\' style=\'max-width: 320px; text-align: justify;\'>'.$native_tooltip);
    if ($vlans_count)
    {
      echo('ALLOWED: ');
      $vlan_prev = 0;
      foreach ($vlans as $vlan)
      {
        if ($vlans_count > 20)
        {
          // Aggregate VLANs
          $last_char = $vlans_aggr[strlen($vlans_aggr)-1];
          if ($vlan_prev == 0)
          {
            $vlans_aggr = '<strong>'.$vlan['vlan'];
          } elseif (is_numeric($last_char))
          {
            $vlans_aggr .= ($vlan['vlan']-1 == $vlan_prev) ? '-' : ', '.$vlan['vlan'];
          } elseif ($last_char == '-')
          {
            if ($vlan['vlan']-1 == $vlan_prev)
            {
              $vlan_prev = $vlan['vlan'];
              continue;
            } else {
              $vlans_aggr .= $vlan_prev.', '.$vlan['vlan'];
            }
          } else {
            $vlans_aggr .= $vlan['vlan'];
          }
          $vlan_prev = $vlan['vlan'];
        } else {
          // List VLANs
          switch ($vlan['state'])
          {
            case 'blocking':   $class = 'text-error'; break;
            case 'forwarding': $class = 'text-success';  break;
            default:           $class = 'muted';
          }
          if (empty($vlan['vlan_name'])) { 'VLAN'.str_pad($vlan['vlan'], 4, '0', STR_PAD_LEFT); }
          echo("<strong class=".$class.">".$vlan['vlan'] ." [".$vlan['vlan_name']."]</strong><br />");
        }
      }
      if ($vlan_prev)
      {
        // End aggregate VLANs
        $last_char = $vlans_aggr[strlen($vlans_aggr)-1];
        if ($last_char == '-')
        {
          $vlans_aggr = substr($vlans_aggr, 0, -1);
        } elseif ($last_char == ' ') {
          $vlans_aggr = substr($vlans_aggr, 0, -2);
        }
        echo($vlans_aggr.'</strong>');
      }
    }
    echo('</div>">'.$port['ifTrunk'].'</a></p>');
  } elseif ($port['ifVlan']) {
    $vlan_state = dbFetchCell('SELECT `state` FROM `ports_vlans` WHERE `port_id` = ? AND `device_id` = ?', array($port['port_id'], $device['device_id']));
    $vlan_name = dbFetchCell('SELECT `vlan_name` FROM vlans WHERE `device_id` = ? AND `vlan_vlan` = ?;', array($device['device_id'], $port['ifVlan']));
    switch ($vlan_state)
    {
      case 'blocking':   $class = 'text-error'; break;
      case 'forwarding': $class = 'text-success';  break;
      default:           $class = 'muted';
    }
    $rel = ($vlan_name) ? 'tooltip' : ''; // Hide tooltip for empty
    echo('<p rel="'.$rel.'" class="small '.$class.'"  data-tooltip="<strong class=\'small '.$class.'\'>'.$port['ifVlan'].' ['.$vlan_name.']</strong>">VLAN ' . $port['ifVlan'] . '</p>');
  } elseif ($port['ifVrf']) {
    $vrf = dbFetchRow("SELECT * FROM vrfs WHERE vrf_id = ?", array($port['ifVrf']));
    echo('<p class="small text-warning" rel="tooltip" data-tooltip="VRF">' . $vrf['vrf_name'] . "</p>");
  }
}

if ($port_adsl['adslLineCoding'])
{
  echo("</td><td width=150>");
  echo($port_adsl['adslLineCoding']."/" . rewrite_adslLineType($port_adsl['adslLineType']));
  echo("<br />");
  echo("Sync:".formatRates($port_adsl['adslAtucChanCurrTxRate']) . "/". formatRates($port_adsl['adslAturChanCurrTxRate']));
  echo("<br />");
  echo("Max:".formatRates($port_adsl['adslAtucCurrAttainableRate']) . "/". formatRates($port_adsl['adslAturCurrAttainableRate']));
  echo("</td><td width=150>");
  echo("Atten:".$port_adsl['adslAtucCurrAtn'] . "dB/". $port_adsl['adslAturCurrAtn'] . "dB");
  echo("<br />");
  echo("SNR:".$port_adsl['adslAtucCurrSnrMgn'] . "dB/". $port_adsl['adslAturCurrSnrMgn']. "dB");
} else {
  echo("</td><td width=150>");
  if ($port['ifType'] && $port['ifType'] != "") { echo("<span class=small>" . fixiftype($port['ifType']) . "</span>"); } else { echo("-"); }
  echo("<br />");
  if ($ifHardType && $ifHardType != "") { echo("<span class=small>" . $ifHardType . "</span>"); } else { echo("-"); }
  echo("</td><td width=150>");
  if ($port['ifPhysAddress'] && $port['ifPhysAddress'] != "") { echo("<span class=small>" . formatMac($port['ifPhysAddress']) . "</span>"); } else { echo("-"); }
  echo("<br />");
  if ($port['ifMtu'] && $port['ifMtu'] != "") { echo("<span class=small>MTU " . $port['ifMtu'] . "</span>"); } else { echo("-"); }
}

echo("</td>");
echo("<td width=375 valign=top class=small>");
if (strpos($port['label'], "oopback") === false && !$graph_type)
{
  foreach (dbFetchRows("SELECT * FROM `links` AS L, `ports` AS I, `devices` AS D WHERE L.local_port_id = ? AND L.remote_port_id = I.port_id AND I.device_id = D.device_id", array($port['port_id'])) as $link)
  {
#         echo("<img src='images/16/connect.png' align=absmiddle alt='Directly Connected' /> " . generate_port_link($link, makeshortif($link['label'])) . " on " . generate_device_link($link, shorthost($link['hostname'])) . "</a><br />");
#         $br = "<br />";
     $int_links[$link['port_id']] = $link['port_id'];
     $int_links_phys[$link['port_id']] = 1;
  }

  unset($br);

  if ($port_details)
  { // Show which other devices are on the same subnet as this interface
    foreach (dbFetchRows("SELECT `ipv4_network_id` FROM `ipv4_addresses` WHERE `port_id` = ? AND `ipv4_address` NOT LIKE '127.%'", array($port['port_id'])) as $net)
    {
      $ipv4_network_id = $net['ipv4_network_id'];
      $sql = "SELECT I.port_id FROM ipv4_addresses AS A, ports AS I, devices AS D
              WHERE A.port_id = I.port_id
              AND A.ipv4_network_id = ? AND D.device_id = I.device_id
              AND I.`ifAdminStatus` = 'up'
              AND D.device_id != ?";
      if (!$config['web_show_disabled']) { $sql .= ' AND D.disabled = 0'; }
      $array = array($net['ipv4_network_id'], $device['device_id']);
      foreach (dbFetchRows($sql, $array) AS $new)
      {
        echo($new['ipv4_network_id']);
        $this_ifid = $new['port_id'];
        $this_hostid = $new['device_id'];
        $this_hostname = $new['hostname'];
        $this_ifname = fixifName($new['label']);
        $int_links[$this_ifid] = $this_ifid;
        $int_links_v4[$this_ifid] = 1;
      }
    }

    foreach (dbFetchRows("SELECT ipv6_network_id FROM ipv6_addresses WHERE port_id = ?", array($port['port_id'])) as $net)
    {
      $ipv6_network_id = $net['ipv6_network_id'];
      $sql = "SELECT I.port_id FROM ipv6_addresses AS A, ports AS I, devices AS D
              WHERE A.port_id = I.port_id
              AND A.ipv6_network_id = ? AND D.device_id = I.device_id
              AND I.`ifAdminStatus` = 'up' AND A.ipv6_origin != 'linklayer' AND A.ipv6_origin != 'wellknown'
              AND D.device_id != ?";
      if (!$config['web_show_disabled']) { $sql .= ' AND D.disabled = 0'; }
      $array = array($net['ipv6_network_id'], $device['device_id']);

      foreach (dbFetchRows($sql, $array) AS $new)
      {
        echo($new['ipv6_network_id']);
          $this_ifid = $new['port_id'];
          $this_hostid = $new['device_id'];
          $this_hostname = $new['hostname'];
          $this_ifname = fixifName($new['label']);
          $int_links[$this_ifid] = $this_ifid;
          $int_links_v6[$this_ifid] = 1;
      }
    }
  }

  if ($port_details && $int_links)
  {
    foreach ($int_links as $int_link)
    {
      $link_if = dbFetchRow("SELECT * from ports AS I, devices AS D WHERE I.device_id = D.device_id and I.port_id = ?", array($int_link));

      echo("$br");

      if ($int_links_phys[$int_link]) { echo('<a alt="Directly connected" class="oicon-connect"><a> '); }
      else { echo('<a alt="Same subnet" class="oicon-arrow-transition"><a> '); }

      echo("<b>" . generate_port_link($link_if, makeshortif($link_if['label'])) . " on " . generate_device_link($link_if, shorthost($link_if['hostname'])));

      if ($int_links_v6[$int_link]) { echo(" <strong style='color: #a10000;'>IPv6</strong>"); }
      if ($int_links_v4[$int_link]) { echo(" <strong style='color: #00a100'>IPv4</strong>"); }
      $br = "<br />";
    }
  }
#     unset($int_links, $int_links_v6, $int_links_v4, $int_links_phys, $br);
}

if ($port_details)
{
  foreach (dbFetchRows("SELECT * FROM `pseudowires` WHERE `port_id` = ?", array($port['port_id'])) as $pseudowire)
  {
    //`port_id`,`peer_device_id`,`peer_ldp_id`,`cpwVcID`,`cpwOid`
    $pw_peer_dev = dbFetchRow("SELECT * FROM `devices` WHERE `device_id` = ?", array($pseudowire['peer_device_id']));
    $pw_peer_int = dbFetchRow("SELECT * FROM `ports` AS I, pseudowires AS P WHERE I.device_id = ? AND P.cpwVcID = ? AND P.port_id = I.port_id", array($pseudowire['peer_device_id'], $pseudowire['cpwVcID']));

    humanize_port($pw_peer_int);
    echo($br.'<i class="oicon-arrow-switch"></i> <strong>' . generate_port_link($pw_peer_int, makeshortif($pw_peer_int['label'])) .' on '. generate_device_link($pw_peer_dev, shorthost($pw_peer_dev['hostname'])) . '</strong>');
    $br = "<br />";
  }

  foreach (dbFetchRows("SELECT * FROM `ports` WHERE `pagpGroupIfIndex` = ? and `device_id` = ?", array($port['ifIndex'], $device['device_id'])) as $member)
  {
    $pagp[$device['device_id']][$port['ifIndex']][$member['ifIndex']] = TRUE;
    echo($br.'<i class="oicon-arrow-join"></i> <strong>' . generate_port_link($member) . ' [PAgP]</strong>');
    $br = "<br />";
  }

  if ($port['pagpGroupIfIndex'] && $port['pagpGroupIfIndex'] != $port['ifIndex'])
  {
    $pagp[$device['device_id']][$port['pagpGroupIfIndex']][$port['ifIndex']] = TRUE;
    $parent = dbFetchRow("SELECT * FROM `ports` WHERE `ifIndex` = ? and `device_id` = ?", array($port['pagpGroupIfIndex'], $device['device_id']));
    echo($br.'<i class="oicon-arrow-split"></i> <strong>' . generate_port_link($parent) . ' [PAgP]</strong>');
    $br = "<br />";
  }

  foreach (dbFetchRows("SELECT * FROM `ports_stack` WHERE `port_id_low` = ? and `device_id` = ?", array($port['ifIndex'], $device['device_id'])) as $higher_if)
  {
    if ($higher_if['port_id_high'])
    {
      if ($pagp[$device['device_id']][$higher_if['port_id_high']][$port['ifIndex']]) { continue; } // Skip if same PAgP port
      $this_port = get_port_by_index_cache($device['device_id'], $higher_if['port_id_high']);
      echo($br.'<i class="oicon-arrow-split"></i> <strong>' . generate_port_link($this_port) . '</strong>');
      $br = "<br />";
    }
  }

  foreach (dbFetchRows("SELECT * FROM `ports_stack` WHERE `port_id_high` = ? and `device_id` = ?", array($port['ifIndex'], $device['device_id'])) as $lower_if)
  {
    if ($lower_if['port_id_low'])
    {
      if ($pagp[$device['device_id']][$port['ifIndex']][$lower_if['port_id_low']]) { continue; } // Skip if same PAgP ports
      $this_port = get_port_by_index_cache($device['device_id'], $lower_if['port_id_low']);
      echo($br.'<i class="oicon-arrow-join"></i> <strong>' . generate_port_link($this_port) . "</strong>");
      $br = "<br />";
    }
  }
}

unset($int_links, $int_links_v6, $int_links_v4, $int_links_phys, $br);

echo("</td></tr>");

// If we're showing graphs, generate the graph and print the img tags

if ($graph_type == "etherlike")
{
  $graph_file = get_port_rrdfilename($device, $port, "dot3");
} else {
  $graph_file = get_port_rrdfilename($device, $port);
}

if ($graph_type && is_file($graph_file))
{
  $type = $graph_type;

  echo("<tr><td colspan=9>");

  $graph_array['to']     = $config['time']['now'];
  $graph_array['id']     = $port['port_id'];
  $graph_array['type']   = $graph_type;

  include("includes/print-graphrow.inc.php");

  echo("</td></tr>");
}

?>
