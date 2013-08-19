<?php
echo("dBm: ");

// Include all discovery modules

$include_dir = "includes/discovery/dbm";
include("includes/include-dir.inc.php");

if ($debug) { print_vars($valid['sensor']['dbm']); }

check_valid_sensors($device, 'dbm', $valid['sensor']);

echo("\n");

?>
