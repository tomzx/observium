<?php

$sql  = "SELECT *, `sensors`.`sensor_id` AS `sensor_id`";
$sql .= " FROM `sensors`";
$sql .= " JOIN `devices` ON `sensors`.`device_id` = `devices`.`device_id`";
$sql .= " LEFT JOIN  `sensors-state` ON `sensors`.`sensor_id` = `sensors-state`.`sensor_id`";
$sql .= " WHERE `sensors`.`sensor_class` = '".$class."'";
$sql .= " ORDER BY `devices`.`hostname`, `sensors`.`sensor_descr`";

echo('<table cellspacing="0" cellpadding="6" width="100%">');

echo('<tr class=tablehead>
        <th width="280">Device</th>
        <th width="180">Sensor</th>
        <th></th>
        <th></th>
        <th width="100">Current</th>
        <th width="250">Range limit</th>
        <th>Notes</th>
      </tr>');

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

    echo('<tr class="health">
          <td class=list-bold>' . generate_device_link($sensor) . '</td>
          <td>'.overlib_link($link, $sensor['sensor_descr'],$overlib_content).'</td>
          <td width=100>'.overlib_link($link_graph, $sensor_minigraph, $overlib_content).'</td>
          <td width=50>'.$alert.'</td>
          <td style="text-align: center; font-weight: bold;">' . $sensor['sensor_value'] . $unit . '</td>
          <td style="text-align: center">' . round($sensor['sensor_limit_low'],2) . $unit . ' - ' . round($sensor['sensor_limit'],2) . $unit . '</td>
          <td>' . (isset($sensor['sensor_notes']) ? $sensor['sensor_notes'] : '') . '</td>
        </tr>
     ');

  if ($vars['view'] == "graphs")
  {
    echo("<tr></tr><tr class='health'><td colspan=7>");

    $daily_graph   = "graph.php?id=" . $sensor['sensor_id'] . "&amp;type=".$graph_type."&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=211&amp;height=100";
    $daily_url     = "graph.php?id=" . $sensor['sensor_id'] . "&amp;type=".$graph_type."&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=400&amp;height=150";

    $weekly_graph  = "graph.php?id=" . $sensor['sensor_id'] . "&amp;type=".$graph_type."&amp;from=".$config['time']['week']."&amp;to=".$config['time']['now']."&amp;width=211&amp;height=100";
    $weekly_url    = "graph.php?id=" . $sensor['sensor_id'] . "&amp;type=".$graph_type."&amp;from=".$config['time']['week']."&amp;to=".$config['time']['now']."&amp;width=400&amp;height=150";

    $monthly_graph = "graph.php?id=" . $sensor['sensor_id'] . "&amp;type=".$graph_type."&amp;from=".$config['time']['month']."&amp;to=".$config['time']['now']."&amp;width=211&amp;height=100";
    $monthly_url   = "graph.php?id=" . $sensor['sensor_id'] . "&amp;type=".$graph_type."&amp;from=".$config['time']['month']."&amp;to=".$config['time']['now']."&amp;width=400&amp;height=150";

    $yearly_graph  = "graph.php?id=" . $sensor['sensor_id'] . "&amp;type=".$graph_type."&amp;from=".$config['time']['yearh']."&amp;to=".$config['time']['now']."&amp;width=211&amp;height=100";
    $yearly_url    = "graph.php?id=" . $sensor['sensor_id'] . "&amp;type=".$graph_type."&amp;from=".$config['time']['yearh']."&amp;to=".$config['time']['now']."&amp;width=400&amp;height=150";

    echo("<a onmouseover=\"return overlib('<img src=\'$daily_url\'>', LEFT);\" onmouseout=\"return nd();\">
        <img src='$daily_graph' border=0></a> ");
    echo("<a onmouseover=\"return overlib('<img src=\'$weekly_url\'>', LEFT);\" onmouseout=\"return nd();\">
        <img src='$weekly_graph' border=0></a> ");
    echo("<a onmouseover=\"return overlib('<img src=\'$monthly_url\'>', LEFT);\" onmouseout=\"return nd();\">
        <img src='$monthly_graph' border=0></a> ");
    echo("<a onmouseover=\"return overlib('<img src=\'$yearly_url\'>', LEFT);\" onmouseout=\"return nd();\">
        <img src='$yearly_graph' border=0></a>");
    echo("</td></tr>");
  } # endif graphs
 }
}

echo("</table>");

?>
