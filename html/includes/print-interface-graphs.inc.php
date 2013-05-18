<?php

global $config;

$graph_array['to']     = $config['time']['now'];
$graph_array['id']     = $port['port_id'];
$graph_array['type']   = $graph_type;

print_graph_row($graph_array);

?>
