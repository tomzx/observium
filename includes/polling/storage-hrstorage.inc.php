<?php

// HOST-RESOURCES-MIB - Storage Objects

if (!is_array($storage_cache['hrstorage']))
{
  $storage_cache['hrstorage'] = snmpwalk_cache_oid($device, "hrStorageEntry", NULL, "HOST-RESOURCES-MIB:HOST-RESOURCES-TYPES");
  if ($debug) { print_r($storage_cache); }
}

$entry = $storage_cache['hrstorage'][$storage[storage_index]];

$storage['units'] = $entry['hrStorageAllocationUnits'];
$storage['used']  = snmp_dewrap32bit($entry['hrStorageUsed']) * $storage['units'];
$storage['size']  = snmp_dewrap32bit($entry['hrStorageSize']) * $storage['units'];
$storage['free']  = $storage['size'] - $storage['used'];

?>
