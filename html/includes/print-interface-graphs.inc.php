<?php

global $config;

$graph_array['to']     = $config['time']['now'];
$graph_array['id']     = $port['port_id'];
$graph_array['type']   = $graph_type;

include("includes/print-graphrow.inc.php");

?>
