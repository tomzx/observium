<?php

include("includes/graphs/common.inc.php");

$scale_min    = 0;
$colours      = "mixed";
$nototal      = 1;
$unit_text    = "Queries/sec";
$rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/app-unbound-".$app['app_id']."-queries.rrd";

$i            = 0;
$array        = array();

$dns_opcode = array('QUERY', 'IQUERY', 'STATUS', 'NOTIFY', 'UPDATE');
  
$colours = $config['graph_colours']['mixed']; # needs moar colours!

foreach ($dns_opcode as $opcode)
{
  $array["opcode$opcode"] = array('descr' => strtoupper($opcode), 'colour' => $colours[(count($array) % count($colours))]);
}

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
