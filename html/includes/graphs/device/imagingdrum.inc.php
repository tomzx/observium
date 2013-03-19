<?php

include("includes/graphs/common.inc.php");

$rrd_options .= " -l 0 -E ";

$drum_rrd  = $config['rrd_dir'] . "/".$device['hostname']."/drum.rrd";

if (file_exists($drum_rrd))
{
  $rrd_options .= " COMMENT:'                           Cur   Min  Max\\n'";

  $rrd_options .= " DEF:drum=".$drum_rrd.":drum:AVERAGE ";
  $rrd_options .= " LINE1:drum#CC0000:'Imaging Drum         ' ";
  $rrd_options .= " GPRINT:drum:LAST:%3.0lf%% ";
  $rrd_options .= " GPRINT:drum:MIN:%3.0lf%% ";
  $rrd_options .= " GPRINT:drum:MAX:%3.0lf%%\\\l ";
}

?>