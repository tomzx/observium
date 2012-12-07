<?php

if (!empty($agent_data['app']['postgresql']))
{
    $pgsql = $agent_data['app']['postgresql'];
    
    foreach (explode("\n",$pgsql) as $line) {
	list($item,$value) = explode(":",$line,2);
	$pgsql_data[trim($item)] = trim($value);
    }
    $rrd_filename = $config['rrd_dir']."/".$device['hostname']."/app-postgresql-".$app['app_id'].".rrd";

    // there are differences between stats in postgresql 8.x and 9.x
    // if $pgsql_data['version']

    if (!is_file($rrd_filename)) {
	// version, ccount, tDbs, tUsr, tHst, idle, select, update, delete, other	
	rrdtool_create($rrd_filename, "--step 300 \
					DS:cCount:GAUGE:600:0:1000000 \
					DS:tDbs:GAUGE:600:0:1000000 \
					DS:tUsr:GAUGE:600:0:1000000 \
					DS:tHst:GAUGE:600:0:1000000 \
					DS:idle:GAUGE:600:0:1000000 \
					DS:select:GAUGE:600:0:1000000 \
					DS:update:GAUGE:600:0:1000000 \
					DS:delete:GAUGE:600:0:1000000 \
					DS:other:GAUGE:600:0:1000000 \
					DS:xact_commit:COUNTER:600:0:100000000000 \
					DS:xact_rollback:COUNTER:600:0:100000000000 \
					DS:blks_read:COUNTER:600:0:1000000000000000 \
					DS:blks_hit:COUNTER:600:0:1000000000000000 \
					DS:tup_returned:COUNTER:600:0:100000000000000 \
					DS:tup_fetched:COUNTER:600:0:100000000000000 \
					DS:tup_inserted:COUNTER:600:0:100000000000000 \
					DS:tup_updated:COUNTER:600:0:100000000000000 \
					DS:tup_deleted:COUNTER:600:0:100000000000000".$config['rrd_rra']);
    }
    
    rrdtool_update($rrd_filename, "N:".$pgsql_data['cCount'].":".$pgsql_data['tDbs'].":".
		   $pgsql_data['tUsr'].":".$pgsql_data['tHst'].":".$pgsql_data['idle'].":".
		   $pgsql_data['select'].":".$pgsql_data['update'].":".$pgsql_data['delete'].":".
		   $pgsql_data['other'].":".$pgsql_data['xact_commit'].":".$pgsql_data['xact_rollback'].":".
		   $pgsql_data['blks_read'].":".$pgsql_data['blks_hit'].":".
		   $pgsql_data['tup_returned'].":".$pgsql_data['tup_fetched'].":".$pgsql_data['tup_inserted'].":".
		   $pgsql_data['tup_updated'].":".$pgsql_data['tup_deleted']);
    
    unset($rrd_data);
    unset($pgsql);
    unset($pgsql_data);
}

?>
