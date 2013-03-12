<?php

if (!empty($agent_data['app']['postgresql']))
{
    $pgsql = $agent_data['app']['ntpd'];

    foreach (explode("\n",$pgsql) as $line) {
        list($item,$value) = explode(":",$line,2);
        $pgsql_data[trim($item)] = trim($value);
    }
    $rrd_filename = $config['rrd_dir']."/".$device['hostname']."/app-postgresql-".$pgsql_data['version']."-".$app['app_id'].".rrd";

    // if $pgsql_data['version']

    if (!is_file($rrd_filename))
    {
        // version, ccount, tDbs, tUsr, tHst, idle, select, update, delete, other
        rrdtool_create($rrd_filename, "--step 300 \
                                        DS:$pgsql_data['cCount']:GAUGE:600:0:1000000 \
                                        DS:$pgsql_data['tDbs']:GAUGE:600:0:1000000 \
                                        DS:$pgsql_data['tUsr']:GAUGE:600:0:1000000 \
                                        DS:$pgsql_data['tHst']:GAUGE:600:0:1000000 \
                                        DS:$pgsql_data['idle']:GAUGE:600:0:1000000 \
                                        DS:$pgsql_data['select']:GAUGE:600:0:1000000 \
                                        DS:$pgsql_data['update']:GAUGE:600:0:1000000 \
                                        DS:$pgsql_data['delete']:GAUGE:600:0:1000000 \
                                        DS:$pgsql_data['other']:GAUGE:600:0:1000000 ".$config['rrd_rra']);
    }

    rrdtool_update($rrd_filename, "N::$pgsql_data['cCount'],$pgsql_data['tDbs'],$pgsql_data['tUsr'],$pgsql_data['tHst'],$pgsql_data['idle'],$pgsql_data['select'],$pgsql_data['update'],$pgsql_data['delete'],$pgsql_data['other']");
}

?>
