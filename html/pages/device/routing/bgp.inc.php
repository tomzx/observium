<div style='padding: 10px; height: 20px; clear: both; display: block;'>
  <div style='float: left; font-size: 22px; font-weight: bold;'>Local AS : <?php echo($device['bgpLocalAs']); ?></div>
</div>

<?php

/// FIXME - this whole page needs rewritte. Use view = graphs / graph = $graphtype.

$link_array = array('page'    => 'device',
                    'device'  => $device['device_id'],
                    'tab'     => 'routing',
                    'proto'   => 'bgp');

if(!isset($vars['view'])) { $vars['view'] = "basic"; }

print_optionbar_start();

echo("<span style='font-weight: bold;'>BGP</span> &#187; ");

  if (!$vars['type']) { $vars['type'] = "all"; }

  if ($vars['type'] == "all") { echo("<span class='pagemenu-selected'>"); }
  echo(generate_link("All",$vars, array('type' => 'all')));
  if ($vars['type'] == "all") { echo("</span>"); }

  echo(" | ");

  if ($vars['type'] == "internal") { echo("<span class='pagemenu-selected'>"); }
  echo(generate_link("iBGP",$vars, array('type' => 'internal')));
  if ($vars['type'] == "internal") { echo("</span>"); }

  echo(" | ");

  if ($vars['type'] == "external") { echo("<span class='pagemenu-selected'>"); }
  echo(generate_link("eBGP",$vars, array('type' => 'external')));
  if ($vars['type'] == "external") { echo("</span>"); }

  echo(" | ");


if ($vars['view'] == "basic") { echo("<span class='pagemenu-selected'>"); }
echo(generate_link("Basic", $link_array,array('view'=>'basic')));
if ($vars['view'] == "basic") { echo("</span>"); }

echo(" | ");

if ($vars['view'] == "updates") { echo("<span class='pagemenu-selected'>"); }
echo(generate_link("Updates", $link_array,array('view'=>'updates')));
if ($vars['view'] == "updates") { echo("</span>"); }

echo(" | Prefixes: ");

if ($vars['view'] == "prefixes_ipv4unicast") { echo("<span class='pagemenu-selected'>"); }
echo(generate_link("IPv4", $link_array,array('view'=>'prefixes_ipv4unicast')));
if ($vars['view'] == "prefixes_ipv4unicast") { echo("</span>"); }

echo(" | ");

if ($vars['view'] == "prefixes_vpnv4unicast") { echo("<span class='pagemenu-selected'>"); }
echo(generate_link("VPNv4", $link_array,array('view'=>'prefixes_vpnv4unicast')));
if ($vars['view'] == "prefixes_vpnv4unicast") { echo("</span>"); }

echo(" | ");

if ($vars['view'] == "prefixes_ipv6unicast") { echo("<span class='pagemenu-selected'>"); }
echo(generate_link("IPv6", $link_array,array('view'=>'prefixes_ipv6unicast')));
if ($vars['view'] == "prefixes_ipv6unicast") { echo("</span>"); }

echo(" | Traffic: ");

if ($vars['view'] == "macaccounting_bits") { echo("<span class='pagemenu-selected'>"); }
echo(generate_link("Bits", $link_array,array('view'=>'macaccounting_bits')));
if ($vars['view'] == "macaccounting_bits") { echo("</span>"); }
echo(" | ");
if ($vars['view'] == "macaccounting_pkts") { echo("<span class='pagemenu-selected'>"); }
echo(generate_link("Packets", $link_array,array('view'=>'macaccounting_pkts')));
if ($vars['view'] == "macaccounting_pkts") { echo("</span>"); }

print_optionbar_end();

  switch ($vars['view'])
  {
    case 'prefixes_ipv4unicast':
    case 'prefixes_ipv4multicast':
    case 'prefixes_ipv4vpn':
    case 'prefixes_ipv6unicast':
    case 'prefixes_ipv6multicast':
    case 'updates':
      $table_class = 'table-striped-two'; $graphs = 1;
      break;
    default:
      $table_class = 'table-striped';
  }

echo('<table class="table table-hover '.$table_class.' table-bordered table-condensed table-rounded">');
echo('<thead>');
echo('<tr><th></th><th></th><th>Peer address</th><th>Type</th><th>AFI.SAFI</th><th>Remote AS</th><th>State</th><th>Uptime</th></tr>');
echo('</thead>');

  if ($vars['type'] == "external")
  {
    $where = " AND bgpPeerRemoteAs != '".$device['bgpLocalAs']."'";
  } elseif ($vars['type'] == "internal") {
    $where = " AND bgpPeerRemoteAs = '".$device['bgpLocalAs']."'";
  }


$sql = 'SELECT * FROM `bgpPeers` AS B
        LEFT JOIN `bgpPeers-state` AS S ON B.bgpPeer_id = S.bgpPeer_id
        WHERE `device_id` = ? '.$where.'
        ORDER BY `bgpPeerRemoteAs`, `bgpPeerRemoteAddr`';

foreach (dbFetchRows($sql, array($device['device_id'])) as $peer)
{

  $peer['bgpLocalAs'] = $device['bgpLocalAs'];
  humanize_bgp($peer);

  $has_macaccounting = dbFetchCell("SELECT COUNT(*) FROM mac_accounting AS M
                                   LEFT JOIN `ip_mac` AS I ON M.mac = I.mac_address
                                   WHERE I.ip_address = ?", array($peer['bgpPeerRemoteAddr']));
  unset ($peerhost, $peername);

  $ip_version = (strstr($peer['bgpPeerRemoteAddr'], ':')) ? 'ipv6' : 'ipv4';
  $peerhost = dbFetchRow('SELECT * FROM '.$ip_version.'_addresses AS A
                         LEFT JOIN ports AS I ON A.port_id = I.port_id
                         LEFT JOIN devices AS D ON I.device_id = D.device_id
                         WHERE A.'.$ip_version.'_address = ?', array($peer['bgpPeerRemoteAddr']));
  if ($peerhost) { $peername = generate_device_link($peerhost, shorthost($peerhost['hostname']), array('tab' => 'routing', 'proto' => 'bgp')); } else { unset($peername); }

  unset($sep);
  foreach (dbFetchRows("SELECT * FROM `bgpPeers_cbgp` WHERE `device_id` = ? AND bgpPeerRemoteAddr = ?", array($device['device_id'], $peer['bgpPeerRemoteAddr'])) as $afisafi)
  {
    $afi = $afisafi['afi'];
    $safi = $afisafi['safi'];
    $this_afisafi = $afi.$safi;
    $peer['afi'] .= $sep . $afi .".".$safi;
    $sep = "<br />";
    $peer['afisafi'][$this_afisafi] = 1; // Build a list of valid AFI/SAFI for this peer
  }

  $graph_type       = "bgp_updates";
  $peer_daily_url   = "graph.php?id=" . $peer['bgpPeer_id'] . "&amp;type=" . $graph_type . "&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=500&amp;height=150";
  $peeraddresslink  = "<span class=entity-title><a onmouseover=\"return overlib('<img src=\'$peer_daily_url\'>', LEFT".$config['overlib_defaults'].");\" onmouseout=\"return nd();\">" . $peer['human_remoteip'] . "</a></span>";

  echo('<tr class="'.$peer['html_row_class'].'">');
  echo('
         <td style="width: 1px; background-color: '.$peer['table_tab_colour'].'; margin: 0px; padding: 0px"></td>
         <td style="width: 1px;"></td>');

  echo("   <td>" . $peeraddresslink . "<br />" . $peername . "</td>
           <td><strong>".$peer['peer_type']."</strong></td>
           <td style='font-size: 10px; font-weight: bold; line-height: 10px;'>" . (isset($peer['afi']) ? $peer['afi'] : '') . "</td>
           <td><strong>AS" . $peer['bgpPeerRemoteAs'] . "</strong><br />" . $peer['astext'] . "</td>
           <td><strong><span class='".$peer['admin_class']."'>" . $peer['bgpPeerAdminStatus'] . "<span><br /><span class='".$peer['state_class']."'>" . $peer['bgpPeerState'] . "</span></strong></td>
           <td>" .formatUptime($peer['bgpPeerFsmEstablishedTime']). "<br />
               Updates <img src='images/16/arrow_down.png' align=absmiddle> " . format_si($peer['bgpPeerInUpdates']) . "
                       <img src='images/16/arrow_up.png' align=absmiddle> " . format_si($peer['bgpPeerOutUpdates']) . "</td>
          </tr>");

  unset($invalid);

  switch ($vars['view'])
  {
    case 'prefixes_ipv4unicast':
    case 'prefixes_ipv4multicast':
    case 'prefixes_vpnv4unicast':
    case 'prefixes_ipv6unicast':
    case 'prefixes_ipv6multicast':
      list(,$afisafi) = explode("_", $vars['view']);
      if (isset($peer['afisafi'][$afisafi])) { $peer['graph'] = 1; }
      // FIXME no break??
    case 'updates':
      $graph_array['type']   = "bgp_" . $vars['view'];
      $graph_array['id']     = $peer['bgpPeer_id'];
  }

  switch ($vars['view'])
  {
    case 'macaccounting_bits':
    case 'macaccounting_pkts':
      $acc = dbFetchRow("SELECT * FROM `mac_accounting` AS M
                        LEFT JOIN `ip_mac`   AS I ON M.mac = I.mac_address
                        LEFT JOIN `ports`    AS P ON P.port_id = M.port_id
                        LEFT JOIN `devices`  AS D ON D.device_id = P.device_id
                        WHERE I.ip_address = ?", array($peer['bgpPeerRemoteAddr']));
      $database = $config['rrd_dir'] . "/" . $device['hostname'] . "/cip-" . $acc['ifIndex'] . "-" . $acc['mac'] . ".rrd";
      if (is_array($acc) && is_file($database))
      {
        $peer['graph']       = 1;
        $graph_array['id']   = $acc['ma_id'];
        $graph_array['type'] = $vars['view'];
      }
  }
  if ($vars['view'] == 'updates') { $peer['graph'] = 1; }

  if ($graphs == 1)
  {
    echo('<tr><td colspan="8">');
    if ($peer['graph'])
    {
      $graph_array['to']     = $config['time']['now'];

      include("includes/print-graphrow.inc.php");
    }
    echo("</td></tr>");
  }
  unset($valid_afi_safi);
}
?>

</table>
