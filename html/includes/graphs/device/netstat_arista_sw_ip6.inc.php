<?php

$rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/arista-netstats-sw-ip6.rrd";
$ipv = "v6";

include("netstat_arista_sw_ip.inc.php");

?>
