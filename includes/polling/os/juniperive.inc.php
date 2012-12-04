<?php

$version = trim(snmp_get($device, "productVersion.0", "-OQv", "JUNIPER-IVE-MIB"),'"');
$hardware = "Juniper " . trim(snmp_get($device, "productName.0", "-OQv", "JUNIPER-IVE-MIB"),'"');
$hostname = snmp_get($device, "sysName.0","-OQv");


$rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/juniperive_users.rrd";
$clusterusers = snmp_get($device, "clusterConcurrentUsers.0", "-OQv", "JUNIPER-IVE-MIB");
$iveusers = snmp_get($device, "iveConcurrentUsers.0", "-OQv", "JUNIPER-IVE-MIB");

if (!is_null($clusterusers) and !is_null($iveusers))
{
 if (!is_file($rrd_filename))
 {
  rrdtool_create($rrd_filename, " --step 300 \
	DS:clusterusers:GAUGE:600:0:3000000 \
	DS:iveusers:GAUGE:600:0:3000000 ".$config['rrd_rra']); }
  rrdtool_update("$rrd_filename", "N:$clusterusers:$iveusers");
  $graphs['juniperive_users'] = TRUE;
}


$rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/juniperive_meetings.rrd";
$meetingusers = snmp_get($device, "meetingUserCount.0", "-OQv", "JUNIPER-IVE-MIB");
$meetings = snmp_get($device, "meetingCount.0", "-OQv", "JUNIPER-IVE-MIB");

if (is_numeric($meetingusers) and is_numeric($meetings))
{
 if (!is_file($rrd_filename))
 {
  rrdtool_create($rrd_filename, " --step 300 \
        DS:meetingusers:GAUGE:600:0:3000000 \
        DS:meetings:GAUGE:600:0:3000000 ".$config['rrd_rra']); }
  rrdtool_update("$rrd_filename", "N:$meetingusers:$meetings");
  $graphs['juniperive_meetings'] = TRUE;
}


$rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/juniperive_connections.rrd";
$webusers = snmp_get($device, "signedInWebUsers.0", "-OQv", "JUNIPER-IVE-MIB");
$mailusers = snmp_get($device, "signedInMailUsers.0", "-OQv", "JUNIPER-IVE-MIB");

if (!is_null($webusers) and !is_null($mailusers))
{
 if (!is_file($rrd_filename))
 {
  rrdtool_create($rrd_filename, " --step 300 \
        DS:webusers:GAUGE:600:0:3000000 \
        DS:mailusers:GAUGE:600:0:3000000 ".$config['rrd_rra']); }
  rrdtool_update("$rrd_filename", "N:$webusers:$mailusers");
  $graphs['juniperive_connections'] = TRUE;
}

$rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/juniperive_storage.rrd";
$diskpercent = snmp_get($device, "diskFullPercent.0", "-OQv", "JUNIPER-IVE-MIB");
$logpercent = snmp_get($device, "logFullPercent.0", "-OQv", "JUNIPER-IVE-MIB");

if (!is_null($diskpercent) and !is_null($logpercent))
{
 if (!is_file($rrd_filename))
 {
  rrdtool_create($rrd_filename, " --step 300 \
        DS:diskpercent:GAUGE:600:0:3000000 \
        DS:logpercent:GAUGE:600:0:3000000 ".$config['rrd_rra']); }
  rrdtool_update("$rrd_filename", "N:$diskpercent:$logpercent");
  $graphs['juniperive_storage'] = TRUE;
}

?>
