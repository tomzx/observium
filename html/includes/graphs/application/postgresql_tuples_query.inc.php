<?php

include("includes/graphs/common.inc.php");

$colours      = "mixed";
$nototal      = (($width<224) ? 1 : 0);
$unit_text    = "Tuples";
$rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/app-postgresql-".$app['app_id'].".rrd";

$array = array(
               'tup_inserted' => array('descr' => 'Insert'),
               'tup_updated' => array('descr' => 'Update'),
               'tup_deleted' => array('descr' => 'Delete')
               );
$i = 0;

if (is_file($rrd_filename))
{
    foreach ($array as $ds => $data)
    {
        $rrd_list[$i]['filename']        = $rrd_filename;
        $rrd_list[$i]['descr']        = $data['descr'];
        $rrd_list[$i]['ds']                = $ds;
        $rrd_list[$i]['colour']        = $config['graph_colours'][$colours][$i];
        $i++;
    }
} else {
    echo("file missing: $file");
}

include("includes/graphs/generic_multi_line.inc.php");

?>
