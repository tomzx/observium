<?php

$ds_in  = "CurClntConnections";
$ds_out = "CurSrvrConnections";

$unit_text = "Connections";

include("netscalervsvr.inc.php");

$units ='pps';
$total_units ='Pkts';
$multiplier = 1;
$colours_in ='purples';
$colours_out = 'oranges';

#$nototal = 1;

$graph_title .= "::connections";

include("includes/graphs/generic_multi_seperated.inc.php");

?>
