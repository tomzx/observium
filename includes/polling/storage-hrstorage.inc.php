<?php

// HOST-RESOURCES-MIB - Storage Objects

if (!is_array($storage_cache['hrstorage']))
{
  $storage_cache['hrstorage'] = snmpwalk_cache_oid($device, "hrStorageEntry", NULL, "HOST-RESOURCES-MIB:HOST-RESOURCES-TYPES");
  if ($debug) { print_vars($storage_cache); }
}

if (is_array($storage_cache['hrstorage']) && !is_array($storage_cache['dsk']))
{
  $storage_cache['dsk'] = snmpwalk_cache_oid($device, 'dskEntry', NULL, 'UCD-SNMP-MIB');
}

$entry = $storage_cache['hrstorage'][$storage['storage_index']];

$storage['units'] = $entry['hrStorageAllocationUnits'];
$storage['used']  = snmp_dewrap32bit($entry['hrStorageUsed']) * $storage['units'];
$storage['size']  = snmp_dewrap32bit($entry['hrStorageSize']) * $storage['units'];

// hrStorageDescr.8 = /mnt/Media, type: zfs, dev: Media
// hrStorageDescr.31 = /
list($path) = explode(',', $storage['storage_descr']);

// Find index from 'dskTable'
foreach($storage_cache['dsk'] as $dsk)
{
  if ($dsk['dskPath'] === $path)
  {
    //Using 64bit counters if available
    if(isset($dsk['dskTotalLow']))
    {
      $storage['size'] = $dsk['dskTotalLow'] * 1024;
      $storage['used'] = $dsk['dskUsedLow'] * 1024;
    }
    break;
  }
}

$storage['free']  = $storage['size'] - $storage['used'];

?>
