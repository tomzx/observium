<?php

include("includes/graphs/common.inc.php");

$rrd_options .= " -l 0 -E ";

$pagecount_rrd  = $config['rrd_dir'] . "/".$device['hostname']."/pagecount.rrd";

if (file_exists($pagecount_rrd))
{
  $rrd_options .= " COMMENT:'                                      Cur\\n'";
  $rrd_options .= " DEF:pagecount=".$pagecount_rrd.":pagecount:AVERAGE ";
  $rrd_options .= " LINE1:pagecount#CC0000:'Pages printed                   ' ";
  $rrd_options .= " GPRINT:pagecount:LAST:%3.0lf\\\l";
}

?>