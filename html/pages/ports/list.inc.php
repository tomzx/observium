<?php

/// Pagination
if(!$vars['pagesize']) { $vars['pagesize'] = "100"; }
if(!$vars['pageno']) { $vars['pageno'] = "1"; }
echo pagination($vars, count($ports));

if($vars['pageno'])
{
  $ports = array_chunk($ports, $vars['pagesize']);
  $ports = $ports[$vars['pageno']-1];
}
/// End Pagination

echo('<table class="table table-striped table-bordered table-rounded table-condensed" style="margin-top: 10px;">');
echo('  <thead>');

echo('<tr class="tablehead">');
echo("      <th style='width: 1px'></th>\n");
echo("      <th style='width: 1px'></th>\n");

$cols = array(array('head' => 'Device', 'sort' => 'device', 'width' => '200'),
              array('head' => 'Port', 'sort' => 'port', 'width' => '200'),
              array('head' => 'Traffic', 'sort' => 'traffic', 'width' => '200'),
              array('head' => 'Traffic %', 'sort' => 'traffic_perc', 'width' => '200'),
              array('head' => 'Packets', 'sort' => 'packets', 'width' => '200'),
              array('head' => 'Speed', 'sort' => 'speed', 'width' => '200'),
              array('head' => 'MAC Address', 'sort' => 'mac', 'width' => '200')
              );

foreach ($cols as $col)
{
  echo('<th');
  if (is_numeric($col['width'])) { echo(' width="'.$col['width'].'"'); }
  echo('>');
  if ($vars['sort'] == $col['sort'])
  {
    echo($col['head'].' *');
  } else {
    echo('<a href="'. generate_url($vars, array('sort' => $col['sort'])).'">'.$col['head'].'</a>');
  }
  echo("</th>");
}

echo("      </tr></thead>");

$ports_disabled = 0; $ports_down = 0; $ports_up = 0; $ports_total = 0;
foreach ($ports as $port)
{
  if (port_permitted($port['port_id'], $port['device_id']))
  {

    if ($port['ifAdminStatus'] == "down") { $ports_disabled++; $table_tab_colour = "#aaaaaa";
    } elseif ($port['ifAdminStatus'] == "up" && $port['ifOperStatus']== "down") { $ports_down++; $table_tab_colour = "#cc0000";
    } elseif ($port['ifAdminStatus'] == "up" && $port['ifOperStatus']== "lowerLayerDown") { $ports_down++; $table_tab_colour = "#ff6600";
    } elseif ($port['ifAdminStatus'] == "up" && $port['ifOperStatus']== "up") { $ports_up++; $table_tab_colour = "#194B7F"; }
    $ports_total++;

    $port = humanize_port($port);

    if ($port['in_errors'] > 0 || $port['out_errors'] > 0)
    {
      $error_img = generate_port_link($port,"<img src='images/16/chart_curve_error.png' alt='Interface Errors' border=0>",errors);
    } else { $error_img = ""; }

    $port['bps_in'] = formatRates($port['ifInOctets_rate'] * 8);
    $port['bps_out'] = formatRates($port['ifOutOctets_rate'] * 8);

    $port['pps_in'] = format_si($port['ifInUcastPkts_rate'])."pps";
    $port['pps_out'] = format_si($port['ifOutUcastPkts_rate'])."pps";

    echo("<tr class='ports'>
          <td style='background-color: ".$table_tab_colour.";'></td>
          <td></td>
          <td class=list-bold>".generate_device_link($port, shorthost($port['hostname'], "20"))."</td>
          <td><span class=list-bold>" . generate_port_link($port, fixIfName($port['label']))." ".$error_img."</span><br />
                                        ".$port['ifAlias']."</td>
          <td><span class=green>&darr; ".$port['bps_in']."<br />
                        <span class=blue>&uarr; ".$port['bps_out']."<br />

          <td><span class=green>".$port['ifInOctets_perc']."%<br />
                        <span class=blue>".$port['ifOutOctets_perc']."%<br />

          <td><span class=purple>&darr; ".$port['pps_in']."<br />
                        <span class=orange>&uarr; ".$port['pps_out']."<br />
          <td>".$port['human_speed']."<br />".$port['ifMtu']."</td>
          <td >".$port['human_type']."<br />".$port['human_mac']."</td>
        </tr>\n");
  }
}

echo('</td></tr></table>');

echo pagination($vars, count($ports));

?>
