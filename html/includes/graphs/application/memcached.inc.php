<?php

$rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/app-memcached-".$app['app_id'].".rrd";
$rrd_filename  = $config['rrd_dir'] . "/" . $device['hostname'] . "/app-memcached-".safename($app['app_instance']).".rrd";


?>
