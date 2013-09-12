<?php

$valid['sensor'] = array();

echo("Sensors: ");

include("includes/discovery/cisco-entity-sensor.inc.php");
include("includes/discovery/entity-sensor.inc.php");

$include_dir = "includes/discovery/sensors";
include("includes/include-dir.inc.php");

$include_dir = "includes/discovery/dbm";
include("includes/include-dir.inc.php");

$include_dir = "includes/discovery/temperatures";
include("includes/include-dir.inc.php");

$include_dir = "includes/discovery/humidity";
include("includes/include-dir.inc.php");

$include_dir = "includes/discovery/voltages";
include("includes/include-dir.inc.php");

$include_dir = "includes/discovery/frequencies";
include("includes/include-dir.inc.php");

$include_dir = "includes/discovery/current";
include("includes/include-dir.inc.php");

$include_dir = "includes/discovery/power";
include("includes/include-dir.inc.php");

foreach (array_keys($config['sensor_types']) as $type)
{
  if ($debug) { print_vars($valid['sensor'][$type]); }
  check_valid_sensors($device, $type, $valid['sensor']);
}

?>
