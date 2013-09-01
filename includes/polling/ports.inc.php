<?php

// Build SNMP Cache Array

// IF-MIB OIDs that go into the ports table

$data_oids = array('ifName','ifDescr','ifAlias', 'ifAdminStatus', 'ifOperStatus', 'ifMtu', 'ifSpeed', 'ifHighSpeed', 'ifType', 'ifPhysAddress',
                   'ifPromiscuousMode','ifConnectorPresent','ifDuplex', 'ifTrunk', 'ifVlan');

// IF-MIB statistics OIDs that go into RRD

$stat_oids = array('ifInErrors', 'ifOutErrors', 'ifInUcastPkts', 'ifOutUcastPkts', 'ifInNUcastPkts', 'ifOutNUcastPkts',
                   'ifHCInMulticastPkts', 'ifHCInBroadcastPkts', 'ifHCOutMulticastPkts', 'ifHCOutBroadcastPkts',
                   'ifInOctets', 'ifOutOctets', 'ifHCInOctets', 'ifHCOutOctets', 'ifInDiscards', 'ifOutDiscards', 'ifInUnknownProtos',
                   'ifInBroadcastPkts', 'ifOutBroadcastPkts', 'ifInMulticastPkts', 'ifOutMulticastPkts');

// Subset of IF-MIB statistics OIDs that we put into the state table

$stat_oids_db = array('ifInOctets', 'ifOutOctets', 'ifInErrors', 'ifOutErrors', 'ifInUcastPkts', 'ifOutUcastPkts'); // From above for DB

// ETHERLIKE-MIB extended error stats oids

$etherlike_oids = array('dot3StatsAlignmentErrors', 'dot3StatsFCSErrors', 'dot3StatsSingleCollisionFrames', 'dot3StatsMultipleCollisionFrames',
                        'dot3StatsSQETestErrors', 'dot3StatsDeferredTransmissions', 'dot3StatsLateCollisions', 'dot3StatsExcessiveCollisions',
                        'dot3StatsInternalMacTransmitErrors', 'dot3StatsCarrierSenseErrors', 'dot3StatsFrameTooLongs', 'dot3StatsInternalMacReceiveErrors',
                        'dot3StatsSymbolErrors');

// Cisco old locIf OIDs. Currently unused.

$cisco_oids = array('locIfHardType', 'locIfInRunts', 'locIfInGiants', 'locIfInCRC', 'locIfInFrame', 'locIfInOverrun', 'locIfInIgnored', 'locIfInAbort',
                    'locIfCollisions', 'locIfInputQueueDrops', 'locIfOutputQueueDrops');

// PAgP OIDs

$pagp_oids = array('pagpOperationMode', 'pagpPortState', 'pagpPartnerDeviceId', 'pagpPartnerLearnMethod', 'pagpPartnerIfIndex', 'pagpPartnerGroupIfIndex',
                   'pagpPartnerDeviceName', 'pagpEthcOperationMode', 'pagpDeviceId', 'pagpGroupIfIndex');

// PoE OIDs

$cpe_oids = array('cpeExtPsePortEnable', 'cpeExtPsePortDiscoverMode', 'cpeExtPsePortDeviceDetected', 'cpeExtPsePortIeeePd',
  'cpeExtPsePortAdditionalStatus', 'cpeExtPsePortPwrMax', 'cpeExtPsePortPwrAllocated', 'cpeExtPsePortPwrAvailable', 'cpeExtPsePortPwrConsumption',
  'cpeExtPsePortMaxPwrDrawn', 'cpeExtPsePortEntPhyIndex', 'cpeExtPsePortEntPhyIndex', 'cpeExtPsePortPolicingCapable', 'cpeExtPsePortPolicingEnable',
  'cpeExtPsePortPolicingAction', 'cpeExtPsePortPwrManAlloc');

$peth_oids = array('pethPsePortAdminEnable', 'pethPsePortPowerPairsControlAbility', 'pethPsePortPowerPairs', 'pethPsePortDetectionStatus',
  'pethPsePortPowerPriority', 'pethPsePortMPSAbsentCounter', 'pethPsePortType', 'pethPsePortPowerClassifications', 'pethPsePortInvalidSignatureCounter',
  'pethPsePortPowerDeniedCounter', 'pethPsePortOverLoadCounter', 'pethPsePortShortCounter', 'pethMainPseConsumptionPower');


$ifmib_oids = array_merge($data_oids, $stat_oids);
$ifmib_oids = array('ifEntry', 'ifXEntry');

echo("Caching Oids: ");
foreach ($ifmib_oids as $oid) { echo("$oid "); $port_stats = snmpwalk_cache_oid($device, $oid, $port_stats, "IF-MIB"); }

// Check if we're dealing with a retarded ass-backwards OS which feels the need to dump standardised variables in its own MIB, apparently just for shits.
if($device['os'] == "netapp")
{
  // Add NetApp's own table so we can get 64-bit values. They are checked later on.
  $port_stats = snmpwalk_cache_oid($device, "NetIfEntry", $port_stats, "NETAPP-MIB", mib_dirs("netapp"));
}

// If etherlike extended error statistics are enabled, walk dot3StatsEntry else only dot3StatsDuplexStatus.
if ($config['enable_ports_etherlike'])
{
  echo("dot3Stats "); $port_stats = snmpwalk_cache_oid($device, "dot3StatsEntry", $port_stats, "EtherLike-MIB");
} else {
  echo("dot3StatsDuplexStatus"); $port_stats = snmpwalk_cache_oid($device, "dot3StatsDuplexStatus", $port_stats, "EtherLike-MIB");
}

// Find out if we have any ADSL ports
if ($config['enable_ports_adsl'])
{
  $device['adsl_count'] = dbFetchCell("SELECT COUNT(*) FROM `ports` WHERE `device_id` = ? AND `ifType` = 'adsl'", array($device['device_id']));
}

// Fetch the ADSL-LINE-MIB OIDs if we have more than one ADSL port.
// This data is used in the per-port adsl include.
if ($device['adsl_count'] > "0")
{
  echo("ADSL ");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.1.1", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.2.1", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.3.1", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.4.1", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.5.1", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.6.1.1", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.6.1.2", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.6.1.3", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.6.1.4", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.6.1.5", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.6.1.6", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.6.1.7", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.6.1.8", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.7.1.1", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.7.1.2", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.7.1.3", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.7.1.4", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.7.1.5", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.7.1.6", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.7.1.7", $port_stats, "ADSL-LINE-MIB");
}

// Fetch POWER-ETHERNET-MIB and CISCO-POWER-ETHERNET-EXT-MIB if enable_ports_poe is enabled.
// This data is used in the per-port poe include.
if ($config['enable_ports_poe'])
{
  $port_stats = snmpwalk_cache_oid($device, "pethPsePortEntry", $port_stats, "POWER-ETHERNET-MIB");
  $port_stats = snmpwalk_cache_oid($device, "cpeExtPsePortEntry", $port_stats, "CISCO-POWER-ETHERNET-EXT-MIB");
}

/// FIXME This probably needs re-enabled. We need to clear these things when they get unset, too.
#foreach ($etherlike_oids as $oid) { $port_stats = snmpwalk_cache_oid($device, $oid, $port_stats, "EtherLike-MIB"); }
#foreach ($cisco_oids as $oid)     { $port_stats = snmpwalk_cache_oid($device, $oid, $port_stats, "OLD-CISCO-INTERFACES-MIB"); }
#foreach ($pagp_oids as $oid)      { $port_stats = snmpwalk_cache_oid($device, $oid, $port_stats, "CISCO-PAGP-MIB"); }

// If the device is cisco, pull a few cisco-specific MIBs and try to get vlan data from CISCO-VTP-MIB
if ($device['os_group'] == "cisco")
{
  foreach ($pagp_oids as $oid) { $port_stats = snmpwalk_cache_oid($device, $oid, $port_stats, "CISCO-PAGP-MIB"); }
  $data_oids[] = "portName";

  // Grab data to put ports into vlans or make them trunks
  /// FIXME we probably shouldn't be doing this from the VTP MIB, right?
  $port_stats = snmpwalk_cache_oid($device, "vmVlan", $port_stats, "CISCO-VLAN-MEMBERSHIP-MIB");
  $port_stats = snmpwalk_cache_oid($device, "vlanTrunkPortEncapsulationOperType", $port_stats, "CISCO-VTP-MIB");
  $port_stats = snmpwalk_cache_oid($device, "vlanTrunkPortNativeVlan", $port_stats, "CISCO-VTP-MIB");

} else {

  // The port is not Cisco. Try to get VLAN data from Q-BRIDGE-MIB.

  $port_stats = snmpwalk_cache_oid($device, "dot1qPortVlanTable", $port_stats, "Q-BRIDGE-MIB");

  $vlan_ports = snmpwalk_cache_twopart_oid($device, "dot1qVlanCurrentEgressPorts", $vlan_stats, "Q-BRIDGE-MIB");
  $vlan_ifindex_map = snmpwalk_cache_oid($device, "dot1dBasePortIfIndex", $vlan_stats, "Q-BRIDGE-MIB");

  foreach ($vlan_ports as $instance)
  {
    foreach (array_keys($instance) as $vlan_id)
    {
      $parts = explode(' ',$instance[$vlan_id]['dot1qVlanCurrentEgressPorts']);
      $binary = '';
      foreach ($parts as $part)
      {
        $binary .= zeropad(decbin($part),8);
      }
      for ($i = 0; $i < strlen($binary); $i++)
      {
        if ($binary[$i])
        {
          $ifindex = $i; /// FIXME $vlan_ifindex_map[$i]
          $q_bridge_mib[$ifindex][] = $vlan_id;
        }
      }
    }
  }
}

$polled = time();

// End Building SNMP Cache Array

if ($debug) { print_vars($port_stats); }

// Build array of ports in the database

/// FIXME -- this stuff is a little messy, looping the array to make an array just seems wrong. :>
//       -- i can make it a function, so that you don't know what it's doing.
//       -- $ports = adamasMagicFunction($ports_db); ?

$sql  = "SELECT *, `ports`.`port_id` as `port_id`";
$sql .= " FROM  `ports`";
$sql .= " LEFT JOIN  `ports-state` ON  `ports`.port_id =  `ports-state`.port_id";
$sql .= " WHERE `device_id` = ?";

$ports_db = dbFetchRows($sql, array($device['device_id']));
foreach ($ports_db as $port) { $ports[$port['ifIndex']] = $port; }

// New interface detection
foreach ($port_stats as $ifIndex => $port)
{
  if (is_port_valid($port, $device))
  {
    if (!is_array($ports[$port['ifIndex']]))
    {
      $port_id = dbInsert(array('device_id' => $device['device_id'], 'ifIndex' => $ifIndex), 'ports');
      $ports[$port['ifIndex']] = dbFetchRow("SELECT * FROM `ports` WHERE `port_id` = ?", array($port_id));
      echo("Adding: ".$port['ifName']."(".$ifIndex.")(".$ports[$port['ifIndex']]['port_id'].")");
    } elseif ($ports[$ifIndex]['deleted'] == "1") {
      dbUpdate(array('deleted' => '0'), 'ports', '`port_id` = ?', array($ports[$ifIndex]['port_id']));
      log_event("Port DELETED mark removed", $device, 'interface', $ports[$ifIndex]['port_id']);
      $ports[$ifIndex]['deleted'] = "0";
    }
  } else {
    if (isset($ports[$port['ifIndex']]) && $ports[$port['ifIndex']]['deleted'] != "1")
    {
      dbUpdate(array('deleted' => '1'), 'ports', '`port_id` = ?', array($ports[$ifIndex]['port_id']));
      log_event("Port was marked as DELETED", $device, 'interface', $ports[$ifIndex]['port_id']);
      $ports[$ifIndex]['deleted'] = "1";
    }
  }
}
// End New interface detection

echo("\n");
// Loop ports in the DB and update where necessary
foreach ($ports as $port)
{
  echo("Port " . $port['ifDescr'] . "(".$port['ifIndex'].") ");
  if ($port_stats[$port['ifIndex']] && $port['disabled'] != "1")
  { // Check to make sure Port data is cached.
    $this_port = &$port_stats[$port['ifIndex']];

    if ($device['os'] == "vmware" && preg_match("/Device ([a-z0-9]+) at .*/", $this_port['ifDescr'], $matches)) { $this_port['ifDescr'] = $matches[1]; }

    if ($device['os'] == 'zxr10') { $this_port['ifAlias'] = preg_replace("/^" . str_replace("/", "\\/", $this_port['ifName']) . "\s*/", '', $this_port['ifDescr']); }

    $polled_period = $polled - $port['poll_time'];

    $port['update'] = array();
    $port['state'] = array();

    $port['state']['poll_time'] = $polled;
    $port['state']['poll_period'] = $polled_period;

    // Record 32 and 64 bit counters for debugging later
    if ($config['debug_port'][$port['port_id']])
    {
      $port_debug  = $port['port_id']."|".$polled."|".$polled_period."|".$this_port['ifInOctets']."|".$this_port['ifOutOctets']."|".$this_port['ifHCInOctets']."|".$this_port['ifHCOutOctets'];
    }

#    // Copy ifHC[In|Out] values to non-HC if they exist
#    // Check if they're greater than zero to work around stupid devices which expose HC counters, but don't populate them. HERPDERP. - adama
#    if($device['os'] == "netapp") { $hc_prefixes = array('HC', '64'); } else { $hc_prefixes = array('HC'); }
#    foreach($hc_prefixes as $hc_prefix)
#    {
#      foreach (array('Octets', 'UcastPkts', 'BroadcastPkts', 'MulticastPkts') as $hc)
#      {
#        $hcin = 'if'.$hc_prefix.'In'.$hc;
#        $hcout = 'if'.$hc_prefix.'Out'.$hc;
#        if (is_numeric($this_port[$hcin]) && $this_port[$hcin] > 0 && is_numeric($this_port[$hcout]) && $this_port[$hcout] > 0)
#        {
#          echo(" ".$hc_prefix." $hc, ");
#          $this_port['ifIn'.$hc]  = $this_port[$hcin];
#          $this_port['ifOut'.$hc] = $this_port[$hcout];
#        }
#      }
#    }

    // If we're not using SNMPv1, assumt there are 64-bit values and overwrite the 32-bit OIDs.
    if($device['snmpver'] != "v1")
    {
      // NetApp are dumb. Very Dumb. They invent their own random OIDs for 64-bit values. Bad netapp, why you so dumb?
      if($device['os'] == "netapp") { $hc_prefixes = array('64'); } else { $hc_prefixes = array('HC'); }

      foreach (array('Octets', 'UcastPkts', 'BroadcastPkts', 'MulticastPkts') as $hc)
      {
        $hcin = 'if'.$hc_prefix.'In'.$hc;
        $hcout = 'if'.$hc_prefix.'Out'.$hc;
        $this_port['ifIn'.$hc]  = $this_port[$hcin];
        $this_port['ifOut'.$hc] = $this_port[$hcout];
      }

    }

    // rewrite the ifPhysAddress
    if (strpos($this_port['ifPhysAddress'], ":"))
    {
      list($a_a, $a_b, $a_c, $a_d, $a_e, $a_f) = explode(":", $this_port['ifPhysAddress']);
      $this_port['ifPhysAddress'] = zeropad($a_a).zeropad($a_b).zeropad($a_c).zeropad($a_d).zeropad($a_e).zeropad($a_f);
    }

    // Overwrite ifSpeed with ifHighSpeed if it's over 10G
    if (is_numeric($this_port['ifHighSpeed']) && $this_port['ifSpeed'] > "1000000000")
    {
      echo("HighSpeed, ");
      $this_port['ifSpeed'] = $this_port['ifHighSpeed'] * 1000000;
    }

    // Overwrite ifDuplex with dot3StatsDuplexStatus if it exists
    if (isset($this_port['dot3StatsDuplexStatus']))
    {
      echo("dot3Duplex, ");
      $this_port['ifDuplex'] = $this_port['dot3StatsDuplexStatus'];
    }

    // Set VLAN and Trunk from Cisco
    if (isset($this_port['vlanTrunkPortEncapsulationOperType']) && $this_port['vlanTrunkPortEncapsulationOperType'] != "notApplicable")
    {
      $this_port['ifTrunk'] = $this_port['vlanTrunkPortEncapsulationOperType'];
      if (isset($this_port['vlanTrunkPortNativeVlan'])) { $this_port['ifVlan'] = $this_port['vlanTrunkPortNativeVlan']; }
    }

    if (isset($this_port['vmVlan']))
    {
      $this_port['ifVlan']  = $this_port['vmVlan'];
    }

    // Set VLAN and Trunk from Q-BRIDGE-MIB
    if (!isset($this_port['ifVlan']) && isset($this_port['dot1qPvid']))
    {
      $this_port['ifVlan'] = $this_port['dot1qPvid'];
    }
    /// FIXME use $q_bridge_mib[$this_port['ifIndex']] to see if it is a trunk (>1 array count)

    echo("VLAN == ".$this_port['ifVlan']);

    // Process ifAlias if needed
    if($config['os'][$device['os']]['ifAliasSemicolon'] == TRUE) { list($this_port['ifDescr']) = explode(';', $this_port['ifDescr']); }

    // Update IF-MIB data
    foreach ($data_oids as $oid)
    {
      if ($port[$oid] != $this_port[$oid] && !isset($this_port[$oid]))
      {
        $port['update'][$oid] = NULL;
        // If this port is ignored, do not create eventlog entries. Ignore means we don't give a fuck.
        if($port['ignore'] != '0')
        {
          log_event($oid . ": ".$port[$oid]." -> NULL", $device, 'interface', $port['port_id']);
        }
        if ($debug) { echo($oid . ": ".$port[$oid]." -> NULL "); } else { echo($oid . " "); }
      } elseif ($port[$oid] != $this_port[$oid]) {
        $port['update'][$oid] = $this_port[$oid];
        // If this port is ignored, do not create eventlog entries. Ignore means we don't give a fuck.
        if($port['ignore'] != '0')
        {
          log_event($oid . ": ".$port[$oid]." -> " . $this_port[$oid], $device, 'interface', $port['port_id']);
        }
        if ($debug) { echo($oid . ": ".$port[$oid]." -> " . $this_port[$oid]." "); } else { echo($oid . " "); }
      }
    }

    // Parse description (usually ifAlias) if config option set
    if (isset($config['port_descr_parser']) && is_file($config['install_dir'] . "/" . $config['port_descr_parser']))
    {
      $port_attribs = array('type','descr','circuit','speed','notes');

      include($config['install_dir'] . "/" . $config['port_descr_parser']);

      foreach ($port_attribs as $attrib)
      {
        $attrib_key = "port_descr_".$attrib;
        if ($port_ifAlias[$attrib] != $port[$attrib_key])
        {
          $port['update'][$attrib_key] = $port_ifAlias[$attrib];
          log_event($attrib . ": ".$port[$attrib_key]." -> " . $port_ifAlias[$attrib], $device, 'interface', $port['port_id']);
        }
      }
    }
    // End parse ifAlias

    // Update IF-MIB metrics
    foreach ($stat_oids_db as $oid)
    {
      $port['state'][$oid] = $this_port[$oid];
      if (isset($port[$oid]))
      {
        $oid_diff = $this_port[$oid] - $port[$oid];
        $oid_rate  = $oid_diff / $polled_period;
        if ($oid_rate < 0) { $oid_rate = "0"; echo("negative $oid"); }
        $port['stats'][$oid.'_rate'] = $oid_rate; $port['alert_array'][$oid.'_rate'] = $oid_rate;
        $port['stats'][$oid.'_diff'] = $oid_diff;
        $port['state'][$oid.'_rate'] = $oid_rate;
        $port['state'][$oid.'_delta'] = $oid_diff;
        if ($debug) { echo("\n $oid ($oid_diff B) $oid_rate Bps $polled_period secs\n"); }
      }

    }

    foreach ($stat_oids as $oid)
    {
      // Update StatsD/Carbon
      if($config['statsd']['enable'] == TRUE && !strpos($oid, "HC"))
      {
        StatsD::gauge(str_replace(".", "_", $device['hostname']).'.'.'port'.'.'.$port['ifIndex'].'.'.$oid, $this_port[$oid]);
      }
    }

    $port['stats']['ifInBits_rate'] = round($port['stats']['ifInOctets_rate'] * 8);
    $port['stats']['ifOutBits_rate'] = round($port['stats']['ifOutOctets_rate'] * 8);

    // If we have been told to debug this port, output the counters we collected earlier, with the rates stuck on the end.
    if ($config['debug_port'][$port['port_id']])
    {
      $port_debug .= "|".formatRates($port['stats']['ifInOctets_rate'])."|".formatRates($port['stats']['ifOutOctets_rate'])."|".$device['snmpver']."\n";
      file_put_contents("/tmp/port_debug_".$port['port_id'].".txt", $port_debug, FILE_APPEND);
      echo("Wrote port debugging data");
    }

    // If we see a spike above ifSpeed, output it to /tmp/port_debug.txt
    if ($this_port['ifSpeed'] > "0" && ($port['stats']['ifInBits_rate'] > $this_port['ifSpeed'] || $port['stats']['ifOutBits_rate'] > $this_port['ifSpeed']))
    {
      echo("Spike above ifSpeed detected!");
      $debug_file = "/tmp/port_debug.txt";
      $debug_temp  = "--------------------------------------------------------------------\n";
      $debug_temp .= $device['hostname']." ".$port['ifDescr']." ".formatRates($this_port['ifSpeed'])."\n";
      $debug_temp .= formatRates($port['stats']['ifOutBits_rate']*8)."|".formatRates($port['stats']['ifInBits_rate'])."\n";
      $debug_temp .= $port['poll_time']."|".$port['ifOutOctets']."|".$port['ifInOctets']."\n";
      $debug_temp .= $polled."|".$this_port['ifOutOctets']."|".$this_port['ifInOctets']."\n";
      $debug_temp .= "\n";
      file_put_contents($debug_file, $debug_temp, FILE_APPEND);
    }

    // Put States into alert array
    foreach(array('ifOperStatus', 'ifAdminStatus', 'ifMtu') AS $oid)
    {
      if (isset($this_port[$oid]))
      {
        $port['alert_array'][$oid] = $this_port[$oid];
      }
    }


    // If we have a valid ifSpeed we should populate the percentage stats for checking.
    if (is_numeric($this_port['ifSpeed']))
    {
      $port['stats']['ifInBits_perc'] = round($port['stats']['ifInBits_rate'] / $this_port['ifSpeed'] * 100);
      $port['stats']['ifOutBits_perc'] = round($port['stats']['ifOutBits_rate'] / $this_port['ifSpeed'] * 100);
      $port['alert_array']['ifSpeed'] = $this_port['ifSpeed'];
    }

    $port['state']['ifInOctets_perc'] = $port['stats']['ifInBits_perc'];
    $port['state']['ifOutOctets_perc'] = $port['stats']['ifOutBits_perc'];

    $port['alert_array']['ifInOctets_perc'] = $port['stats']['ifInBits_perc'];
    $port['alert_array']['ifOutOctets_perc'] = $port['stats']['ifOutBits_perc'];

    $port['alert_array']['rx_ave_pktsize']   = $port['state']['ifInOctets_delta'] / ($port['state']['ifInUcastPkts_delta'] + $port['state']['ifInNUcastPkts_delta']);
    $port['alert_array']['tx_ave_pktsize']   = $port['state']['ifOutOctets_delta'] / ($port['state']['ifOutUcastPkts_delta'] + $port['state']['ifOutNUcastPkts_delta']);


    echo('bps('.formatRates($port['stats']['ifInBits_rate']).'/'.formatRates($port['stats']['ifOutBits_rate']).')');
    echo('bytes('.formatStorage($port['stats']['ifInOctets_diff']).'/'.formatStorage($port['stats']['ifOutOctets_diff']).')');
    echo('pkts('.format_si($port['stats']['ifInUcastPkts_rate']).'pps/'.format_si($port['stats']['ifOutUcastPkts_rate']).'pps)');

    // Store aggregate in/out state
    $port['state']['ifOctets_rate']    = $port['stats']['ifOutOctets_rate'] + $port['stats']['ifInOctets_rate'];
    $port['state']['ifUcastPkts_rate'] = $port['stats']['ifOutUcastPkts_rate'] + $port['stats']['ifInUcastPkts_rate'];
    $port['state']['ifErrors_rate'] = $port['stats']['ifOutErrors_rate'] + $port['stats']['ifInErrors_rate'];

    // Port utilisation % threshold alerting. /// FIXME allow setting threshold per-port. probably 90% of ports we don't care about.
    /// FIXME integrate this into some giant alerting thing. probably.
    if ($port['ignore'] == 0 && $config['alerts']['port_util_alert'])
    {
      // Check for port saturation of $config['alerts']['port_util_perc'] or higher.  Alert if we see this.
      // Check both inbound and outbound rates
      $saturation_threshold = $this_port['ifSpeed'] * ( $config['alerts']['port_util_perc'] / 100 );
      echo(" Threshold:" . formatRates($saturation_threshold));
      if (($port['stats']['ifInBits_rate'] >= $saturation_threshold ||  $port['stats']['ifOutBits_rate'] >= $saturation_threshold) && $saturation_threshold > 0)
      {
          log_event('Port reached saturation threshold: ' . formatRates($port['stats']['ifInBits_rate']) . '/' . formatRates($port['stats']['ifOutBits_rate']) . '('.$port['stats']['ifInBits_perc'].'/'.$port['stats']['ifOutBits_perc'].') >'.$config['alerts']['port_util_perc'].'% of ' . formatRates( $this_port['ifSpeed'])  , $device, 'interface', $port['port_id']);
          notify($device, 'Port saturation on ' . $device['hostname'] . ' (' . $port['ifName'] . ')' , 'Port saturation threshold alarm: ' . $device['hostname'] . ' on ' . $port['ifDescr'] . "\nRates:" . formatRates($port['stats']['ifInBits_rate']) . '/' . formatRates($port['stats']['ifOutBits_rate']) . '('.$port['stats']['ifInBits_perc'].'/'.$port['stats']['ifOutBits_perc'].') >'.$config['alerts']['port_util_perc'].'% of ' . formatRates( $this_port['ifSpeed']));
          echo(" *EXCEEDED*");
      }
    }


    // Update RRDs
    $rrdfile = get_port_rrdfilename($device, $port);
    if (!is_file($rrdfile))
    {
      rrdtool_create($rrdfile," --step 300 \
      DS:INOCTETS:DERIVE:600:0:12500000000 \
      DS:OUTOCTETS:DERIVE:600:0:12500000000 \
      DS:INERRORS:DERIVE:600:0:12500000000 \
      DS:OUTERRORS:DERIVE:600:0:12500000000 \
      DS:INUCASTPKTS:DERIVE:600:0:12500000000 \
      DS:OUTUCASTPKTS:DERIVE:600:0:12500000000 \
      DS:INNUCASTPKTS:DERIVE:600:0:12500000000 \
      DS:OUTNUCASTPKTS:DERIVE:600:0:12500000000 \
      DS:INDISCARDS:DERIVE:600:0:12500000000 \
      DS:OUTDISCARDS:DERIVE:600:0:12500000000 \
      DS:INUNKNOWNPROTOS:DERIVE:600:0:12500000000 \
      DS:INBROADCASTPKTS:DERIVE:600:0:12500000000 \
      DS:OUTBROADCASTPKTS:DERIVE:600:0:12500000000 \
      DS:INMULTICASTPKTS:DERIVE:600:0:12500000000 \
      DS:OUTMULTICASTPKTS:DERIVE:600:0:12500000000 ".$config['rrd_rra']);
    }

    $this_port['rrd_update']  = array($this_port['ifInOctets'], $this_port['ifOutOctets'], $this_port['ifInErrors'], $this_port['ifOutErrors'],
                                      $this_port['ifInUcastPkts'], $this_port['ifOutUcastPkts'], $this_port['ifInNUcastPkts'], $this_port['ifOutNUcastPkts'],
                                      $this_port['ifInDiscards'], $this_port['ifOutDiscards'], $this_port['ifInUnknownProtos'],
                                      $this_port['ifInBroadcastPkts'], $this_port['ifOutBroadcastPkts'], $this_port['ifInMulticastPkts'], $this_port['ifOutMulticastPkts']);

    rrdtool_update("$rrdfile", $this_port['rrd_update']);
    // End Update IF-MIB

    // Update PAgP
    if ($this_port['pagpOperationMode'] || $port['pagpOperationMode'])
    {
      foreach ($pagp_oids as $oid)
      { // Loop the OIDs
        if ($this_port[$oid] != $port[$oid])
        { // If data has changed, build a query
          $port['update'][$oid] = $this_port[$oid];
          echo("PAgP ");
          log_event("$oid -> ".$this_port[$oid], $device, 'interface', $port['port_id']);
        }
      }
    }
    // End Update PAgP

    /// FIXME. Is that true include for EACH port? -- mike 
    /// Yes, but it's not expensive computationally. Use php-xcache. :) -- adama
    // Do EtherLike-MIB
    if ($config['enable_ports_etherlike']) { include("port-etherlike.inc.php"); }

    // Do ADSL MIB
    if ($config['enable_ports_adsl']) { include("port-adsl.inc.php"); }

    // Do PoE MIBs
    if ($config['enable_ports_poe']) { include("port-poe.inc.php"); }

#    if ($debug || TRUE) { print_vars($port['alert_array']); echo(PHP_EOL); print_vars($this_port);}
#    print_vars($port['alert_array']);

    check_entity('port', $port, $port['alert_array']);


    // Send statistics array via AMQP/JSON if AMQP is enabled globally and for the ports module
    if($config['amqp']['enable'] == TRUE && $config['amqp']['modules']['ports'])
    {
      $json_data = array_merge($this_port, $port['state']) ;
      unset($json_data['rrd_update']);
      messagebus_send(array('attribs' => array('t' => $polled, 'device' => $device['hostname'], 'device_id' => $device['device_id'], 'e_type' => 'port', 'e_index' => $port['ifIndex']), 'data' => $json_data));
      unset($json_data);
    }

#    // Do Alcatel Detailed Stats
#    if ($device['os'] == "aos") { include("port-alcatel.inc.php"); }

    // Update Database
    if (count($port['update']))
    {
      $updated = dbUpdate($port['update'], 'ports', '`port_id` = ?', array($port['port_id']));
      if ($debug) { echo("$updated updated"); }
    }

    // Update State
    if (count($port['state']))
    {
      if (empty($port['poll_time']))
      {
        $insert = dbInsert(array('port_id' => $port['port_id']), 'ports-state');
        if ($debug) { echo("state inserted"); }
      }
      $updated = dbUpdate($port['state'], 'ports-state', '`port_id` = ?', array($port['port_id']));
      if ($debug) { echo("$updated updated"); }
    }

    // End Update Database

    // Send alerts for interface flaps.
    $port_ifdown = (isset($port_ifAlias['type']) ? in_array($port_ifAlias['type'], $config['alerts']['port']['ifdown_type']) : TRUE);
    if ($port['ignore'] == 0 && $config['alerts']['port']['ifdown'] && $port_ifdown && ($port['ifOperStatus'] != $this_port['ifOperStatus']))
    {
      if ($this_port['ifAlias'])
      {
        $falias = preg_replace('/^"/', '', $this_port['ifAlias']); $falias = preg_replace('/"$/', '', $falias);
        $full = $this_port['ifDescr'] . " (" . $falias . ")";
      } else {
        $full = $this_port['ifDescr'];
      }
      switch ($this_port['ifOperStatus'])
      {
        case "up":
          notify($device, "Interface UP - " . $device['hostname'] . " - " . $full, "Device:    " . $device['hostname'] . "\nInterface: " . $full);
          break;
        case "down":
          notify($device, "Interface DOWN - " . $device['hostname'] . " - " . $full, "Device:    " . $device['hostname'] . "\nInterface: " . $full);
          break;
      }
    }
  }
  elseif ($port['disabled'] != "1")
  {
    print_message("Port Deleted."); // Port missing from SNMP cache.
    if (isset($port['ifIndex']) && $port['deleted'] != "1")
    {
      dbUpdate(array('deleted' => '1'), 'ports',  '`device_id` = ? AND `ifIndex` = ?', array($device['device_id'], $port['ifIndex']));
      log_event("Port was marked as DELETED", $device, 'interface', $port['port_id']);
    }
  } else {
    print_message("Port Disabled.");
  }

  echo("\n");

  // Clear Per-Port Variables Here
  unset($this_port);
}

// Clear Variables Here
unset($port_stats);

?>
