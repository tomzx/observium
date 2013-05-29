<?php

# We are interrested in equallogic group members (devices), not in the group
# find group member id.

$eqlgrpmemid = snmp_get($device, "eqliscsiLocalMemberId.0", "-OQv", "EQLVOLUME-MIB", mib_dirs("equallogic"));

# EQLMEMBER-MIB::eqlMemberProductFamily.1.$eqlgrpmemid = STRING: PS6500
# EQLMEMBER-MIB::eqlMemberControllerMajorVersion.1.$eqlgrpmemid = Gauge32: 6
# EQLMEMBER-MIB::eqlMemberControllerMinorVersion.1.$eqlgrpmemid = Gauge32: 0
# EQLMEMBER-MIB::eqlMemberControllerMaintenanceVersion.1.$eqlgrpmemid = Gauge32: 2
# EQLMEMBER-MIB::eqlMemberSerialNumber.1.$eqlgrpmemid = STRING: XXXNNNNNNNXNNNN

$hardware = "Dell EqualLogic ".trim(snmp_get($device, "eqlMemberProductFamily.1.".$eqlgrpmemid, "-OQv", "EQLMEMBER-MIB", mib_dirs("equallogic")),'" ');

$serial = trim(snmp_get($device, "eqlMemberSerialNumber.1.".$eqlgrpmemid, "-OQv", "EQLMEMBER-MIB", mib_dirs("equallogic")),'" ');

$eqlmajor = snmp_get($device, "eqlMemberControllerMajorVersion.1.".$eqlgrpmemid, "-OQv", "EQLMEMBER-MIB", mib_dirs("equallogic"));
$eqlminor = snmp_get($device, "eqlMemberControllerMinorVersion.1.".$eqlgrpmemid, "-OQv", "EQLMEMBER-MIB", mib_dirs("equallogic"));
$eqlmaint = snmp_get($device, "eqlMemberControllerMaintenanceVersion.1.".$eqlgrpmemid, "-OQv", "EQLMEMBER-MIB", mib_dirs("equallogic"));
$version = sprintf("V%d.%d.%d",$eqlmajor, $eqlminor, $eqlmaint);

unset($eqlmajor, $eqlminor, $eqlmaint);

#$software
#$features

unset($eqlgrpmemid);

?>
