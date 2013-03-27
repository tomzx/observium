<?php

// FIXME - REWRITE!

$hostname = $device['hostname'];
$hostid   = $device['port_id'];
$ifname   = $port['ifDescr'];
$ifIndex   = $port['ifIndex'];
$speed = humanspeed($port['ifSpeed']);

$ifalias = $port['name'];

if ($port['ifPhysAddress']) { $mac = $port['ifPhysAddress']; }

$color = "black";
if ($port['ifAdminStatus'] == "down") { $status = "<span class='grey'>Disabled</span>"; }
if ($port['ifAdminStatus'] == "up" && $port['ifOperStatus'] == "down") { $status = "<span class='red'>Enabled / Disconnected</span>"; }
if ($port['ifAdminStatus'] == "up" && $port['ifOperStatus'] == "up") { $status = "<span class='green'>Enabled / Connected</span>"; }

$i = 1;
$inf = fixifName($ifname);

echo("<div style='clear: both;'>");

if ($vars['subview'] == "top10")
{

  if (!isset($vars['sort'])) { $vars['sort'] = "in"; }
  if (!isset($vars['period'])) { $vars['period'] = "day"; }
  $from = "-" . $vars['period'];
  $from = $config['time'][$vars['period']];

  echo("<div style='margin: 0px 0px 0px 0px'>
         <div style=' margin:0px; float: left;';>
           <div style='margin: 0px 10px 5px 0px; padding:5px; background: #e5e5e5;'>
           <span class=device-head>Day</span><br />

           <a href='".generate_url($link_array,array('view' => 'macaccounting', 'subview' => 'top10', 'graph'=>$vars['graph'], sort => $vars['sort'], 'period' => 'day'))."'>

             <img style='border: #5e5e5e 2px;' valign=middle src='graph.php?id=".$port['port_id'].
                    "&amp;stat=".$vars['graph']."&amp;type=port_mac_acc_total&amp;sort=".$vars['sort']."&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=150&amp;height=50' />
           </a>
           </div>
           <div style='margin: 0px 10px 5px 0px; padding:5px; background: #e5e5e5;'>
           <span class=device-head>Two Day</span><br />
           <a href='".generate_url($link_array,array('view' => 'macaccounting', 'subview' => 'top10', 'graph'=>$vars['graph'], sort => $vars['sort'], 'period' => 'twoday'))."/'>
             <img style='border: #5e5e5e 2px;' valign=middle src='graph.php?id=".$port['port_id'].
                    "&amp;stat=".$vars['graph']."&amp;type=port_mac_acc_total&amp;sort=".$vars['sort']."&amp;from=".$config['time']['twoday']."&amp;to=".$config['time']['now']."&amp;width=150&amp;height=50' />
           </a>
           </div>
           <div style='margin: 0px 10px 5px 0px; padding:5px; background: #e5e5e5;'>
           <span class=device-head>Week</span><br />
            <a href='".generate_url($link_array,array('view' => 'macaccounting', 'subview' => 'top10', 'graph'=>$vars['graph'], sort => $vars['sort'], 'period' => 'week'))."/'>
            <img style='border: #5e5e5e 2px;' valign=middle src='graph.php?id=".$port['port_id']."&amp;type=port_mac_acc_total&amp;sort=".$vars['sort']."&amp;stat=".$vars['graph']."&amp;from=".$config['time']['week']."&amp;to=".$config['time']['now']."&amp;width=150&amp;height=50' />
            </a>
            </div>
            <div style='margin: 0px 10px 5px 0px; padding:5px; background: #e5e5e5;'>
            <span class=device-head>Month</span><br />
            <a href='".generate_url($link_array,array('view' => 'macaccounting', 'subview' => 'top10', 'graph'=>$vars['graph'], sort => $vars['sort'], 'period' => 'month'))."/'>
            <img style='border: #5e5e5e 2px;' valign=middle src='graph.php?id=".$port['port_id']."&amp;type=port_mac_acc_total&amp;sort=".$vars['sort']."&amp;stat=".$vars['graph']."&amp;from=".$config['time']['month']."&amp;to=".$config['time']['now']."&amp;width=150&amp;height=50' />
            </a>
            </div>
            <div style='margin: 0px 10px 5px 0px; padding:5px; background: #e5e5e5;'>
            <span class=device-head>Year</span><br />
            <a href='".generate_url($link_array,array('view' => 'macaccounting', 'subview' => 'top10', 'graph'=>$vars['graph'], sort => $vars['sort'], 'period' => 'year'))."/'>
            <img style='border: #5e5e5e 2px;' valign=middle src='graph.php?id=".$port['port_id']."&amp;type=port_mac_acc_total&amp;sort=".$vars['sort']."&amp;stat=".$vars['graph']."&amp;from=".$config['time']['year']."&amp;to=".$config['time']['now']."&amp;width=150&amp;height=50' />
            </a>
            </div>
       </div>
       <div style='float: left;'>
         <img src='graph.php?id=".$port['port_id']."&amp;type=port_mac_acc_total&amp;sort=".$vars['sort']."&amp;stat=".$vars['graph']."&amp;from=$from&amp;to=".$config['time']['now']."&amp;width=745&amp;height=300' />
       </div>
       <div style=' margin:0px; float: left;';>
            <div style='margin: 0px 0px 5px 10px; padding:5px; background: #e5e5e5;'>
           <span class=device-head>Traffic</span><br />
           <a href='".generate_url($link_array,array('view' => 'macaccounting', 'subview' => 'top10', 'graph'=>'bits', sort => $vars['sort'], 'period' => $vars['period']))."'>
             <img style='border: #5e5e5e 2px;' valign=middle src='graph.php?id=".$port['port_id']."&amp;stat=bits&amp;type=port_mac_acc_total&amp;sort=".$vars['sort']."&amp;from=$from&amp;to=".$config['time']['now']."&amp;width=150&amp;height=50' />
           </a>
           </div>
           <div style='margin: 0px 0px 5px 10px; padding:5px; background: #e5e5e5;'>
           <span class=device-head>Packets</span><br />
           <a href='".generate_url($link_array,array('view' => 'macaccounting', 'subview' => 'top10', 'graph'=>'pkts', sort => $vars['sort'], 'period' => $vars['period']))."/'>
             <img style='border: #5e5e5e 2px;' valign=middle src='graph.php?id=".$port['port_id']."&amp;stat=pkts&amp;type=port_mac_acc_total&amp;sort=".$vars['sort']."&amp;from=$from&amp;to=".$config['time']['now']."&amp;width=150&amp;height=50' />
           </a>
           </div>
           <div style='margin: 0px 0px 5px 10px; padding:5px; background: #e5e5e5;'>
           <span class=device-head>Top Input</span><br />
           <a href='".generate_url($link_array,array('view' => 'macaccounting', 'subview' => 'top10', 'graph'=>$vars['graph'], sort => 'in', 'period' => $vars['period']))."'>
             <img style='border: #5e5e5e 2px;' valign=middle src='graph.php?id=".$port['port_id'].
                    "&amp;stat=".$vars['graph']."&amp;type=port_mac_acc_total&amp;sort=in&amp;from=$from&amp;to=".$config['time']['now']."&amp;width=150&amp;height=50' />
           </a>
           </div>
           <div style='margin: 0px 0px 5px 10px; padding:5px; background: #e5e5e5;'>
           <span class=device-head>Top Output</span><br />
           <a href='".generate_url($link_array,array('view' => 'macaccounting', 'subview' => 'top10', 'graph'=>$vars['graph'], sort => 'out', 'period' => $vars['period']))."'>
             <img style='border: #5e5e5e 2px;' valign=middle src='graph.php?id=".$port['port_id'].
                    "&amp;stat=".$vars['graph']."&amp;type=port_mac_acc_total&amp;sort=out&amp;from=$from&amp;to=".$config['time']['now']."&amp;width=150&amp;height=50' />
           </a>
           </div>
           <div style='margin: 0px 0px 5px 10px; padding:5px; background: #e5e5e5;'>
           <span class=device-head>Top Aggregate</span><br />
           <a href='".generate_url($link_array,array('view' => 'macaccounting', 'subview' => 'top10', 'graph'=>$vars['graph'], sort => 'both', 'period' => $vars['period']))."'>
             <img style='border: #5e5e5e 2px;' valign=middle src='graph.php?id=".$port['port_id'].
                    "&amp;stat=".$vars['graph']."&amp;type=port_mac_acc_total&amp;sort=both&amp;from=$from&amp;to=".$config['time']['now']."&amp;width=150&amp;height=50' />
           </a>
           </div>
       </div>
     </div>
");
  unset($query);
  }
  else
  {

  $query = "SELECT *, `mac_accounting`.`ma_id` as `ma_id` FROM `mac_accounting` LEFT JOIN `mac_accounting-state` ON  `mac_accounting`.`ma_id` =  `mac_accounting-state`.`ma_id` WHERE port_id = ?";

#  $query = "SELECT *, (M.bytes_input_rate + M.bytes_output_rate) as bps FROM `mac_accounting` AS M,
#                       `ports` AS I, `devices` AS D WHERE M.port_id = ? AND I.port_id = M.port_id AND I.device_id = D.device_id ORDER BY bps DESC";
  $param = array($port['port_id']);

  if($vars['subview'] == "graphs") { $table_class = "table-striped-two"; } else { $table_class = "table-striped"; }

  echo('<table class="table table-hover table-condensed table-rounded table-bordered '.$table_class.'">');
  echo('  <thead>');

  echo('<tr>');

  $cols = array(
              'BLANK' => NULL,
              'mac' => 'MAC Address',
              'BLANK' => NULL,
              'ip' => 'IP Address',
              'graphs' => NULL,
              'bps_in' => 'Traffic In',
              'bps_out' => 'Traffic Out',
              'pkts_in' => 'Packets In',
              'pkts_out' => 'Packets Out',
              'BLANK' => NULL);

foreach ($cols as $sort => $col)
{
  if ($col == NULL)
  {
    echo('<th></th>');
  }
  elseif ($vars['sort'] == $sort)
  {
    echo('<th>'.$col.' *</th>');
  } else {
    echo('<th><a href="'. generate_url($vars, array('sort' => $sort)).'">'.$col.'</a></th>');
  }
}

  echo("      </tr>");
  echo('  </thead>');



  $ma_array = dbFetchRows($query, $param);

  switch ($vars['sort'])
  {
    case 'bps_in':
      $ma_array = array_sort($ma_array, 'bytes_input_rate', SORT_DESC);
      break;
    case 'bps_out':
      $ma_array = array_sort($ma_array, 'bytes_output_rate', SORT_DESC);
      break;
    case 'pkts_in':
      $ma_array = array_sort($ma_array, 'bytes_input_rate', SORT_DESC);
      break;
    case 'pkts_out':
      $ma_array = array_sort($ma_array, 'bytes_output_rate', SORT_DESC);
      break;
  }

  foreach ($ma_array as $acc)
  {

    $ips = array();
    foreach (dbFetchRows("SELECT * FROM `ip_mac` WHERE `mac_address` = ?", array($acc['mac'], $acc['port_id'])) AS $ip)
    {
      $ips[] = $ip['ip_address'];
    }

    unset($name);
    ///FIXME. Need rewrite, because $addy is array with multiple items.
    #$name = gethostbyaddr($addy['ipv4_address']); FIXME - Maybe some caching for this?

    $arp_host = dbFetchRow("SELECT * FROM ipv4_addresses AS A, ports AS I, devices AS D WHERE A.ipv4_address = ? AND I.port_id = A.port_id AND D.device_id = I.device_id", array($addy['ip_address']));
    if ($arp_host) { $arp_name = generate_device_link($arp_host); $arp_name .= " ".generate_port_link($arp_host); } else { unset($arp_if); }

    if ($name == $addy['ip_address']) { unset ($name); }
    if (dbFetchCell("SELECT COUNT(*) FROM bgpPeers WHERE device_id = ? AND bgpPeerIdentifier = ?", array($acc['device_id'], $addy['ip_address'])))
    {
      $peer_info = dbFetchRow("SELECT * FROM bgpPeers WHERE device_id = ? AND bgpPeerIdentifier = ?", array($acc['device_id'], $addy['ip_address']));
    } else { unset ($peer_info); }

    if ($peer_info)
    {
      $asn = "AS".$peer_info['bgpPeerRemoteAs']; $astext = $peer_info['astext'];
    } else {
      unset ($as); unset ($astext); unset($asn);
    }

    if ($vars['graph'])
    {
      $graph_type = "macaccounting_" . $vars['graph'];
    } else {
      $graph_type = "macaccounting_bits";
    }

    if ($vars['subview'] == "minigraphs")
    {
      if (!$asn) { $asn = "No Session"; }

     echo("<div style='display: block; padding: 3px; margin: 3px; min-width: 221px; max-width:221px; min-height:90px; max-height:90px; text-align: center; float: left; background-color: #e5e5e5;'>
      ".$addy['ipv4_address']." - ".$asn."
          <a href='#' onmouseover=\"return overlib('\
     <div style=\'font-size: 16px; padding:5px; font-weight: bold; color: #555555;\'>".$name." - ".$addy['ipv4_address']." - ".$asn."</div>\
     <img src=\'graph.php?id=" . $acc['ma_id'] . "&amp;type=$graph_type&amp;from=".$config['time']['twoday']."&amp;to=".$config['time']['now']."&amp;width=450&amp;height=150\'>\
     ', CENTER, LEFT, FGCOLOR, '#e5e5e5', BGCOLOR, '#e5e5e5', WIDTH, 400, HEIGHT, 150);\" onmouseout=\"return nd();\" >
          <img src='graph.php?id=" . $acc['ma_id'] . "&amp;type=$graph_type&amp;from=".$config['time']['twoday']."&amp;to=".$config['time']['now']."&amp;width=213&amp;height=45'></a>

          <span style='font-size: 10px;'>".$name."</span>
         </div>");

   }
   else
   {
     echo("
        <tr>
          <td width=20></td>
          <td width=200><bold>".mac_clean_to_readable($acc['mac'])."</bold></td>
          <td width=200>".implode($ips, "<br />")."</td>
          <td width=500>".$name." ".$arp_name . "</td>
          <td width=100>".formatRates($acc['bytes_input_rate'] / 8)."</td>
          <td width=100>".formatRates($acc['bytes_output_rate'] / 8)."</td>
          <td width=100>".format_number($acc['pkts_input_rate'] / 8)."pps</td>
          <td width=100>".format_number($acc['pkts_output_rate'] / 8)."pps</td>
        </tr>
    ");

     $peer_info['astext'];

     $graph_array['type']   = $graph_type;
     $graph_array['id']     = $acc['ma_id'];
     $graph_array['height'] = "100";
     $graph_array['to']     = $config['time']['now'];
     echo('<tr><td colspan="8">');

     include("includes/print-graphrow.inc.php");

     echo("</td></tr>");

     $i++;
    }
  }
  echo("</table>");
}

?>
