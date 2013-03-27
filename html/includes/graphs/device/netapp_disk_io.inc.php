<?php

$rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/netapp_stats.rrd";

$ds_in = "disk_rd";
$ds_out = "disk_wr";
$format = "octets";

include("includes/graphs/generic_data.inc.php");

?>
