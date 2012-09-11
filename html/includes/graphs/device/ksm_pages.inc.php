<?php

$rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/ksm-pages.rrd";

$stats = array('shared', 'sharing', 'unshared');

$i=0;
foreach ($stats as $stat)
{
  $i++;
  $rrd_list[$i]['filename'] = $rrd_filename;
  $rrd_list[$i]['descr'] = "Pages " . ucfirst($stat);
  $rrd_list[$i]['ds'] = "pages" . ucfirst($stat);
}

$colours='mixed';

$nototal = 1;
$simple_rrd = 1;

include("includes/graphs/generic_multi_line.inc.php");

?>
