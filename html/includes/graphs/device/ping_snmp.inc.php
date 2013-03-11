<?php

$scale_min = 0;
include('includes/graphs/common.inc.php');

$rrd   = $config['rrd_dir'] . '/' . $device['hostname'] . '/ping_snmp.rrd';
if (is_file($rrd))
{
  $rrd_filename = $rrd;
}

$ds = 'ping_snmp';
$colour_area = '00000000';
$colour_line = '0000CC';
$colour_area_max = 'FFEE99';
$unit_text = 'Milliseconds';
$line_text = 'SNMP';
include('includes/graphs/generic_simplex.inc.php');

?>
