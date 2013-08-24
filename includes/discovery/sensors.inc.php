<?php

$valid['sensor'] = array();

echo("Sensors: ");

include("includes/discovery/cisco-entity-sensor.inc.php");
include("includes/discovery/entity-sensor.inc.php");

$include_dir = "includes/discovery/sensors";
include("includes/include-dir.inc.php");

echo("\n");

include("includes/discovery/dbm.inc.php");
include("includes/discovery/temperatures.inc.php");
include("includes/discovery/humidity.inc.php");
include("includes/discovery/voltages.inc.php");
include("includes/discovery/frequencies.inc.php");
include("includes/discovery/current.inc.php");
include("includes/discovery/power.inc.php");

$sensortypes = array('fanspeed');

foreach ($sensortypes as $type)
{
  if ($debug) { print_vars($valid['sensor']['$type']); }
  check_valid_sensors($device, $type, $valid['sensor']);
}

?>
