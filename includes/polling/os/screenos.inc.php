<?php

$version = preg_replace("/(.+)\ version\ (.+)\ \(SN:\ (.+)\,\ (.+)\)/", "\\1||\\2||\\3||\\4", $poll_device['sysDescr']);
list($hardware,$version,$serial,$features) = explode("||", $version);

$hardware = snmp_get($device, "sysObjectID.0", "-Ovqsn");
$hardware = rewrite_junos_hardware($hardware);

$sessrrd  = $config['rrd_dir'] . "/" . $device['hostname'] . "/screenos_sessions.rrd";

$snmpdata = snmp_get_multi($device, "nsResSessAllocate.0 nsResSessMaxium.0 nsResSessFailed.0", "-OQUs", "NETSCREEN-RESOURCE-MIB", mib_dirs("netscreen"));
$sessalloc = $snmpdata[0]['nsResSessAllocate'];
$sessmax = $snmpdata[0]['nsResSessMaxium'];
$sessfailed = $snmpdata[0]['nsResSessFailed'];

if (!is_file($sessrrd))
{
   rrdtool_create($sessrrd, " --step 300 \
     DS:allocate:GAUGE:600:0:3000000 \
     DS:max:GAUGE:600:0:3000000 \
     DS:failed:GAUGE:600:0:1000 ".$config['rrd_rra']);
}

rrdtool_update("$sessrrd", "N:$sessalloc:$sessmax:$sessfailed");

$graphs['screenos_sessions'] = TRUE;

?>
