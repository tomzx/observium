<?php

if ($device['os'] == "linux" || $device['os'] == "endian")
{
  list(,,$version) = explode (" ", $poll_device['sysDescr']);
  $hardware = rewrite_unix_hardware($poll_device['sysDescr']);

  $kernel = $version;

  // Distro "extend" support
  $features = snmp_get($device, ".1.3.6.1.4.1.2021.7890.1.3.1.1.6.100.105.115.116.114.111", "-Oqv", "UCD-SNMP-MIB");
  $features = str_replace("\"", "", $features);

  if (!$features) // No "extend" support, try "exec" support
  {
    $features = snmp_get($device, ".1.3.6.1.4.1.2021.7890.1.101.1", "-Oqv", "UCD-SNMP-MIB");
    $features = str_replace("\"", "", $features);
  }

  // Unset features if we're just getting an error.
  if(strpos($features, "/usr/bin/distro")) { unset($features); }

  list($distro, $distro_ver) = explode(" ", $features);
  unset($features);



  // Detect Dell hardware via OpenManage SNMP
  $hw = snmp_get($device, ".1.3.6.1.4.1.674.10892.1.300.10.1.9.1", "-Oqv", "MIB-Dell-10892");
  $hw = trim(str_replace("\"", "", $hw));
  if ($hw) { $hardware = "Dell " . $hw; }

  $serial = snmp_get($device, ".1.3.6.1.4.1.674.10892.1.300.10.1.11.1", "-Oqv", "MIB-Dell-10892");
  $serial = trim(str_replace("\"", "", $serial));

  // Use agent DMI data if available
  if (isset($agent_data['dmi']))
  {
    if ($agent_data['dmi']['system-product-name'])
    {
      $hardware = ($agent_data['dmi']['system-manufacturer'] ? $agent_data['dmi']['system-manufacturer'] . ' ' : '') . $agent_data['dmi']['system-product-name'];

      // Clean up "Dell Computer Corporation" and "Intel Corporation"
      $hardware = str_replace(" Computer Corporation","",$hardware);
      $hardware = str_replace(" Corporation","",$hardware);
    }

    if ($agent_data['dmi']['system-serial-number'])
    {
      $serial = $agent_data['dmi']['system-serial-number'];
    }
  }

}
elseif ($device['os'] == "aix")
{
  list($hardware,,$os_detail,) = explode("\n", $poll_device['sysDescr']);
  list(,$version) = explode(":", $os_detail);

  echo("\n\nHardware: $hardware, Version: $version\n\n");

}
elseif ($device['os'] == "freebsd")
{
  preg_match('/FreeBSD ([\d\.]+-[\w\d-]+)/i', $poll_device['sysDescr'], $matches);
  $kernel = $matches[1];
  $hardware = rewrite_unix_hardware($poll_device['sysDescr']);
}
elseif ($device['os'] == "dragonfly")
{
  list(,,$version,,,$features) = explode (" ", $poll_device['sysDescr']);
  $hardware = rewrite_unix_hardware($poll_device['sysDescr']);
}
elseif ($device['os'] == "netbsd")
{
  list(,,$version,,,$features) = explode (" ", $poll_device['sysDescr']);
  $features = str_replace("(", "", $features);
  $features = str_replace(")", "", $features);
  $hardware = rewrite_unix_hardware($poll_device['sysDescr']);
}
elseif ($device['os'] == "openbsd" || $device['os'] == "solaris" || $device['os'] == "opensolaris")
{
  list(,,$version,$features) = explode (" ", $poll_device['sysDescr']);
  $features = str_replace("(", "", $features);
  $features = str_replace(")", "", $features);
  $hardware = rewrite_unix_hardware($poll_device['sysDescr']);
}
elseif ($device['os'] == "monowall" || $device['os'] == "pfsense" || $device['os'] == "Voswall")
{
  list(,,$version,,, $kernel) = explode(" ", $poll_device['sysDescr']);
  $distro = $device['os'];
  $hardware = rewrite_unix_hardware($poll_device['sysDescr']);
}
elseif ($device['os'] == "freenas" || $device['os'] == "nas4free")
{
  preg_match('/Software: FreeBSD ([\d\.]+-[\w\d-]+)/i', $poll_device['sysDescr'], $matches);
  $version = $matches[1];
  $hardware = rewrite_unix_hardware($poll_device['sysDescr']);
}
elseif ($device['os'] == "qnap")
{
  $hardware = snmp_get($device, "ENTITY-MIB::entPhysicalName.1", "-Osqnv");
  $version  = snmp_get($device, "ENTITY-MIB::entPhysicalFirmwareRev.1", "-Osqnv");
  $serial   = snmp_get($device, "ENTITY-MIB::entPhysicalSerial.1", "-Osqnv");
}
elseif ($device['os'] == "dsm")
{
//  This only gets us the build, not the actual version number, so won't use this.. yet.
//  list(,,,$version,) = explode(" ",$poll_device['sysDescr'],5);
//  $version = "Build " . trim($version,'#');

  $hrSystemInitialLoadParameters = trim(snmp_get($device, "hrSystemInitialLoadParameters.0", "-Osqnv"));

  $options = explode(" ",$hrSystemInitialLoadParameters);

  foreach ($options as $option)
  {
    list($key,$value) = explode("=",$option,2);
    if ($key == "syno_hw_version")
    {
      $hardware = $value;
    }
  }
}

// 'os' script data via SNMP "exec" support
$os_data = str_replace('"', "", snmp_get($device, ".1.3.6.1.4.1.2021.36602.1.1.1.4.1.2.2.111.115.1", "-Oqv"));

// check if we got a SCRIPTVER back
if(strpos($os_data, "SCRIPTVER"))
{
  foreach(explode("||", $os_data) as $part)
  {
    list($a, $b) = explode("=", $part);
    $stats['os'][$a] = $b;
  }

  print_vars($stats);

  $distro     = $stats['os']['DISTRO'];
  $distro_ver = $stats['os']['DISTROVER'];
  $kernel     = $stats['os']['KERNEL'];
  $arch       = $stats['os']['ARCH'];

  unset($features);

  #$hardware   = 'Generic '.$arch;

}


?>
