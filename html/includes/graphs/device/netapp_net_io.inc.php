<?php

$rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/netapp_stats.rrd";

$ds_in = "net_rx";
$ds_out = "net_tx";

include("includes/graphs/generic_data.inc.php");

?>
