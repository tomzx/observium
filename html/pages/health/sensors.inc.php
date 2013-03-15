<?php

$sql  = "SELECT *, `sensors`.`sensor_id` AS `sensor_id`";
$sql .= " FROM `sensors`";
$sql .= " JOIN `devices` ON `sensors`.`device_id` = `devices`.`device_id`";
$sql .= " LEFT JOIN  `sensors-state` ON `sensors`.`sensor_id` = `sensors-state`.`sensor_id`";
$sql .= " WHERE `sensors`.`sensor_class` = '".$class."'";
$sql .= " ORDER BY `devices`.`hostname`, `sensors`.`sensor_descr`";

if ($vars['view'] == "graphs") { $stripe_class = "table-striped-two"; } else { $stripe_class = "table-striped"; }

echo('<table class="table '.$stripe_class.' table-condensed" style="margin-top: 10px;">');
echo('  <thead>');
echo('    <tr>');
echo('      <th width="250">Device</th>');
echo('      <th>Sensor</th>');
echo('      <th width="40"></th>');
echo('      <th width="100"></th>');
echo('      <th width="100">Current</th>');
echo('      <th width="175">Thresholds</th>');
echo('    </tr>');
echo('  </thead>');

echo('  <tbody>');

foreach (dbFetchRows($sql, $param) as $sensor)
{

 if(device_permitted($sensor['device_id']))
 {

  $alert = "";
  if (!is_numeric($sensor['sensor_value']))
  {
    $sensor['sensor_value'] = "NaN";
  } else {
    if ($sensor['sensor_value'] >= $sensor['sensor_limit']) { $alert = '<img src="images/16/flag_red.png" alt="alert" />'; }
  }

    // FIXME - make this "four graphs in popup" a function/include and "small graph" a function.
    // FIXME - So now we need to clean this up and move it into a function. Isn't it just "print-graphrow"?
    // FIXME - DUPLICATED IN device/overview/sensors

    $graph_array           = array();
    $graph_array['to']     = $config['time']['now'];
    $graph_array['id']     = $sensor['sensor_id'];
    $graph_array['type']   = $graph_type;
    $graph_array['legend'] = "no";

    $link_array = $graph_array;
    $link_array['page'] = "graphs";
    unset($link_array['height'], $link_array['width'], $link_array['legend']);
    $link_graph = generate_url($link_array);

    $link = generate_url(array("page" => "device", "device" => $sensor['device_id'], "tab" => "health", "metric" => $sensor['sensor_class']));

    $overlib_content = generate_overlib_content($graph_array, $sensor['hostname'] ." - " . $sensor['sensor_descr'], NULL);

    $graph_array['width'] = 80; $graph_array['height'] = 20; $graph_array['bg'] = 'ffffff00'; # the 00 at the end makes the area transparent.
    $graph_array['from'] = $config['time']['day'];
    $sensor_minigraph =  generate_graph_tag($graph_array);

    $sensor['sensor_descr'] = truncate($sensor['sensor_descr'], 48, '');

    if ($class == "frequency") {
      echo('<tr>
          <td class=list-bold>' . generate_device_link($sensor) . '</td>
          <td>'.overlib_link($link, $sensor['sensor_descr'],$overlib_content).'</td>
          <td>'.$alert.'</td>
          <td>'.overlib_link($link_graph, $sensor_minigraph, $overlib_content).'</td>
          <td style="font-weight: bold;">' . format_si($sensor['sensor_value']) . $unit . '</td>
          <td>' . format_si(round($sensor['sensor_limit_low'],2)) . $unit . ' - ' . format_si(round($sensor['sensor_limit'],2)) . $unit . '</td>
        </tr>
      ');
    } else{
      echo('<tr>
          <td class=list-bold>' . generate_device_link($sensor) . '</td>
          <td>'.overlib_link($link, $sensor['sensor_descr'],$overlib_content).'</td>
          <td>'.$alert.'</td>
          <td>'.overlib_link($link_graph, $sensor_minigraph, $overlib_content).'</td>
          <td style="font-weight: bold;">' . $sensor['sensor_value'] . $unit . '</td>
          <td>' . round($sensor['sensor_limit_low'],2) . $unit . ' - ' . round($sensor['sensor_limit'],2) . $unit . '</td>
        </tr>
      ');
    }

    if ($vars['view'] == "graphs")
    {
      echo("<tr><td colspan=7>");

      unset($graph_array['height'], $graph_array['width'], $graph_array['legend']);
      $graph_array['to']     = $config['time']['now'];
      $graph_array['id']     = $sensor['sensor_id'];
      $graph_array['type']   = $graph_type;

      include("includes/print-graphrow.inc.php");

      echo("</td></tr>");
    } # endif graphs

 }
}

echo("</tbody>");
echo("</table>");

?>
