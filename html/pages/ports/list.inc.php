
<table cellpadding="3" cellspacing="0" class="devicetable sortable" width="100%">

<?php

echo('<tr class="tablehead"><td></td>');

$cols = array('device' => 'Device',
              'port' => 'Port',
              'traffic' => 'Traffic',
              'traffic_perc' => 'Traffic %',
              'packets' => 'Packets',
              'speed' => 'Speed', );

foreach ($cols as $sort => $col)
{
  if ($vars['sort'] == $sort)
  {
    echo('<th>'.$col.' *</th>');
  } else {
    echo('<th><a href="'. generate_url($vars, array('sort' => $sort)).'">'.$col.'</a></th>');
  }
}

echo("      </tr>");

$ports_disabled = 0; $ports_down = 0; $ports_up = 0; $ports_total = 0;

foreach ($ports as $port)
{
  if (port_permitted($port['port_id'], $port['device_id']))
  {
    if ($port['ifAdminStatus'] == "down") { $ports_disabled++;
    } elseif ($port['ifAdminStatus'] == "up" && $port['ifOperStatus']== "down") { $ports_down++;
    } elseif ($port['ifAdminStatus'] == "up" && $port['ifOperStatus']== "up") { $ports_up++; }
    $ports_total++;

    $speed = humanspeed($port['ifSpeed']);
    $type  = fixiftype($port['ifType']);
    $ifclass = ifclass($port['ifOperStatus'], $port['ifAdminStatus']);
    $mac = formatMac($port['ifPhysAddress']);

    if ($port['in_errors'] > 0 || $port['out_errors'] > 0)
    {
      $error_img = generate_port_link($port,"<img src='images/16/chart_curve_error.png' alt='Interface Errors' border=0>",errors);
    } else { $error_img = ""; }

    $port['bps_in'] = formatRates($port['ifInOctets_rate'] * 8);
    $port['bps_out'] = formatRates($port['ifOutOctets_rate'] * 8);

    $port['pps_in'] = format_si($port['ifInUcastPkts_rate'])."pps";
    $port['pps_out'] = format_si($port['ifOutUcastPkts_rate'])."pps";

    $port = ifLabel($port, $device);
    echo("<tr class='ports'>
          <td width=5></td>
          <td width=200 class=list-bold>".generate_device_link($port, shorthost($port['hostname'], "20"))."</td>
          <td width=250><span class=list-bold>" . generate_port_link($port, fixIfName($port['label']))." ".$error_img."</span><br />
                                        ".$port['ifAlias']."</td>
          <td width=100><span class=green>&darr; ".$port['bps_in']."<br />
                        <span class=blue>&uarr; ".$port['bps_out']."<br />

          <td width=100><span class=green>".$port['ifInOctets_perc']."%<br />
                        <span class=blue>".$port['ifOutOctets_perc']."%<br />

          <td width=100><span class=purple>&darr; ".$port['pps_in']."<br />
                        <span class=orange>&uarr; ".$port['pps_out']."<br />
          <td width=110 >$speed<br />".$port['ifMtu']."</td>
          <td width=110 >$type<br />".$mac."</td>
        </tr>\n");
  }
}

echo('<tr><td colspan="7">');
echo("<strong>Matched Ports: $ports_total ( <span class=green>Up $ports_up</span> | <span class=red>Down $ports_down</span> | Disabled $ports_disabled )</strong>");
echo('</td></tr></table>');

?>
