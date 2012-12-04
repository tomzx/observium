<?php

$rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/" . safename("juniperive_users.rrd");

$rrd_list[0]['filename'] = $rrd_filename;
$rrd_list[0]['descr'] = "Cluster";
$rrd_list[0]['ds'] = "clusterusers";

$rrd_list[1]['filename'] = $rrd_filename;
$rrd_list[1]['descr'] = "Local";
$rrd_list[1]['ds'] = "iveusers";

if ($_GET['debug']) { print_r($rrd_list); }

$colours = "juniperive";
$nototal = 1;
$unit_text = "Users";
$scale_min = "0";

include("includes/graphs/generic_multi_line.inc.php");

?>
