<?php

// Sections are printed in the order they exist in $config['graph_sections']
// Graphs are printed in the order they exist in $config['graph_types']

$link_array = array('page'    => 'device',
                    'device'  => $device['device_id'],
                    'tab' => 'graphs');

foreach (dbFetchRows("SELECT * FROM device_graphs WHERE device_id = ? ORDER BY graph", array($device['device_id'])) as $graph)
{
  $section = $config['graph_types']['device'][$graph['graph']]['section'];
  $graph_enable[$section][$graph['graph']] = $graph['graph'];
}

$navbar['brand'] = "Graphs";
$navbar['class'] = "navbar-narrow";

foreach ($graph_enable as $section => $nothing)
{
  if (isset($graph_enable) && is_array($graph_enable[$section]))
  {
    $type = strtolower($section);
    if (!$vars['group']) { $vars['group'] = $type; }
    if ($vars['group'] == $type) { $navbar['options'][$section]['class'] = "active"; }
    $navbar['options'][$section]['url'] = generate_url(array('page' => 'device', 'device' => $device['device_id'], 'tab' => 'graphs', 'group' => $type));
    $navbar['options'][$section]['text'] = ucwords($type);
  }
}

print_navbar($navbar);

$graph_enable = $graph_enable[$vars['group']];

echo('<table class="table table-condensed table-striped table-hover">');

foreach ($graph_enable as $graph => $entry)
{
  $graph_array = array();
  if ($graph_enable[$graph])
  {
    $graph_title = $config['graph_types']['device'][$graph]['descr'];
    $graph_array['type'] = "device_" . $graph;

    include("includes/print-device-graph.php");
  }
}

echo('</table>');

$pagetitle[] = "Graphs";

?>
