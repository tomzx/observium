#!/usr/bin/env php
<?php

include("includes/defaults.inc.php");
include("config.php");
include("includes/definitions.inc.php");
include("includes/functions.php");

$opts = getopt("h:");

if(empty($opts['h']))
{
  print "Usage: scripts/rename-port-rrdfile.php -h <device_id|hostname>\n\n";
  exit(1);
}

if(is_numeric($opts['h']))
{
  $where = "`device_id` = ?";
}
else
{
  $where = "`hostname` = ?";
}

$device = dbFetchRow("SELECT * FROM devices WHERE " . $where, array($opts['h']));

$ports = dbFetchRows("SELECT * FROM ports WHERE `device_id` = ?", array($device['device_id']));

foreach ($ports as $port) {
  $old_rrdfile = trim($config['rrd_dir'])."/".trim($device['hostname'])."/port-".$port['ifIndex'].".rrd";
  $new_rrdfile = get_port_rrdfilename($device, $port);

  printf("%s -> %s\n", $old_rrdfile, $new_rrdfile);

  rename($old_rrdfile, $new_rrdfile);
}

