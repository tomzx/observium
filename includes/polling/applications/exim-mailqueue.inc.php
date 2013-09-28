<?php
// Correct output of the agent script should look like this:
//<<<exim-mailqueue>>>
//frozen:173
//bounces:1052
//total:2496
//active:2323

if (!empty($agent_data['app']['exim-mailqueue']))
{
    $cnt = $agent_data['app']['exim-mailqueue'];

    foreach (explode("\n",$cnt) as $line) {
	list($item,$value) = explode(":",$line,2);
	$cnt_data[trim($item)] = trim($value);
    }
    $rrd_filename = $config['rrd_dir']."/".$device['hostname']."/app-exim-mailqueue-".$app['app_id'].".rrd";

    if (!is_file($rrd_filename)) {
	// mailqueue count
	rrdtool_create($rrd_filename, " DS:frozen:GAUGE:600:0:1000000\
		DS:bounces:GAUGE:600:0:1000000\
		DS:total:GAUGE:600:0:1000000\
		DS:active:GAUGE:600:0:1000000");
    }

    rrdtool_update($rrd_filename, "N:".$cnt_data['frozen'].":".$cnt_data['bounces'].":".$cnt_data['total'].":".$cnt_data['active']);

    unset($rrd_data);
    unset($cnt);
    unset($cnt_data);
}

?>
