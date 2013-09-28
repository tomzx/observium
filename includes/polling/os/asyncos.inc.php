<?php

// Cisco IronPort Email Security Appliances, eg:
// Cisco IronPort Model C160, AsyncOS Version: 7.6.2-014, Build Date: 2012-11-02, Serial #: 99999AAA9AA9-99AAAA9

if (preg_match('/^Cisco IronPort Model (\w+),.*AsyncOS Version: ([\d\.-]+),.*Serial #: ([\w-]+)/', $poll_device['sysDescr'], $regexp_result))
{
  $hardware = $regexp_result[1];
  $version = $regexp_result[2];
  $serial = $regexp_result[3];
}

$a_rrd = $config['rrd_dir'] . "/" . $device['hostname'] . "/asyncos_workq.rrd";
$a_val = snmp_get($device, "ASYNCOS-MAIL-MIB::workQueueMessages.0", "-Ovq");
if (is_numeric($a_val))
{
  if (!is_file($a_rrd))
  {
    rrdtool_create($a_rrd,"  DS:DEPTH:ABSOLUTE:600:0:U ");
  }
  echo("Work Queue: $a_val\n");
  rrdtool_update($a_rrd, " N:$a_val");
  $graphs['asyncos_workq'] = TRUE;
}

// EOF
