<?php

/* Observium Network Management and Monitoring System
 * Copyright (C) 2006-2013, Observium Developers - http://www.observium.org
 *
 * @package    observium
 * @subpackage poller
 * @author     Adam Armstrong <adama@memetic.org>
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */

  unset($poll_device);

  $snmpdata = snmp_get_multi($device, "sysUpTime.0 sysLocation.0 sysContact.0 sysName.0", "-OQUs", "SNMPv2-MIB");
  $poll_device = $snmpdata[0];

  $poll_device['sysDescr'] = snmp_get($device, "sysDescr.0", "-Oqv", "SNMPv2-MIB");
  $poll_device['sysObjectID'] = snmp_get($device, "sysObjectID.0", "-Oqvn", "SNMPv2-MIB");
  $poll_device['sysName'] = strtolower($poll_device['sysName']);

  if (is_numeric($agent_data['uptime'])) {
    list($uptime) = explode(" ", $agent_data['uptime']);
    $uptime = round($uptime);
    $uptime_msg = "Using UNIX Agent Uptime";
  } else  {
    $hrSystemUptime = snmp_get($device, "hrSystemUptime.0", "-Oqv", "HOST-RESOURCES-MIB");
    if (!empty($hrSystemUptime) && !strpos($hrSystemUptime, "No") && ($device['os'] != "windows"))
    {
      $agent_uptime = $uptime; // Move uptime into agent_uptime
      // HOST-RESOURCES-MIB::hrSystemUptime.0 = Timeticks: (63050465) 7 days, 7:08:24.65
      $hrSystemUptime = str_replace("(", "", $hrSystemUptime);
      $hrSystemUptime = str_replace(")", "", $hrSystemUptime);
      list($days,$hours, $mins, $secs) = explode(":", $hrSystemUptime);
      list($secs, $microsecs) = explode(".", $secs);
      $hours = $hours + ($days * 24);
      $mins = $mins + ($hours * 60);
      $secs = $secs + ($mins * 60);
      $uptime = $secs;
      $uptime_msg = "Using SNMP Agent hrSystemUptime";
    } else {
      // SNMPv2-MIB::sysUpTime.0 = Timeticks: (2542831) 7:03:48.31
      $poll_device['sysUpTime'] = str_replace("(", "", $poll_device['sysUpTime']);
      $poll_device['sysUpTime'] = str_replace(")", "", $poll_device['sysUpTime']);
      list($days, $hours, $mins, $secs) = explode(":", $poll_device['sysUpTime']);
      list($secs, $microsecs) = explode(".", $secs);
      $hours = $hours + ($days * 24);
      $mins = $mins + ($hours * 60);
      $secs = $secs + ($mins * 60);
      $uptime = $secs;
      $uptime_msg = "Using SNMP Agent sysUpTime";
    }

    // Last check snmpEngineTime and fix if needed uptime (sysUpTime 68 year rollover issue)
    // SNMP-FRAMEWORK-MIB::snmpEngineTime.0 = INTEGER: 72393514 seconds
    $snmpEngineTime = (integer)snmp_get($device, "snmpEngineTime.0", "-OUqv", "SNMP-FRAMEWORK-MIB");
    if (is_numeric($snmpEngineTime) && $snmpEngineTime > 0 && $snmpEngineTime > $uptime)
    {
      $uptime = $snmpEngineTime;
      $uptime_msg = "Using SNMP Agent snmpEngineTime";
    }
  }
  echo($uptime_msg." (".$uptime." seconds)\n");

  if (is_numeric($uptime))
  {
    if ($uptime < $device['uptime'])
    {
      notify($device,"Device rebooted: " . $device['hostname'],  "Device Rebooted : " . $device['hostname'] . " " . formatUptime($uptime) . " ago.");
      log_event('Device rebooted after '.formatUptime($device['uptime']), $device, 'reboot', $device['uptime']);
    }

    $uptime_rrd = $config['rrd_dir'] . "/" . $device['hostname'] . "/uptime.rrd";

    if (!is_file($uptime_rrd))
    {
      rrdtool_create ($uptime_rrd, "DS:uptime:GAUGE:600:0:U ".$config['rrd_rra']);
    }
    rrdtool_update($uptime_rrd, "N:".$uptime);

    $graphs['uptime'] = TRUE;

    echo("Uptime: ".formatUptime($uptime)."\n");

    $update_array['uptime'] = $uptime;
  }

  $poll_device['sysLocation'] = str_replace("\"","", $poll_device['sysLocation']);

  // Rewrite sysLocation if there is a mapping array (database too?)
  if (!empty($poll_device['sysLocation']) && is_array($config['location_map']))
  {
    $poll_device['sysLocation'] = rewrite_location($poll_device['sysLocation']);
  }

  $poll_device['sysContact']  = str_replace("\"","", $poll_device['sysContact']);

  if ($poll_device['sysLocation'] == "not set")
  {
    $poll_device['sysLocation'] = "";
  }

  if ($poll_device['sysContact'] == "not set")
  {
    $poll_device['sysContact'] = "";
  }

  if ($poll_device['sysContact'] && $poll_device['sysContact'] != $device['sysContact'])
  {
    $update_array['sysContact'] = $poll_device['sysContact'];
    log_event("Contact -> ".$poll_device['sysContact'], $device, 'system');
  }

  if ($poll_device['sysName'] && $poll_device['sysName'] != $device['sysName'])
  {
    $update_array['sysName'] = $poll_device['sysName'];
    log_event("sysName -> ".$poll_device['sysName'], $device, 'system');
  }

  if ($poll_device['sysDescr'] && $poll_device['sysDescr'] != $device['sysDescr'])
  {
    $update_array['sysDescr'] = $poll_device['sysDescr'];
    log_event("sysDescr -> ".$poll_device['sysDescr'], $device, 'system');
  }

  // Allow override of sysLocation.

  if($attribs['override_sysLocation_bool'])
  {
    $poll_device['sysLocation'] = $attribs['override_sysLocation_string'];
  }

  if ($poll_device['sysLocation'] && $device['location'] != $poll_device['sysLocation'])
  {
    $update_array['location'] = $poll_device['sysLocation'];
    log_event("Location -> ".$poll_device['sysLocation'], $device, 'system');
  }

  if (($poll_device['sysLocation'] && $device['location'] != $poll_device['sysLocation']) || !$device['location_lat'] || !$device['location_lon'])
  {
    $update_array = array_merge($update_array, get_geolocation($poll_device['sysLocation']));
  }

?>
