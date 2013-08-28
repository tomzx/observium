<?php

if (isset($port_stats[$port['ifIndex']]) && $port['ifType'] == "ethernetCsmacd")
{ // Check to make sure Port data is cached.

    $this_port = &$port_stats[$port['ifIndex']];

    $rrdfile = get_port_rrdfilename($device, $port, "poe");

    if (!file_exists($rrdfile))
    {
      $rrd_create .= $config['rrd_rra'];

      // FIXME CISCOSPECIFIC
      $rrd_create .= " DS:PortPwrAllocated:GAUGE:600:0:U";
      $rrd_create .= " DS:PortPwrAvailable:GAUGE:600:0:U";
      $rrd_create .= " DS:PortConsumption:DERIVE:600:0:U";
      $rrd_create .= " DS:PortMaxPwrDrawn:GAUGE:600:0:U ";

      rrdtool_create($rrdfile, $rrd_create);
    }

    if($config['statsd']['enable'] == TRUE)
    {
      foreach (array('cpeExtPsePortPwrAllocated', 'cpeExtPsePortPwrAvailable', 'cpeExtPsePortPwrConsumption', 'cpeExtPsePortMaxPwrDrawn') as $oid)
      {
        // Update StatsD/Carbon
        StatsD::gauge(str_replace(".", "_", $device['hostname']).'.'.'port'.'.'.$port['ifIndex'].'.'.$oid, $this_port[$oid]);
      }
    }

    $upd = "$polled:".$port['cpeExtPsePortPwrAllocated'].":".$port['cpeExtPsePortPwrAvailable'].":".$port['cpeExtPsePortPwrConsumption'].":".$port['cpeExtPsePortMaxPwrDrawn'];
    $ret = rrdtool_update("$rrdfile", $upd);

    echo("PoE ");
  }

?>
