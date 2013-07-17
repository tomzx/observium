#!/usr/bin/env php
<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    observium
 * @subpackage discovery
 * @author     Adam Armstrong <adama@memetic.org>
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */

chdir(dirname($argv[0]));

include("includes/defaults.inc.php");
include("config.php");
include("includes/definitions.inc.php");
include("includes/functions.php");
include("includes/discovery/functions.inc.php");

$start = utime();
$runtime_stats = array();

// Observium Device Discovery

$options = getopt("h:m:i:n:d::a::qV");

if (isset($options['V']))
{
  print_message("Observium ".$config['version']);
  exit;
}
if (!isset($options['q']))
{
  print_message("%gObservium v".$config['version'].PHP_EOL."%WDiscovery\n%n", 'color');
}

if (isset($options['h']))
{
  if ($options['h'] == "odd")    { $options['n'] = "1"; $options['i'] = "2"; }
  elseif ($options['h'] == "even") { $options['n'] = "0"; $options['i'] = "2"; }
  elseif ($options['h'] == "all")  { $where = " "; $doing = "all"; }
  elseif ($options['h'] == "new")  { $where = "AND `last_discovered` IS NULL"; $doing = "new"; }
  elseif ($options['h'])
  {
    if (is_numeric($options['h']))
    {
      $where = "AND `device_id` = '".$options['h']."'";
      $doing = $options['h'];
    }
    else
    {
      $where = "AND `hostname` LIKE '".str_replace('*','%',mres($options['h']))."'";
      $doing = $options['h'];
    }
  }
}

if (isset($options['i']) && $options['i'] && isset($options['n']))
{
  $where = "AND MOD(device_id,".$options['i'].") = '" . $options['n'] . "'";
  $doing = $options['n'] ."/".$options['i'];
}

if (isset($options['d']))
{
  echo("DEBUG!\n");
  $debug = TRUE;
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  ini_set('log_errors', 1);
  ini_set('error_reporting', 1);
} else {
  $debug = FALSE;
  #  ini_set('display_errors', 0);
  ini_set('display_startup_errors', 0);
  ini_set('log_errors', 0);
  #  ini_set('error_reporting', 0);
}

if (!$where)
{
  print_message("USAGE:
discovery.php [-dqV] [-i instances] [-n number] [-m module] [-h device]

EXAMPLE:
-h <device id> | <device hostname wildcard>  Discover single device
-h odd                                       Discover odd numbered devices  (same as -i 2 -n 0)
-h even                                      Discover even numbered devices (same as -i 2 -n 1)
-h all                                       Discover all devices
-h new                                       Discover all devices that have not had a discovery run before

-i <instances> -n <number>                   Discover as instance <number> of <instances>
                                             Instances start at 0. 0-3 for -n 4

OPTIONS:
 -h                                          Device hostname, id or key odd/even/all/new.
 -i                                          Discovery instance.
 -n                                          Discovery number.
 -q                                          Quiet output.
 -V                                          Show version and exit.

DEBUGGING OPTIONS:
 -d                                          Enable debugging output.
 -m                                          Specify modules (separated by commas) to be run.

%rInvalid arguments!%n", 'color');
  exit;
}

if ($options['h'] == "new")
{
  print_warning("Schema update disabled by %W-h new%n, run with %W-h none%n to perform it manually.");
} else {
  include("includes/update/update.php");
}

$discovered_devices = 0;

$devices = array();
foreach (dbFetch("SELECT * FROM `devices` WHERE status = 1 AND disabled = 0 $where ORDER BY device_id DESC") as $device)
{
  array_push($devices, $device);
}

while($device = array_pop($devices))
{
  discover_device($device, $options);
}

$end = utime(); $run = $end - $start;
$proctime = substr($run, 0, 5);

if ($discovered_devices)
{
  dbInsert(array('type' => 'discover', 'doing' => $doing, 'start' => $start, 'duration' => $proctime, 'devices' => $discovered_devices), 'perf_times');
}

$string = $argv[0] . " $doing " .  date("F j, Y, G:i") . " - $discovered_devices devices discovered in $proctime secs";
if ($debug) echo("$string\n");

if($options['h'] != "new" && $config['version_check'])
{
  include("includes/versioncheck.inc.php");
}

if (!isset($options['q']))
{
  print_message('MySQL: Cell['.($db_stats['fetchcell']+0).'/'.round($db_stats['fetchcell_sec']+0,2).'s]'.
                       ' Row['.($db_stats['fetchrow']+0). '/'.round($db_stats['fetchrow_sec']+0,2).'s]'.
                      ' Rows['.($db_stats['fetchrows']+0).'/'.round($db_stats['fetchrows_sec']+0,2).'s]'.
                    ' Column['.($db_stats['fetchcol']+0). '/'.round($db_stats['fetchcol_sec']+0,2).'s]'.
                    ' Update['.($db_stats['update']+0).'/'.round($db_stats['update_sec']+0,2).'s]'.
                    ' Insert['.($db_stats['insert']+0). '/'.round($db_stats['insert_sec']+0,2).'s]'.
                    ' Delete['.($db_stats['delete']+0). '/'.round($db_stats['delete_sec']+0,2).'s]');
}

logfile($string);

?>
