<?php

include("includes/graphs/common.inc.php");

$scale_min    = 0;
$colours      = "mixed";
$nototal      = (($width<224) ? 1 : 0);
$unit_text    = "Packets/sec";
$rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/app-powerdns-recursor-".$app['app_id'].".rrd";
$array        = array(
                      'questions'        => array('descr' => 'Questions', 'colour' => '0000FFFF'),
                      'answers_nxdomain' => array('descr' => 'NXDOMAIN Answers', 'colour' => '00FF00FF'),
                      'answers_noerror'  => array('descr' => 'NOERROR Answers', 'colour' => 'FFA500FF'),
                      'answers_servfail' => array('descr' => 'SERVFAIL Answers', 'colour' => 'FF0000FF'),
                     );

$i            = 0;

if (is_file($rrd_filename))
{
  foreach ($array as $ds => $vars)
  {
    $rrd_list[$i]['filename'] = $rrd_filename;
    $rrd_list[$i]['descr'] = $vars['descr'];
    $rrd_list[$i]['ds'] = $ds;
    $rrd_list[$i]['colour'] = $vars['colour'];
    $i++;
  }
} else {
  echo("file missing: $file");
}

include("includes/graphs/generic_multi_simplex_seperated.inc.php");

?>
