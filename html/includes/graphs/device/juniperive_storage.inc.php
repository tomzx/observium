<?php

$rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/" . safename("juniperive_storage.rrd");

$rrd_list[0]['filename'] = $rrd_filename;
$rrd_list[0]['descr'] = "Disk";
$rrd_list[0]['ds'] = "diskpercent";

$rrd_list[1]['filename'] = $rrd_filename;
$rrd_list[1]['descr'] = "Log";
$rrd_list[1]['ds'] = "logpercent";

if ($_GET['debug']) { print_r($rrd_list); }

$colours = "juniperive";

$unit_text = "Storage %";
$units = '%';
$total_units = '%';

$scale_min = "0";
$scale_max = "100";
$nototal = 1;

include("includes/graphs/generic_multi_line.inc.php");

?>
