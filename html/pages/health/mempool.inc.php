<?php

$graph_type = "mempool_usage";

if ($vars['view'] == "graphs") { $stripe_class = "table-striped-two"; } else { $stripe_class = "table-striped"; }

echo('<table class="table '.$stripe_class.' table-condensed">');
echo('  <thead>');
echo('    <tr>');
echo('      <th width="250">Device</th>');
echo('      <th>Memory</th>');
echo('      <th width=100></th>');
echo('      <th width="280">Usage</th>');
echo('      <th width="50">Used</th>');
echo('    </tr>');
echo('  </thead>');
$sql  = "SELECT *, `mempools`.`mempool_id` AS `mempool_id`";
$sql .= " FROM  `mempools`";
$sql .= " JOIN `devices` ON  `mempools`.`device_id` =  `devices`.`device_id`";
$sql .= " LEFT JOIN  `mempools-state` ON  `mempools`.mempool_id =  `mempools-state`.mempool_id";
$sql .= " ORDER BY `devices`.`hostname`";

foreach (dbFetchRows($sql) as $mempool)
{
  if (isset($cache['devices']['id'][$mempool['device_id']]))
  {
    if (!$config['web_show_disabled'])
    {
      if ($cache['devices']['id'][$mempool['device_id']]['disabled']) { continue; }
    }

    $graph_array           = array();
    $graph_array['to']     = $config['time']['now'];
    $graph_array['id']     = $mempool['mempool_id'];
    $graph_array['type']   = $graph_type;
    $graph_array['legend'] = "no";

    $link_array = $graph_array;
    $link_array['page'] = "graphs";
    unset($link_array['height'], $link_array['width'], $link_array['legend']);
    $link_graph = generate_url($link_array);

    $link = generate_url( array("page" => "device", "device" => $mempool['device_id'], "tab" => "health", "metric" => 'mempool'));

    $overlib_content = generate_overlib_content($graph_array, $mempool['hostname'] ." - " . $mempool['mempool_descr'], NULL);

    $graph_array['width'] = 80; $graph_array['height'] = 20; $graph_array['bg'] = 'ffffff00'; # the 00 at the end makes the area transparent.
    $graph_array['from'] = $config['time']['day'];
    $mini_graph =  generate_graph_tag($graph_array);

    $total = formatStorage($mempool['mempool_total']);
    $used = formatStorage($mempool['mempool_used']);
    $free = formatStorage($mempool['mempool_free']);

    $background = get_percentage_colours($mempool['mempool_perc']);

    echo('<tr>
          <td class=list-bold>' . generate_device_link($mempool) . '</td>
          <td>'.overlib_link($link, $mempool['mempool_descr'],$overlib_content).'</td>
          <td>'.overlib_link($link_graph, $mini_graph, $overlib_content).'</td>
          <td><a href="'.$proc_url.'" '.$proc_popup.'>
            '.print_percentage_bar (400, 20, $mempool['mempool_perc'], $used.' / '.$total, "ffffff", $background['left'], $free , "ffffff", $background['right']).'
            </a>
          </td>
          <td>'.$mempool['mempool_perc'].'%</td>
        </tr>
     ');

    if ($vars['view'] == "graphs")
    {
      echo("<tr><td colspan=5>");

      unset($graph_array['height'], $graph_array['width'], $graph_array['legend']);
      $graph_array['to']     = $config['time']['now'];
      $graph_array['id']     = $mempool['mempool_id'];
      $graph_array['type']   = $graph_type;

      include("includes/print-graphrow.inc.php");

      echo("</td></tr>");
    } # endif graphs
  }
}

echo("</table>");

?>
