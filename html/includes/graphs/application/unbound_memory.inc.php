<?php

include("includes/graphs/common.inc.php");

$scale_min    = 0;
$colours      = "blue";
$nototal      = 1;
$unit_text    = "Bytes";
$rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/app-unbound-".$app['app_id']."-memory.rrd";
$array        = array(
                      'memCacheRRset' => array('descr' => 'RRset cache', 'colour' => '003366FF'),
                      'memCacheMessage' => array('descr' => 'Message cache', 'colour' => '336699FF'),
                      'memModIterator' => array('descr' => 'Iterator module', 'colour' => '6699CCFF'),
                      'memModValidator' => array('descr' => 'Validator module', 'colour' => '99CCEEFF'),
                     );

#DS:memTotal:DERIVE:600:0:125000000000 \

$i            = 0;

if (is_file($rrd_filename))
{
  foreach ($array as $ds => $vars)
  {
    $rrd_list[$i]['filename'] = $rrd_filename;
    $rrd_list[$i]['descr']    = $vars['descr'];
    $rrd_list[$i]['ds']       = $ds;
    $rrd_list[$i]['colour']   = $vars['colour'];
    $i++;
  }
} else {
  echo("file missing: $file");
}

include("includes/graphs/generic_multi_simplex_seperated.inc.php");

?>
