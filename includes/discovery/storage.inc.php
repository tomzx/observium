<?php

echo("Storage : ");

// Include all discovery modules

$include_dir = "includes/discovery/storage";
include("includes/include-dir.inc.php");

if ($debug) { print_r($valid_storage); }

// Remove storage which weren't redetected here
$query = 'SELECT * FROM `storage` WHERE `device_id` = ?';

foreach(dbFetchRows($query, array($device['device_id'])) as $test_storage)
{
  $storage_index = $test_storage['storage_index'];
  $storage_mib = $test_storage['storage_mib'];
  if ($debug) { echo($storage_index . " -> " . $storage_mib . "\n"); }

  if (!$valid_storage[$storage_mib][$storage_index])
  {
    dbDelete('storage', 'storage_id = ?', array($test_storage['storage_id']));
    echo("-");
  }
}

unset($valid_storage);
echo("\n");

?>
