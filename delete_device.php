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

// Remove a host and all related data from the system

if ($argv[1])
{
  $host = strtolower($argv[1]);
  $id = getidbyname($host);
  $delete_rrd = (isset($argv[2]) && strtolower($argv[2]) == 'rrd') ? TRUE : FALSE;

  // Test if a valid id was fetched from getidbyname.
  if (isset($id) && is_numeric($id))
  {
    echo(delete_device($id, $delete_rrd));
    print_success("Device $host removed.");
  } else {
    print_error("Device $host doesn't exist!");
  }

} else {
    print_message("%gObservium v".$config['version']."
%WRemove Device%n

USAGE:
delete_host.php.php <hostname> [rrd]

EXAMPLE:
%WKeep RRDs%n:   delete_host.php.php <hostname>
%WRemove RRDs%n: delete_host.php.php <hostname> rrd

%rInvalid arguments!%n", 'color');
}

?>
