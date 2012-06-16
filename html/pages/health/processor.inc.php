<?php

$graph_type = "processor_usage";

echo("<div style='margin-top: 5px; padding: 0px;'>");
echo("  <table width=100% cellpadding=6 cellspacing=0>");

echo("<tr class=tablehead>
        <th width=280>Device</th>
        <th>Processor</th>
        <th width=100></th>
        <th width=280>Usage</th>
      </tr>");

$sql  = "SELECT *, `processors`.`processor_id` AS `processor_id`";
$sql .= " FROM `processors`";
$sql .= " JOIN `devices` ON `processors`.`device_id` = `devices`.`device_id`";
$sql .= " LEFT JOIN  `processors-state` ON `processors`.`processor_id` = `processors-state`.`processor_id`";
$sql .= " ORDER BY `devices`.`hostname`, `processors`.`processor_descr`";

foreach (dbFetchRows($sql) as $proc)
{
  if (device_permitted($proc['device_id']))
  {
    $device = $proc;

    // FIXME should that really be done here? :-)
    // FIXME - not it shouldn't. we need some per-os rewriting on discovery-time.
    $text_descr = $proc['processor_descr'];
    $text_descr = str_replace("Routing Processor", "RP", $text_descr);
    $text_descr = str_replace("Switching Processor", "SP", $text_descr);
    $text_descr = str_replace("Sub-Module", "Module ", $text_descr);
    $text_descr = str_replace("DFC Card", "DFC", $text_descr);

    $graph_array           = array();
    $graph_array['to']     = $config['time']['now'];
    $graph_array['id']     = $proc['processor_id'];
    $graph_array['type']   = $graph_type;
    $graph_array['legend'] = "no";

    $link_array = $graph_array;
    $link_array['page'] = "graphs";
    unset($link_array['height'], $link_array['width'], $link_array['legend']);
    $link_graph = generate_url($link_array);

    $link = generate_url( array("page" => "device", "device" => $proc['device_id'], "tab" => "health", "metric" => 'processor'));

    $overlib_content = generate_overlib_content($graph_array, $proc['hostname'] ." - " . $text_descr, NULL);

    $graph_array['width'] = 80; $graph_array['height'] = 20; $graph_array['bg'] = 'ffffff00'; # the 00 at the end makes the area transparent.
    $graph_array['from'] = $config['time']['day'];
    $mini_graph =  generate_graph_tag($graph_array);

    $perc = round($proc['processor_usage']);
    $background = get_percentage_colours($perc);

    echo('<tr class="health">
          <td class=list-bold>' . generate_device_link($proc) . '</td>
          <td>'.overlib_link($link, $text_descr,$overlib_content).'</td>
          <td width=100>'.overlib_link($link_graph, $mini_graph, $overlib_content).'</td>
          <td width="200"><a href="'.$proc_url.'" '.$proc_popup.'>
            '.print_percentage_bar (400, 20, $perc, $perc."%", "ffffff", $background['left'], (100 - $perc)."%" , "ffffff", $background['right']).'
            </a>
          </td>
        </tr>
     ');


    if ($vars['view'] == "graphs")
    {
      echo('    <tr></tr><tr class="health"><td colspan="5">');

      $daily_graph   = "graph.php?id=" . $proc['processor_id'] . "&amp;type=".$graph_type."&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=211&amp;height=100";
      $daily_url     = "graph.php?id=" . $proc['processor_id'] . "&amp;type=".$graph_type."&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=400&amp;height=150";

      $weekly_graph  = "graph.php?id=" . $proc['processor_id'] . "&amp;type=".$graph_type."&amp;from=".$config['time']['week']."&amp;to=".$config['time']['now']."&amp;width=211&amp;height=100";
      $weekly_url    = "graph.php?id=" . $proc['processor_id'] . "&amp;type=".$graph_type."&amp;from=".$config['time']['week']."&amp;to=".$config['time']['now']."&amp;width=400&amp;height=150";

      $monthly_graph = "graph.php?id=" . $proc['processor_id'] . "&amp;type=".$graph_type."&amp;from=".$config['time']['month']."&amp;to=".$config['time']['now']."&amp;width=211&amp;height=100";
      $monthly_url   = "graph.php?id=" . $proc['processor_id'] . "&amp;type=".$graph_type."&amp;from=".$config['time']['month']."&amp;to=".$config['time']['now']."&amp;width=400&amp;height=150";

      $yearly_graph  = "graph.php?id=" . $proc['processor_id'] . "&amp;type=".$graph_type."&amp;from=".$config['time']['yearh']."&amp;to=".$config['time']['now']."&amp;width=211&amp;height=100";
      $yearly_url    = "graph.php?id=" . $proc['processor_id'] . "&amp;type=".$graph_type."&amp;from=".$config['time']['yearh']."&amp;to=".$config['time']['now']."&amp;width=400&amp;height=150";

      echo("      <a onmouseover=\"return overlib('<img src=\'$daily_url\'>', LEFT);\" onmouseout=\"return nd();\">
        <img src=\"$daily_graph\" border=\"0\"></a> ");
      echo("      <a onmouseover=\"return overlib('<img src=\'$weekly_url\'>', LEFT);\" onmouseout=\"return nd();\">
        <img src=\"$weekly_graph\" border=\"0\"></a> ");
      echo("      <a onmouseover=\"return overlib('<img src=\'$monthly_url\'>', LEFT);\" onmouseout=\"return nd();\">
        <img src=\"$monthly_graph\" border=\"0\"></a> ");
      echo("      <a onmouseover=\"return overlib('<img src=\'$yearly_url\'>', LEFT);\" onmouseout=\"return nd();\">
        <img src=\"$yearly_graph\" border=\"0\"></a>");
      echo("  </td>
  </tr>");

    } #end graphs if
  }
}

echo("</table>");
echo("</div>");

?>
