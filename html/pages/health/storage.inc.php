<?php

$graph_type = "storage_usage";

$sql  = "SELECT *, `storage`.`storage_id` as `storage_id`";
$sql .= " FROM  `storage`";
$sql .= " LEFT JOIN  `devices` ON  `storage`.device_id =  `devices`.device_id";
$sql .= " LEFT JOIN  `storage-state` ON  `storage`.storage_id =  `storage-state`.storage_id";
$sql .= " ORDER BY `hostname`, `storage_descr`";

if ($vars['view'] == "graphs") { $stripe_class = "table-striped-two"; } else { $stripe_class = "table-striped"; }

echo('<table class="table '.$stripe_class.' table-condensed" style="margin-top: 10px;">');
echo('  <thead>');
echo('    <tr>');
echo('      <th width="250">Device</th>');
echo('      <th>Memory</th>');
echo('      <th width="100"></th>');
echo('      <th width="280">Usage</th>');
echo('      <th width="50">Used</th>');
echo('    </tr>');
echo('  </thead>');

foreach (dbFetchRows($sql) as $storage)
{
  if (device_permitted($storage['device_id']))
  {

    $graph_array           = array();
    $graph_array['to']     = $config['time']['now'];
    $graph_array['id']     = $storage['storage_id'];
    $graph_array['type']   = $graph_type;
    $graph_array['legend'] = "no";

    $link_array = $graph_array;
    $link_array['page'] = "graphs";
    unset($link_array['height'], $link_array['width'], $link_array['legend']);
    $link_graph = generate_url($link_array);

    $link = generate_url( array("page" => "device", "device" => $storage['device_id'], "tab" => "health", "metric" => 'storage'));

    $overlib_content = generate_overlib_content($graph_array, $storage['hostname'] ." - " . $storage['storage_descr'], NULL);

    $graph_array['width'] = 80; $graph_array['height'] = 20; $graph_array['bg'] = 'ffffff00'; # the 00 at the end makes the area transparent.
    $graph_array['from'] = $config['time']['day'];
    $mini_graph =  generate_graph_tag($graph_array);

    $total = formatStorage($storage['storage_total']);
    $used = formatStorage($storage['storage_used']);
    $free = formatStorage($storage['storage_free']);

    $background = get_percentage_colours($storage['storage_perc']);

    echo('<tr>
          <td class=list-bold>' . generate_device_link($storage) . '</td>
          <td>'.overlib_link($link, $storage['storage_descr'],$overlib_content).'</td>
          <td>'.overlib_link($link_graph, $mini_graph, $overlib_content).'</td>
          <td><a href="'.$proc_url.'" '.$proc_popup.'>
            '.print_percentage_bar (400, 20, $storage['storage_perc'], $used.' / '.$total, "ffffff", $background['left'], $free , "ffffff", $background['right']).'
            </a>
          </td>
          <td>'.$storage['storage_perc'].'%</td>
        </tr>
     ');

    if ($vars['view'] == "graphs")
    {
      echo("<tr><td colspan=5>");

      unset($graph_array['height'], $graph_array['width'], $graph_array['legend']);
      $graph_array['to']     = $config['time']['now'];
      $graph_array['id']     = $storage['storage_id'];
      $graph_array['type']   = $graph_type;

      include("includes/print-graphrow.inc.php");

      echo("</td></tr>");
    } # endif graphs
  }
}

echo("</table>");

?>
