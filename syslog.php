#!/usr/bin/env php
<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    observium
 * @subpackage syslog
 * @author     Adam Armstrong <adama@memetic.org>
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */

include_once("includes/defaults.inc.php");
include_once("config.php");
include_once("includes/definitions.inc.php");
include($config['install_dir'] . "/includes/functions.php");

$i = 1;

$s = fopen('php://stdin','r');
while ($line = fgets($s))
{
  //logfile($line);
  // host || facility || priority || level || tag || timestamp || msg || program
  list($entry['host'],$entry['facility'],$entry['priority'], $entry['level'], $entry['tag'], $entry['timestamp'], $entry['msg'], $entry['program']) = explode("||", trim($line));
  process_syslog($entry, 1);
  unset($entry); unset($line);
  $i++;
}

?>
