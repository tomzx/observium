<?php

if (!empty($agent_data['app']['shoutcast'])) {
  // Polls shoutcast statistics from agent script
  $shoutcast = $agent_data['app']['shoutcast'];
} else {
  // Polls shoutcast statistics from script via SNMP
  $options   = "-O qv";
  $oid       = "nsExtendOutputFull.9.115.104.111.117.116.99.97.115.116";
  $shoutcast = snmp_get($device, $oid, $options);
  echo(" shoutcast");
}

$servers = explode("\n", $shoutcast);

foreach ($servers as $item=>$server)
{
  $server = trim($server);

  if (!empty($server))
  {
    $data              = explode(";", $server);
    list($host, $port) = explode(":", $data['0'], 2);
    $bitrate           = $data['1'];
    $traf_in           = $data['2'];
    $traf_out          = $data['3'];
    $current           = $data['4'];
    $status            = $data['5'];
    $peak              = $data['6'];
    $max               = $data['7'];
    $unique            = $data['8'];
    $rrdfile           = $config['rrd_dir'] . "/" . $device['hostname'] . "/app-shoutcast-".$app['app_id']."-".$host."_".$port.".rrd";

    if (!is_file($rrdfile))
    {
      rrdtool_create($rrdfile, " \
                DS:bitrate:GAUGE:600:0:125000000000 \
                DS:traf_in:GAUGE:600:0:125000000000 \
                DS:traf_out:GAUGE:600:0:125000000000 \
                DS:current:GAUGE:600:0:125000000000 \
                DS:status:GAUGE:600:0:125000000000 \
                DS:peak:GAUGE:600:0:125000000000 \
                DS:max:GAUGE:600:0:125000000000 \
                DS:unique:GAUGE:600:0:125000000000 ");
    }

    rrdtool_update($rrdfile,  "N:$bitrate:$traf_in:$traf_out:$current:$status:$peak:$max:$unique");
  }
}

?>
