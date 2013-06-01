<?php

if (!empty($agent_data['app']['memcached']))
{

  foreach ($agent_data['app']['memcached'] as $memcached_host => $memcached_data)
  {

    list ($accepting_conns, $auth_cmds, $auth_errors, $bytes, $bytes_read, $bytes_written, $cas_badval, $cas_hits, $cas_misses, $cmd_flush, $cmd_get, $cmd_set, $conn_yields, $connection_structures, $curr_connections, $curr_items, $decr_hits, $decr_misses, $delete_hits, $delete_misses, $evictions, $get_hits, $get_misses, $incr_hits, $incr_misses, $limit_maxbytes, $listen_disabled_num, $pid, $pointer_size, $rusage_system, $rusage_user, $threads, $time, $total_connections, $total_items, $uptime, $version) = explode("\n", $memcached_data);

    $app_id = dbFetchCell("SELECT app_id FROM `applications` WHERE `device_id` = ? AND `app_instance` = ?", array($device['device_id'], $memcached_host));

    $rrd_filename  = $config['rrd_dir'] . "/" . $device['hostname'] . "/app-memcached-".$app_id.".rrd";

    echo("memcached(".$memcached_host.") ");

    if (!is_file($rrd_filename)) {
      rrdtool_create ($rrd_filename, "--step 300 \
        DS:uptime:GAUGE:600:0:125000000000 \
        DS:threads:GAUGE:600:0:125000000000 \
        DS:rusage_user:GAUGE:600:0:125000000000 \
        DS:rusage_system:GAUGE:600:0:125000000000 \
        DS:curr_items:GAUGE:600:0:125000000000 \
        DS:total_items:DERIVE:600:0:125000000000 \
        DS:limit_maxbytes:GAUGE:600:0:U \
        DS:curr_connections:GAUGE:600:0:125000000000 \
        DS:total_connections:DERIVE:600:0:125000000000 \
        DS:conn_structures:GAUGE:600:0:125000000000 \
        DS:bytes:GAUGE:600:0:U \
        DS:cmd_get:DERIVE:600:0:125000000000 \
        DS:cmd_set:DERIVE:600:0:125000000000 \
	DS:cmd_flush:DERIVE:600:0:12500000000 \
        DS:get_hits:DERIVE:600:0:125000000000 \
        DS:get_misses:DERIVE:600:0:125000000000 \
        DS:evictions:DERIVE:600:0:125000000000 \
        DS:bytes_read:DERIVE:600:0:125000000000 \
        DS:bytes_written:DERIVE:600:0:125000000000 \
        ".$config['rrd_rra']);
    }

    rrdtool_update($rrd_filename, "N:$uptime:$threads:$rusage_user:$rusage_system:$curr_items:$total_items:$limit_maxbytes:$curr_connections:$total_connections:$connection_structures:$bytes:$cmd_get:$cmd_set:$cmd_flush:$get_hits:$get_misses:$evictions:$bytes_read:$bytes_written");

  }

}
?>
