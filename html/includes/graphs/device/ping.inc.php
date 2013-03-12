<?php

$scale_min = 0;
include('includes/graphs/common.inc.php');

$rrd   = $config['rrd_dir'] . '/' . $device['hostname'] . '/ping.rrd';
if (is_file($rrd))
{
  $rrd_filename = $rrd;
}

$ds = 'ping';
$colour_area = 'FFEEEE';
$colour_line = 'CC0000';
$colour_area_max = 'FFEE99';
$unit_text = 'Milliseconds';
$line_text = 'Ping';
include('includes/graphs/generic_simplex.inc.php');

?>
