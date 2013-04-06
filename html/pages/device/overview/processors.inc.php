<?php

$graph_type = "processor_usage";

$sql  = "SELECT *, `processors`.`processor_id` as `processor_id`";
$sql .= " FROM  `processors`";
$sql .= " LEFT JOIN `processors-state` ON `processors`.processor_id = `processors-state`.processor_id";
$sql .= " WHERE `device_id` = ?";

$processors = dbFetchRows($sql, array($device['device_id']));

if (count($processors))
{
?>
<div class="well info_box">
    <div id="title"><i class="oicon-processor"></i> Processors</div>
    <div id="content">

<?php
  echo('<table class="table table-condensed-more table-striped">');

  foreach ($processors as $proc)
  {
    $text_descr = rewrite_entity_descr($proc['processor_descr']);

    # disable short hrDeviceDescr. need to make this prettier.
    #$text_descr = short_hrDeviceDescr($proc['processor_descr']);
    $percent = $proc['processor_usage'];
    $background = get_percentage_colours($percent);
    $graph_colour = str_replace("#", "", $row_colour);

    $graph_array           = array();
    $graph_array['height'] = "100";
    $graph_array['width']  = "210";
    $graph_array['to']     = $config['time']['now'];
    $graph_array['id']     = $proc['processor_id'];
    $graph_array['type']   = $graph_type;
    $graph_array['from']   = $config['time']['day'];
    $graph_array['legend'] = "no";

    $link_array = $graph_array;
    $link_array['page'] = "graphs";
    unset($link_array['height'], $link_array['width'], $link_array['legend']);
    $link = generate_url($link_array);

    $overlib_content = generate_overlib_content($graph_array, $device['hostname'] . " - " . $text_descr);

    $graph_array['width'] = 80; $graph_array['height'] = 20; $graph_array['bg'] = 'ffffff00'; # the 00 at the end makes the area transparent.

    $minigraph =  generate_graph_tag($graph_array);

    echo('<tr>
           <td><span class="tablehead">'.overlib_link($link, $text_descr, $overlib_content).'</span></td>
           <td width=90>'.overlib_link($link, $minigraph, $overlib_content).'</td>
           <td width=200>'.overlib_link($link, print_percentage_bar (200, 20, $percent, NULL, "ffffff", $background['left'], $percent . "%", "ffffff", $background['right']), $overlib_content).'
           </a></td>
         </tr>');
  }

  echo("</table>");
  echo("</div></div>");
}

?>
