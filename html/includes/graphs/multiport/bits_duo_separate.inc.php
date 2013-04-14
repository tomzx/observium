<?php

$i = 0;

$groups[0]['ports']       = explode(",", $vars['id']);
$groups[0]['colours_in']  = 'oranges';
$groups[0]['colours_out'] = 'red2';


$groups[1]['ports']       = explode(",", $vars['idb']);
$groups[1]['colours_in']  = 'greens';
$groups[1]['colours_out'] = 'blues';


foreach($groups as $group_id => $group)
{
  $iter=0;
  foreach ($group['ports'] as $port_id)
  {
    $port = dbFetchRow("SELECT * FROM `ports` AS I, devices as D WHERE I.port_id = ? AND I.device_id = D.device_id", array($port_id));
    $rrdfile = get_port_rrdfilename($port, $port);
    if (is_file($rrdfile))
    {
      $port = humanize_port($port);
      $rrd_list[$i]['filename'] = $rrdfile;
      $rrd_list[$i]['descr'] = $port['hostname'] . " " . $port['ifDescr'];
      $rrd_list[$i]['descr_in'] = $port['hostname'];
      $rrd_list[$i]['descr_out'] = makeshortif($port['label']);

      if (!$config['graph_colours'][$group['colours_in']][$iter] || !$config['graph_colours'][$group['colours_out']][$iter]) { $iter = 0; }
      $rrd_list[$i]['colour_in']  = $config['graph_colours'][$group['colours_in']][$iter];
      $rrd_list[$i]['colour_out'] = $config['graph_colours'][$group['colours_out']][$iter];
      $i++; $iter++;

    }
  }
}

#echo("<pre>");
#print_r($rrd_list);
#echo("</pre>");

$units = 'bps';
$total_units='B';
$multiplier = "8";

#$nototal = 1;

$ds_in  = "INOCTETS";
$ds_out = "OUTOCTETS";

include("includes/graphs/generic_multi_bits_separated.inc.php");

?>
