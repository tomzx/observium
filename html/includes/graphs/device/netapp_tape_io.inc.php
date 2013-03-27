<?php

$rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/netapp_stats.rrd";

$format = "bytes";
$ds_in = "tape_rd";
$ds_out = "tape_wr";

include("includes/graphs/generic_data.inc.php");

?>
