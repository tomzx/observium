<?php

$ksm = $agent_data['ksm'];
unset($agent_data['ksm']);

foreach (explode("\n",$ksm) as $line)
{
  list($field,$contents) = explode("=",$line,2);
  $agent_data['ksm'][$field] = trim($contents);
}

$rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/ksm-pages.rrd";

if (!is_file($rrd_filename))
{
  rrdtool_create($rrd_filename, "--step 300 \
    DS:pagesShared:GAUGE:600:0:125000000000 \
    DS:pagesSharing:GAUGE:600:0:125000000000 \
    DS:pagesUnshared:GAUGE:600:0:125000000000 ".$config['rrd_rra']);
}

rrdtool_update($rrd_filename, "N:" . $agent_data['ksm']['pages_shared'] . ":" . $agent_data['ksm']['pages_sharing'] . ":" . $agent_data['ksm']['pages_unshared']);

$graphs['ksm_pages'] = TRUE;

unset($ksm);

?>