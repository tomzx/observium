<?php

# We are interrested in equallogic group members (devices), not in the group
# find group member id.

$eqlgrpmembers = snmp_walk($device, "eqlMemberName", "-OsqU", "EQLMEMBER-MIB", mib_dirs("equallogic"));
$eqlgrpmembers = explode("\n", $eqlgrpmembers);

# eqlMemberName.1.443914937 hostname-1
# eqlMemberName.1.1664046123 hostname-2

foreach($eqlgrpmembers as $eqlgrpmem)
{
  # find member id and name in results
  preg_match('/^eqlMemberName\.[0-9]+\.([0-9]+) (.*)$/', $eqlgrpmem, $store);
  if ( (isset($store[2])) && (strtolower($store[2]) == $poll_device['sysName']) )
  {
    $eqlgrpmemid = $store[1];
  }
}

if (isset($eqlgrpmemid))
{
  # store member id when detected
  set_dev_attrib($device, "eqlgrpmemid", $eqlgrpmemid);
  print("\neqlgrpmemid: $eqlgrpmemid\n");
} else {
  # fall-back to old method.
  $eqlgrpmemid = snmp_get($device, "eqliscsiLocalMemberId.0", "-OQv", "EQLVOLUME-MIB", mib_dirs("equallogic"));
}

# EQLMEMBER-MIB::eqlMemberProductFamily.1.$eqlgrpmemid = STRING: PS6500
# EQLMEMBER-MIB::eqlMemberControllerMajorVersion.1.$eqlgrpmemid = Gauge32: 6
# EQLMEMBER-MIB::eqlMemberControllerMinorVersion.1.$eqlgrpmemid = Gauge32: 0
# EQLMEMBER-MIB::eqlMemberControllerMaintenanceVersion.1.$eqlgrpmemid = Gauge32: 2
# EQLMEMBER-MIB::eqlMemberSerialNumber.1.$eqlgrpmemid = STRING: XXXNNNNNNNXNNNN
# EQLMEMBER-MIB::eqlMemberServiceTag.1.$eqlgrpmemid = STRING: XXXXXXX

$hardware = "Dell EqualLogic ".trim(snmp_get($device, "eqlMemberProductFamily.1.".$eqlgrpmemid, "-OQv", "EQLMEMBER-MIB", mib_dirs("equallogic")),'" ');

$serial = trim(snmp_get($device, "eqlMemberSerialNumber.1.".$eqlgrpmemid, "-OQv", "EQLMEMBER-MIB", mib_dirs("equallogic")),'" ');
$serial .= ' ['.trim(snmp_get($device, "eqlMemberServiceTag.1.".$eqlgrpmemid, "-OQv", "EQLMEMBER-MIB", mib_dirs("equallogic")),'" ').']';

$eqlmajor = snmp_get($device, "eqlMemberControllerMajorVersion.1.".$eqlgrpmemid, "-OQv", "EQLMEMBER-MIB", mib_dirs("equallogic"));
$eqlminor = snmp_get($device, "eqlMemberControllerMinorVersion.1.".$eqlgrpmemid, "-OQv", "EQLMEMBER-MIB", mib_dirs("equallogic"));
$eqlmaint = snmp_get($device, "eqlMemberControllerMaintenanceVersion.1.".$eqlgrpmemid, "-OQv", "EQLMEMBER-MIB", mib_dirs("equallogic"));
$version = sprintf("V%d.%d.%d",$eqlmajor, $eqlminor, $eqlmaint);

unset($eqlmajor, $eqlminor, $eqlmaint);

#$software
#$features

unset($eqlgrpmemid,$eqlgrpmembers,$eqlgrpmem,$store);

?>
