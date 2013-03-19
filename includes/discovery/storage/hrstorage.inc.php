<?php

$hrstorage_array = snmpwalk_cache_oid($device, "hrStorageEntry", NULL, "HOST-RESOURCES-MIB:HOST-RESOURCES-TYPES:NetWare-Host-Ext-MIB");

if (is_array($hrstorage_array))
{
  //64bit counters
  $dsk_array = snmpwalk_cache_oid($device, 'dskEntry', NULL, 'UCD-SNMP-MIB');

  echo("hrStorage : ");
  foreach ($hrstorage_array as $index => $storage)
  {
    $fstype = $storage['hrStorageType'];
    $descr = $storage['hrStorageDescr'];
    $units = $storage['hrStorageAllocationUnits'];

    switch($fstype)
    {
      case 'hrStorageVirtualMemory':
      case 'hrStorageRam';
      case 'hrStorageOther';
      case 'nwhrStorageDOSMemory';
      case 'nwhrStorageMemoryAlloc';
      case 'nwhrStorageMemoryPermanent';
      case 'nwhrStorageMemoryAlloc';
      case 'nwhrStorageCacheBuffers';
      case 'nwhrStorageCacheMovable';
      case 'nwhrStorageCacheNonMovable';
      case 'nwhrStorageCodeAndDataMemory';
      case 'nwhrStorageDOSMemory';
      case 'nwhrStorageIOEngineMemory';
      case 'nwhrStorageMSEngineMemory';
      case 'nwhrStorageUnclaimedMemory';
        $deny = 1;
        break;
    }

    foreach ($config['ignore_mount'] as $bi) { if ($bi == $descr) { $deny = 1; if ($debug) echo("$bi == $descr \n"); } }
    foreach ($config['ignore_mount_string'] as $bi) { if (strpos($descr, $bi) !== FALSE)     { $deny = 1; if ($debug) echo("strpos: $descr, $bi \n"); } }
    foreach ($config['ignore_mount_regexp'] as $bi) { if (preg_match($bi, $descr) > "0") { $deny = 1; if ($debug) echo("preg_match $bi, $descr \n"); } }

    if (isset($config['ignore_mount_removable']) && $config['ignore_mount_removable'] && $fstype == "hrStorageRemovableDisk") { $deny = 1; if ($debug) echo("skip(removable)\n"); }
    if (isset($config['ignore_mount_network']) && $config['ignore_mount_network'] && $fstype == "hrStorageNetworkDisk") { $deny = 1; if ($debug) echo("skip(network)\n"); }
    if (isset($config['ignore_mount_optical']) && $config['ignore_mount_optical'] && $fstype == "hrStorageCompactDisc") { $deny = 1; if ($debug) echo("skip(cd)\n"); }

    if(!$deny)
    {
      //32bit counters
      $size = snmp_dewrap32bit($storage['hrStorageSize']) * $units;
      $used = snmp_dewrap32bit($storage['hrStorageUsed']) * $units;

      // hrStorageDescr.8 = /mnt/Media, type: zfs, dev: Media
      // hrStorageDescr.31 = /
      list($path) = explode(',', $descr);
  
      // Find index from 'dskTable'
      foreach($dsk_array as $dsk)
      {
        if ($dsk['dskPath'] === $path)
        {
          //Using 64bit counters if available
          if(isset($dsk['dskTotalLow']))
          {
            $size = $dsk['dskTotalLow'] * $units;
            $used = $dsk['dskUsedLow'] * $units;
          }
          break;
        }
      }
      $percent = round($used / $size * 100);
    }
    
    if (!$deny && is_numeric($index))
    {
      discover_storage($valid_storage, $device, $index, $fstype, "hrstorage", $descr, $size , $units, $used, $free, $percent);
    }

    unset($deny, $fstype, $descr, $size, $used, $units, $path, $dsk, $storage_rrd, $old_storage_rrd);
  }
  unset($hrstorage_array, $dsk_array);
}

?>
