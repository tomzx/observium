<?php

if ($port_stats[$port['ifIndex']] && $port['ifType'] == "ethernetCsmacd"
   && isset($port_stats[$port['ifIndex']]['dot3StatsIndex']))
{ // Check to make sure Port data is cached.

  $this_port = &$port_stats[$port[ifIndex]];

  // TODO: remove $old_rrdfile?
  $old_rrdfile = $config['rrd_dir'] . "/" . $device['hostname'] . "/" . safename("etherlike-".$port['ifIndex'].".rrd");
  $rrdfile = get_port_rrdfilename($device, $port, "dot3");

  $rrd_create = $config['rrd_rra'];

  if (!file_exists($rrdfile))
  {
    if (file_exists($old_rrdfile))
    {
      rename($old_rrdfile,$rrd_file);
    }
    else
    {
      foreach ($etherlike_oids as $oid)
      {
        $oid = truncate(str_replace("dot3Stats", "", $oid), 19, '');
        $rrd_create .= " DS:$oid:COUNTER:600:U:100000000000";
      }
      rrdtool_create($rrdfile, $rrd_create);
    }
  }

  if($config['statsd']['enable'] == TRUE)
  {
    foreach ($etherlike_oids as $oid)
    {
      // Update StatsD/Carbon
      StatsD::gauge(str_replace(".", "_", $device['hostname']).'.'.'port'.'.'.$port['ifIndex'].'.'.$oid, $this_port[$oid]);
    }
  }

  $rrdupdate = "N";
  foreach ($etherlike_oids as $oid)
  {
    $data = $this_port[$oid] + 0;
    $rrdupdate .= ":$data";
  }
  rrdtool_update($rrdfile, $rrdupdate);

  echo("EtherLike ");
}

?>
