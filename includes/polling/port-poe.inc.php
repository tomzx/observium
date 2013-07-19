<?php

$cpe_oids = array('cpeExtPsePortEnable', 'cpeExtPsePortDiscoverMode', 'cpeExtPsePortDeviceDetected', 'cpeExtPsePortIeeePd',
'cpeExtPsePortAdditionalStatus', 'cpeExtPsePortPwrMax', 'cpeExtPsePortPwrAllocated', 'cpeExtPsePortPwrAvailable', 'cpeExtPsePortPwrConsumption',
'cpeExtPsePortMaxPwrDrawn', 'cpeExtPsePortEntPhyIndex', 'cpeExtPsePortEntPhyIndex', 'cpeExtPsePortPolicingCapable', 'cpeExtPsePortPolicingEnable',
'cpeExtPsePortPolicingAction', 'cpeExtPsePortPwrManAlloc');

$peth_oids = array('pethPsePortAdminEnable', 'pethPsePortPowerPairsControlAbility', 'pethPsePortPowerPairs', 'pethPsePortDetectionStatus',
'pethPsePortPowerPriority', 'pethPsePortMPSAbsentCounter', 'pethPsePortType', 'pethPsePortPowerClassifications', 'pethPsePortInvalidSignatureCounter',
'pethPsePortPowerDeniedCounter', 'pethPsePortOverLoadCounter', 'pethPsePortShortCounter', 'pethMainPseConsumptionPower');

$port_stats = snmpwalk_cache_oid($device, "pethPsePortEntry", $port_stats, "POWER-ETHERNET-MIB");
$port_stats = snmpwalk_cache_oid($device, "cpeExtPsePortEntry", $port_stats, "CISCO-POWER-ETHERNET-EXT-MIB");

if ($port_stats[$port['ifIndex']] && $port['ifType'] == "ethernetCsmacd" && isset($port_stats[$port['ifIndex']]['dot3StatsIndex']))
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
