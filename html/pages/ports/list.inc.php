<?php

// Pagination
if(!$vars['pagesize']) { $vars['pagesize'] = "100"; }
if(!$vars['pageno']) { $vars['pageno'] = "1"; }
echo pagination($vars, count($ports));

if($vars['pageno'])
{
  $ports = array_chunk($ports, $vars['pagesize']);
  $ports = $ports[$vars['pageno']-1];
}
// End Pagination


// Populate ports array (much faster for large systems)

$where = " WHERE 0 ";
$param = array();

foreach($ports as $p)
{
  $where    .= " OR `ports`.`port_id` = ?";
  $param[]  = $p['port_id'];
}

$select = "`ports`.*,`ports-state`.*,`ports`.`port_id` as `port_id`";
#$select = "*,`ports`.`port_id` as `port_id`";


include("includes/port-sort-select.inc.php");

$sql  = "SELECT ".$select;
$sql .= " FROM  `ports`";
$sql .= " JOIN `devices` ON  `ports`.`device_id` =  `devices`.`device_id`";
$sql .= " LEFT JOIN `ports-state` ON  `ports`.`port_id` =  `ports-state`.`port_id`";
$sql .= " ".$where;

unset($ports);

$ports = dbFetchRows($sql, $param);

// Re-sort because the DB doesn't do that.
include("includes/port-sort.inc.php");

// End populating ports array


echo('<table class="table table-striped table-bordered table-rounded table-condensed" style="margin-top: 10px;">');
echo('  <thead>');

echo('<tr class="entity">');
echo("      <th style='width: 1px'></th>\n");
echo("      <th style='width: 1px'></th>\n");

$cols = array(array('head' => 'Device', 'sort' => 'device', 'width' => 250),
              array('head' => 'Port', 'sort' => 'port', 'width' => '350'),
              array('head' => 'Traffic', 'sort' => 'traffic', 'width' => '100'),
              array('head' => 'Traffic %', 'sort' => 'traffic_perc', 'width' => '90'),
              array('head' => 'Packets', 'sort' => 'packets', 'width' => '90'),
              array('head' => 'Speed', 'sort' => 'speed', 'width' => '90'),
              array('head' => 'MAC Address', 'sort' => 'mac', 'width' => '150')
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

    $device = &$GLOBALS['cache']['devices']['id'][$port['device_id']];

    $ports_total++;

    humanize_port($port);

    if ($port['in_errors'] > 0 || $port['out_errors'] > 0)
    {
      $error_img = generate_port_link($port,"<img src='images/16/chart_curve_error.png' alt='Interface Errors' border=0>",errors);
    } else { $error_img = ""; }

    $port['bps_in'] = formatRates($port['ifInOctets_rate'] * 8);
    $port['bps_out'] = formatRates($port['ifOutOctets_rate'] * 8);

    $port['pps_in'] = format_si($port['ifInUcastPkts_rate'])."pps";
    $port['pps_out'] = format_si($port['ifOutUcastPkts_rate'])."pps";

    echo "<tr class='ports'>
          <td style='background-color: ".$table_tab_colour.";'></td>
          <td></td>
          <td><span class=entity>".generate_device_link($device, shorthost($device['hostname'], "20"))."</span><br />
              <span class=em>".truncate($port['location'],32,"")."</span></td>

          <td><span class=entity>" . generate_port_link($port, fixIfName($port['label']))." ".$error_img."</span><br />
              <span class=em>".truncate($port['ifAlias'], 50, '')."</span></td>".

    '<td> <i class="icon-circle-arrow-down" style="',$port['bps_in_style'],'"></i>  <span class="small" style="',$port['bps_in_style'],'">' , formatRates($port['in_rate'])  , '</span><br />'.
       '<i class="icon-circle-arrow-up" style="',$port['bps_out_style'],'"></i> <span class="small" style="',$port['bps_out_style'],'">' , formatRates($port['out_rate']) , '</span><br /></td>'.

    '<td> <i class="icon-circle-arrow-down" style="',$port['bps_in_style'],'"></i>  <span class="small" style="',$port['bps_in_style'],'">' , $port['ifInOctets_perc']  , '%</span><br />'.
       '<i class="icon-circle-arrow-up" style="',$port['bps_out_style'],'"></i> <span class="small" style="',$port['bps_out_style'],'">' ,  $port['ifOutOctets_perc'], '%</span><br /></td>'.

       '<td><i class="icon-circle-arrow-down" style="',$port['pps_in_style'],'"></i>  <span class="small" style="',$port['pps_in_style'],'">' , format_bi($port['ifInUcastPkts_rate']) ,'pps</span><br />',
       '<i class="icon-circle-arrow-up" style="',$port['pps_out_style'],'"></i> <span class="small" style="',$port['pps_out_style'],'">' , format_bi($port['ifOutUcastPkts_rate']),'pps</span></td>',

          "<td>".$port['human_speed']."<br />".$port['ifMtu']."</td>
          <td >".$port['human_type']."<br />".$port['human_mac']."</td>
        </tr>\n";
#  }
}

echo('</td></tr></table>');

echo pagination($vars, count($ports));

?>
