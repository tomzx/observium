<?php

if ($_SESSION['userlevel'] < '5')
{
  include("includes/error-no-perm.inc.php");
}
else
{
  $link_array = array('page' => 'routing', 'protocol' => 'bgp');

  print_optionbar_start('', '');

  echo('<span style="font-weight: bold;">BGP</span> &#187; ');

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

  if ($vars['adminstatus'] == "stop")
  {
    echo("<span class='pagemenu-selected'>");
    echo(generate_link("Shutdown",$vars, array('adminstatus' => NULL)));
    echo("</span>");
  } else {
    echo(generate_link("Shutdown",$vars, array('adminstatus' => 'stop')));
  }

  echo(" | ");

  if ($vars['adminstatus'] == "start")
  {
    echo("<span class='pagemenu-selected'>");
    echo(generate_link("Enabled",$vars, array('adminstatus' => NULL)));
    echo("</span>");
  } else {
    echo(generate_link("Enabled",$vars, array('adminstatus' => 'start')));
  }

  echo(" | ");

  if ($vars['state'] == "down")
  {
    echo("<span class='pagemenu-selected'>");
    echo(generate_link("Down",$vars, array('state' => NULL)));
    echo("</span>");
  } else {
    echo(generate_link("Down",$vars, array('state' => 'down')));
  }

  // End BGP Menu

  if (!isset($vars['view'])) { $vars['view'] = 'details'; }

  echo('<div style="float: right;">');

  if ($vars['view'] == "details") { echo("<span class='pagemenu-selected'>"); }
  echo(generate_link("No Graphs",$vars, array('view' => 'details', 'graph' => 'NULL')));
  if ($vars['view'] == "details") { echo("</span>"); }

  echo(" | ");

  if ($vars['graph'] == "updates") { echo("<span class='pagemenu-selected'>"); }
  echo(generate_link("Updates",$vars, array('view' => 'graphs', 'graph' => 'updates')));
  if ($vars['graph'] == "updates") { echo("</span>"); }

  echo(" | Prefixes: Unicast (");
  if ($vars['graph'] == "prefixes_ipv4unicast") { echo("<span class='pagemenu-selected'>"); }
  echo(generate_link("IPv4",$vars, array('view' => 'graphs', 'graph' => 'prefixes_ipv4unicast')));
  if ($vars['graph'] == "prefixes_ipv4unicast") { echo("</span>"); }

  echo("|");

  if ($vars['graph'] == "prefixes_ipv6unicast") { echo("<span class='pagemenu-selected'>"); }
  echo(generate_link("IPv6",$vars, array('view' => 'graphs', 'graph' => 'prefixes_ipv6unicast')));
  if ($vars['graph'] == "prefixes_ipv6unicast") { echo("</span>"); }

  echo("|");

  if ($vars['graph'] == "prefixes_ipv4vpn") { echo("<span class='pagemenu-selected'>"); }
  echo(generate_link("VPNv4",$vars, array('view' => 'graphs', 'graph' => 'prefixes_ipv4vpn')));
  if ($vars['graph'] == "prefixes_ipv4vpn") { echo("</span>"); }
  echo(")");

  echo(" | Multicast (");
  if ($vars['graph'] == "prefixes_ipv4multicast") { echo("<span class='pagemenu-selected'>"); }
  echo(generate_link("IPv4",$vars, array('view' => 'graphs', 'graph' => 'prefixes_ipv4multicast')));
  if ($vars['graph'] == "prefixes_ipv4multicast") { echo("</span>"); }

  echo("|");

  if ($vars['graph'] == "prefixes_ipv6multicast") { echo("<span class='pagemenu-selected'>"); }
  echo(generate_link("IPv6",$vars, array('view' => 'graphs', 'graph' => 'prefixes_ipv6multicast')));
  if ($vars['graph'] == "prefixes_ipv6multicast") { echo("</span>"); }
  echo(")");

  echo(" | MAC (");
  if ($vars['graph'] == "macaccounting_bits") { echo("<span class='pagemenu-selected'>"); }
  echo(generate_link("Bits",$vars, array('view' => 'graphs', 'graph' => 'macaccounting_bits')));
  if ($vars['graph'] == "macaccounting_bits") { echo("</span>"); }

  echo("|");

  if ($vars['graph'] == "macaccounting_pkts") { echo("<span class='pagemenu-selected'>"); }
  echo(generate_link("Packets",$vars, array('view' => 'graphs', 'graph' => 'macaccounting_pkts')));
  if ($vars['graph'] == "macaccounting_pkts") { echo("</span>"); }
  echo(")");

  echo('</div>');

  print_optionbar_end();

  switch ($vars['view'])
  {
    case 'prefixes_ipv4unicast':
    case 'prefixes_ipv4multicast':
    case 'prefixes_ipv4vpn':
    case 'prefixes_ipv6unicast':
    case 'prefixes_ipv6multicast':

    case 'macaccounting_bits':
    case 'macaccounting_pkts':
    case 'updates':
      $table_class = 'table-striped-two';
      break;
    default:
      $table_class = 'table-striped';
  }

  echo('<table class="table table-hover table-bordered '.$table_class.' table-condensed table-rounded">');
  echo('<thead>');

  echo('<tr><th></th><th></th><th>Local address</th><th></th><th>Peer address</th><th>Type</th><th>Family</th><th>Remote AS</th><th>State</th><th width=200>Uptime / Updates</th></tr>');
  echo('</thead>');

  if ($vars['type'] == "external")
  {
    $where = " AND D.bgpLocalAs != B.bgpPeerRemoteAs";
  } elseif ($vars['type'] == "internal") {
    $where = " AND D.bgpLocalAs = B.bgpPeerRemoteAs";
  }

  if ($vars['adminstatus'] == "stop")
  {
    $where .= " AND (B.bgpPeerAdminStatus = 'stop')";
  } elseif ($vars['adminstatus'] == "start")
  {
    $where .= " AND (B.bgpPeerAdminStatus = 'start' OR B.bgpPeerAdminStatus = 'running')";
  }

  if ($vars['state'] == "down")
  {
    $where .= " AND (B.bgpPeerState != 'established')";
  }

  if (!$config['web_show_disabled']) { $where .= ' AND D.disabled = 0 '; }

  $peer_query = 'SELECT * FROM `bgpPeers` AS B
                 LEFT JOIN `bgpPeers-state` AS S ON B.bgpPeer_id = S.bgpPeer_id
                 LEFT JOIN `devices` AS D ON B.device_id = D.device_id
                 WHERE 1 ' . $where .
                 ' ORDER BY D.hostname, B.bgpPeerRemoteAs, B.bgpPeerRemoteAddr';

  foreach (dbFetchRows($peer_query) as $peer)
  {

    humanize_bgp($peer);

    $ip_version = (strstr($peer['bgpPeerRemoteAddr'], ':')) ? 'ipv6' : 'ipv4';
    $peerhost = dbFetchRow('SELECT * FROM '.$ip_version.'_addresses AS A
                           LEFT JOIN ports AS I ON A.port_id = I.port_id
                           LEFT JOIN devices AS D ON I.device_id = D.device_id
                           WHERE A.'.$ip_version.'_address = ?', array($peer['bgpPeerRemoteAddr']));
    if ($peerhost) { $peername = generate_device_link($peerhost, shorthost($peerhost['hostname']), array('tab' => 'routing', 'proto' => 'bgp')); } else { unset($peername); }

    // display overlib graphs

    $graph_type       = "bgp_updates";
    $local_daily_url  = "graph.php?id=" . $peer['bgpPeer_id'] . "&amp;type=" . $graph_type . "&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=500&amp;height=150&amp;afi=ipv4&amp;safi=unicast";
    $local_ip = (strstr($peer['bgpPeerLocalAddr'], ':')) ? Net_IPv6::compress($peer['bgpPeerLocalAddr']) : $peer['bgpPeerLocalAddr'];
    $localaddresslink = "<span class=list-large><a href='device/device=" . $peer['device_id'] . "/tab=routing/proto=bgp/' onmouseover=\"return overlib('<img src=\'$local_daily_url\'>', LEFT".$config['overlib_defaults'].");\" onmouseout=\"return nd();\">" . $local_ip . "</a></span>";

    $graph_type       = "bgp_updates";
    $peer_daily_url   = "graph.php?id=" . $peer['bgpPeer_id'] . "&amp;type=" . $graph_type . "&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=500&amp;height=150";
    $peer_ip = (strstr($peer['bgpPeerRemoteAddr'], ':')) ? Net_IPv6::compress($peer['bgpPeerRemoteAddr']) : $peer['bgpPeerRemoteAddr'];
    $peeraddresslink  = "<span class=list-large><a href='device/device=" . $peer['device_id'] . "/tab=routing/proto=bgp/' onmouseover=\"return overlib('<img src=\'$peer_daily_url\'>', LEFT".$config['overlib_defaults'].");\" onmouseout=\"return nd();\">" . $peer_ip . "</a></span>";

    echo('<tr class="'.$peer['html_row_class'].'">');

    unset($sep);
    foreach (dbFetchRows("SELECT * FROM `bgpPeers_cbgp` WHERE `device_id` = ? AND bgpPeerRemoteAddr = ?", array($peer['device_id'], $peer['bgpPeerRemoteAddr'])) as $afisafi)
    {
      $afi = $afisafi['afi'];
      $safi = $afisafi['safi'];
      $this_afisafi = $afi.$safi;
      $peer['afi'] .= $sep . $afi .".".$safi;
      $sep = "<br />";
      $peer['afisafi'][$this_afisafi] = 1; // Build a list of valid AFI/SAFI for this peer
    }
    unset($sep);

    echo('
         <td style="width: 1px; background-color: '.$peer['table_tab_colour'].'; margin: 0px; padding: 0px"></td>
         <td style="width: 1px;"></td>');

    echo("
            <td width=150>" . $localaddresslink . "<br />".generate_device_link($peer, shorthost($peer['hostname']), array('tab' => 'routing', 'proto' => 'bgp'))."</td>
            <td width=30><b>&#187;</b></td>
            <td width=150>" . $peeraddresslink . "<br />" . $peername . "</td>
            <td width=50><b>".$peer['peer_type']."</b></td>
            <td width=50><small>".$peer['afi']."</small></td>
            <td><strong>AS" . $peer['bgpPeerRemoteAs'] . "</strong><br />" . $peer['astext'] . "</td>
            <td><strong><span class='".$peer['admin_class']."'>" . $peer['bgpPeerAdminStatus'] . "</span><br /><span class='".$peer['state_class']."'>" . $peer['bgpPeerState'] . "</span></strong></td>
            <td>" .formatUptime($peer['bgpPeerFsmEstablishedTime']). "<br />
                Updates <img src='images/16/arrow_down.png' align=absmiddle /> " . format_si($peer['bgpPeerInUpdates']) . "
                        <img src='images/16/arrow_up.png' align=absmiddle /> " . format_si($peer['bgpPeerOutUpdates']) . "</td></tr>");

    unset($invalid);
    switch ($vars['graph'])
    {
      case 'prefixes_ipv4unicast':
      case 'prefixes_ipv4multicast':
      case 'prefixes_ipv4vpn':
      case 'prefixes_ipv6unicast':
      case 'prefixes_ipv6multicast':
        list(,$afisafi) = explode("_", $vars['graph']);
        if (isset($peer['afisafi'][$afisafi])) { $peer['graph'] = 1; }
      case 'updates':
        $graph_array['type']   = "bgp_" . $vars['graph'];
        $graph_array['id']     = $peer['bgpPeer_id'];
    }

    switch ($vars['graph'])
    {
      case 'macaccounting_bits':
      case 'macaccounting_pkts':
        ///FIXME. This is worked? -- mike
        $acc = dbFetchRow("SELECT * FROM `mac_accounting` AS M
                          LEFT JOIN `ip_mac` AS I ON M.mac = I.mac_address
                          LEFT JOIN `ports` AS P ON P.port_id = M.port_id
                          LEFT JOIN `devices` AS D ON D.device_id = P.device_id
                          WHERE I.ip_address = ?", array($peer['bgpPeerRemoteAddr']));
        $database = $config['rrd_dir'] . "/" . $device['hostname'] . "/cip-" . $acc['ifIndex'] . "-" . $acc['mac'] . ".rrd";
        if (is_array($acc) && is_file($database))
        {
          $peer['graph']       = 1;
          $graph_array['id']   = $acc['ma_id'];
          $graph_array['type'] = $vars['graph'];
        }
    }

    if ($vars['graph'] == 'updates') { $peer['graph'] = 1; }

    if ($peer['graph'])
    {
        $graph_array['to']     = $config['time']['now'];
    echo('<tr class="'.$peer['html_row_class'].'">
         <td colspan="11">');

        include("includes/print-graphrow.inc.php");

        echo("</td></tr>");
    }
  }

  echo("</table>");
}

?>
