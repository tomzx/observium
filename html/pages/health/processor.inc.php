<?php

$graph_type = "processor_usage";

if ($vars['view'] == "graphs") { $stripe_class = "table-striped-two"; } else { $stripe_class = "table-striped"; }

echo('<table class="table '.$stripe_class.' table-condensed" style="margin-top: 10px;">');
echo('  <thead>');
echo('    <tr>');
echo('      <th width="200">Device</th>');
echo('      <th>Processor</th>');
echo('      <th width="100"></th>');
echo('      <th width="250">Usage</th>');
echo('    </tr>');
echo('  </thead>');

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

    echo('<tr>
          <td>' . generate_device_link($proc) . '</td>
          <td>'.overlib_link($link, $text_descr,$overlib_content).'</td>
          <td>'.overlib_link($link_graph, $mini_graph, $overlib_content).'</td>
          <td><a href="'.$proc_url.'" '.$proc_popup.'>
            '.print_percentage_bar (400, 20, $perc, $perc."%", "ffffff", $background['left'], (100 - $perc)."%" , "ffffff", $background['right']).'
            </a>
          </td>
        </tr>
     ');


    if ($vars['view'] == "graphs")
    {
      echo("<tr><td colspan=5>");

      unset($graph_array['height'], $graph_array['width'], $graph_array['legend']);
      $graph_array['to']     = $config['time']['now'];
      $graph_array['id']     = $proc['processor_id'];
      $graph_array['type']   = $graph_type;

      include("includes/print-graphrow.inc.php");

      echo("</td></tr>");
    } # endif graphs

  }
}

echo("</table>");

?>
