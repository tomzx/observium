#!/usr/bin/env php
<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    observium
 * @subpackage cli
 * @author     Adam Armstrong <adama@memetic.org>
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */

chdir(dirname($argv[0]));

include("includes/defaults.inc.php");
include("config.php");
include("includes/definitions.inc.php");
include("includes/functions.php");

# Remove a host and all related data from the system

if ($argv[1] && $argv[2])
{
  $host = strtolower($argv[1]);
  $id = getidbyname($host);
  if ($id)
  {
    $tohost = strtolower($argv[2]);
    $toid = getidbyname($tohost);
    if ($toid)
    {
      print_error("NOT renamed. New hostname $tohost already exists.");
    } else {
      if (renamehost($id, $tohost, 'console'))
      {
	print_message("Host $host renamed to $tohost.");
      }
    }
  } else {
    print_error("Host $host doesn't exist.");
  }
}
else
{
    print_message("%gObservium v".$config['version']."
%WRename Device%n

USAGE:
renamehost.php <old hostname> <new hostname>

%rInvalid arguments!%n", 'color');
}

?>
