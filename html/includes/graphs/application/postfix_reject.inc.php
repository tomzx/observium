<?php

include("includes/graphs/common.inc.php");

$scale_min    = 0;
$colours      = "mixed";
$nototal      = (($width < 550) ? 1 : 0);
$unit_text    = "Messages/minute";
$rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/app-postfix-mailgraph-" . $app['app_id'] . ".rrd";
$array        = array(
                      'rejected' => array('descr' => 'Rejected'),
                      'bounced' => array('descr' => 'Bounced'),
                      'greylisted' => array('descr' => 'Greylisted'),
                      'delayed' => array('descr' => 'Delayed'),
                     );

$i            = 0;
$x            = 0;

if (is_file($rrd_filename))
{
  $max_colours = count($config['graph_colours'][$colours]);
  foreach ($array as $ds => $vars)
  {
    $x = (($x<=$max_colours) ? $x : 0);
    $rrd_list[$i]['filename'] = $rrd_filename;
    $rrd_list[$i]['descr']    = $vars['descr'];
    $rrd_list[$i]['ds']       = $ds;
    $rrd_list[$i]['colour']   = $config['graph_colours'][$colours][$x];
    $i++;
    $x++;
  }
}

include("includes/graphs/minute_multi_line.inc.php");

?>
