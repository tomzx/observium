<?php

$rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/". safename("cbgp-" . $data['bgpPeerRemoteAddr'] . ".ipv4.vpn.rrd");

include("includes/graphs/bgp/prefixes.inc.php");

?>
