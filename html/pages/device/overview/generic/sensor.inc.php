<?php

$sql  = "SELECT *, `sensors`.`sensor_id` AS `sensor_id`";
$sql .= " FROM  `sensors`";
$sql .= " LEFT JOIN  `sensors-state` ON  `sensors`.sensor_id =  `sensors-state`.sensor_id";
$sql .= " WHERE `sensor_class` = ? AND `device_id` = ? ORDER BY `sensor_descr`";

$sensors = dbFetchRows($sql, array($sensor_class, $device['device_id']));

if (count($sensors))
{
?>

<div class="well info_box">
    <div class="title"><a href="<?php echo(generate_url(array('page' => 'device', 'device' => $device['device_id'], 'tab' => 'health', 'metric' => strtolower($sensor_type)))); ?>">
      <i class="<?php echo($config['sensor_types'][strtolower($sensor_type)]['icon']); ?>"></i> <?php echo($sensor_type) ?></a></div>
    <div class="content">

<?php

  echo('<table class="table table-condensed-more table-striped">');
  foreach ($sensors as $sensor)
  {
    if (!is_numeric($sensor['sensor_value']))
    {
      $sensor['sensor_value'] = "NaN";
    }

    // FIXME - make this "four graphs in popup" a function/include and "small graph" a function.
    // FIXME - So now we need to clean this up and move it into a function. Isn't it just "print-graphrow"?
    // FIXME - DUPLICATED IN health/sensors

    $graph_colour = str_replace("#", "", $row_colour);

    $graph_array           = array();
    $graph_array['to']     = $config['time']['now'];
    $graph_array['id']     = $sensor['sensor_id'];
    $graph_array['type']   = $graph_type;
    $graph_array['from']   = $config['time']['day'];
    $graph_array['legend'] = "no";

    $link_array = $graph_array;
    $link_array['page'] = "graphs";
    unset($link_array['height'], $link_array['width'], $link_array['legend']);
    $link = generate_url($link_array);

    $overlib_content = generate_overlib_content($graph_array);

    $graph_array['width'] = 80; $graph_array['height'] = 20; $graph_array['bg'] = 'ffffff00'; # the 00 at the end makes the area transparent.
    $graph_array['from'] = $config['time']['day'];
    $sensor_minigraph =  generate_graph_tag($graph_array);

    $sensor['sensor_descr'] = truncate($sensor['sensor_descr'], 48, '');

    if (strtolower($sensor_type) == "frequency") {
      echo("<tr class=device-overview>
           <td class=strong style='padding-left:5px;'><strong>".overlib_link($link, $sensor['sensor_descr'], $overlib_content)."</strong></td>
           <td width=80 align=right class=strong>".overlib_link($link, $sensor_minigraph, $overlib_content)."</td>
           <td width=80 align=right class=strong>".overlib_link($link, "<span " . ($sensor['sensor_value'] < $sensor['sensor_limit_low'] || $sensor['sensor_value'] > $sensor['sensor_limit'] ? "style='color: red'" : '') . '>' . format_si($sensor['sensor_value']) . $sensor_unit . "</span>", $overlib_content)."</td>
          </tr>");
    } else {
      echo("<tr class=device-overview>
           <td class=strong style='padding-left:5px;'><strong>".overlib_link($link, $sensor['sensor_descr'], $overlib_content)."</strong></td>
           <td width=80 align=right class=strong>".overlib_link($link, $sensor_minigraph, $overlib_content)."</td>
           <td width=80 align=right class=strong>".overlib_link($link, "<span " . ($sensor['sensor_value'] < $sensor['sensor_limit_low'] || $sensor['sensor_value'] > $sensor['sensor_limit'] ? "style='color: red'" : '') . '>' . $sensor['sensor_value'] . $sensor_unit . "</span>", $overlib_content)."</td>
          </tr>");
    }
  }

  echo("</table>");
  echo("</div></div>");
}

?>
