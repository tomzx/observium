<?php

$scale_min = 0;
include("includes/graphs/common.inc.php");

$rrd   = $config['rrd_dir'] . "/" . $device['hostname'] . "/perf-poller.rrd";
if (is_file($rrd))
{
  $rrd_filename = $rrd;
}

$ds = "val";
$colour_area = "EEEEEE";
$colour_line = "36393D";
$colour_area_max = "FFEE99";
$unit_text = "Seconds";
$line_text = 'Poller';
include("includes/graphs/generic_simplex.inc.php");

?>
