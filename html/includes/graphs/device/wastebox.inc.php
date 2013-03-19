<?php

include("includes/graphs/common.inc.php");

$rrd_options .= " -l 0 -E ";

$waste_rrd  = $config['rrd_dir'] . "/".$device['hostname']."/wastebox.rrd";

if (file_exists($waste_rrd))
{
  $rrd_options .= " COMMENT:'                           Cur   Min  Max\\n'";

  $rrd_options .= " DEF:level=".$waste_rrd.":level:AVERAGE ";
  $rrd_options .= " LINE1:level#CC0000:'Waste toner box      ' ";
  $rrd_options .= " GPRINT:level:LAST:%3.0lf%% ";
  $rrd_options .= " GPRINT:level:MIN:%3.0lf%% ";
  $rrd_options .= " GPRINT:level:MAX:%3.0lf%%\\\l ";
}

?>