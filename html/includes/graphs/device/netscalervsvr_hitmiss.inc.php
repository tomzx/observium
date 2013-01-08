<?php

$ds_in  = "TotHits";
$ds_out = "TotMiss";

$unit_text = "Hit/Miss";

include("netscalervsvr.inc.php");

$units ='pps';
$total_units ='Pkts';
$multiplier = 1;
$colours_in  = 'greens';
$colours_out = 'oranges';

#$nototal = 1;

$graph_title .= "::packets";

include("includes/graphs/generic_multi_seperated.inc.php");


?>
