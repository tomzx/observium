<?php

$rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/netscaler-stats-tcp.rrd";

$ds_in = "CurClientConn";
$ds_out = "CurServerConn";

$in_text = "Client";
$out_text = "Server";

$colour_area_in = "78c7eb";
$colour_line_in = "00519b";

$colour_area_out = "A9E558";
$colour_line_out = "4F8910";

$colour_area_in_max  = "AAAAAA";
$colour_area_out_max = "AAAAAA";

$graph_max = 1;
$unit_text = "Connections";

include("includes/graphs/generic_duplex.inc.php");

?>
