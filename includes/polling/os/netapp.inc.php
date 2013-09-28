<?php

// NETAPP-MIB::productType.0 pciBased
// NETAPP-MIB::productVersion.0 NetApp Release 7.3.6P2: Wed Sep 14 01:39:26 PDT 2011
// NETAPP-MIB::productId.0 0101206979
// NETAPP-MIB::productVendor.0 netapp
// NETAPP-MIB::productModel.0 FAS3020
// NETAPP-MIB::productFirmwareVersion.0 CFE 3.1.0
// NETAPP-MIB::productGuiUrl.0 https://:443/na_admin
// NETAPP-MIB::productApiUrl.0 https://:443/servlets/netapp.servlets.admin.XMLrequest_filer
// NETAPP-MIB::productSerialNum.0 XXXXXXXXXXX
// NETAPP-MIB::productPartnerSerialNum.0 not applicable
// NETAPP-MIB::productCPUArch.0 x86
// NETAPP-MIB::productTrapData.0 Trap variable currrently unsused.
// NETAPP-MIB::productMachineType.0 FAS3020

/// FIXME: Someone do this in regext. :)

list(,, $version) = explode(" ", $poll_device['sysDescr']);
list($version) = explode(":", $version);

$hardware = snmp_get($device, "NETAPP-MIB::productModel.0", "-Osqv", "NETAPP-MIB", mib_dirs(array("netapp")));
$serial   = snmp_get($device, "NETAPP-MIB::productSerialNum.0", "-Osqv", "NETAPP-MIB", mib_dirs(array("netapp")));
$firmware = snmp_get($device, "NETAPP-MIB::productFirmwareVersion.0", "-Osqv", "NETAPP-MIB", mib_dirs(array("netapp")));
$features = snmp_get($device, "NETAPP-MIB::productCPUArch.0", "-Osqv", "NETAPP-MIB", mib_dirs(array("netapp")));

// 64-bit counters. We don't support the legacy 32-bit counters and their high-low maths.
//
// misc64NfsOps.0 = 22970088164
// misc64CifsOps.0 = 106806017
// misc64HttpOps.0 = 0
// misc64NetRcvdBytes.0 = 136780925422179
// misc64NetSentBytes.0 = 187136027544040
// misc64DiskReadBytes.0 = 449307535990784
// misc64DiskWriteBytes.0 = 247258801713152
// misc64TapeReadBytes.0 = 0
// misc64TapeWriteBytes.0 = 0

$rrd_filename   = $host_rrd . "/netapp_stats.rrd";

$rrd_create = "  \
     DS:iscsi_ops:COUNTER:600:0:10000000000 \
     DS:fcp_ops:COUNTER:600:0:10000000000 \
     DS:nfs_ops:COUNTER:600:0:10000000000 \
     DS:cifs_ops:COUNTER:600:0:10000000000 \
     DS:http_ops:COUNTER:600:0:10000000000 \
     DS:net_rx:COUNTER:600:0:10000000000 \
     DS:net_tx:COUNTER:600:0:10000000000 \
     DS:disk_rd:COUNTER:600:0:10000000000 \
     DS:disk_wr:COUNTER:600:0:10000000000 \
     DS:tape_rd:COUNTER:600:0:10000000000 \
     DS:tape_wr:COUNTER:600:0:10000000000 ";

$snmpdata = snmp_get_multi($device, "iscsi64Ops.0 fcp64Ops.0 misc64NfsOps.0 misc64CifsOps.0 misc64HttpOps.0 misc64NetRcvdBytes.0 misc64NetSentBytes.0 misc64DiskReadBytes.0 misc64DiskWriteBytes.0 misc64TapeReadBytes.0 misc64TapeWriteBytes.0", "-OQUs", "NETAPP-MIB", mib_dirs(array("netapp")));

  if (!is_file($rrd_filename))
  {
    // Create the rrd file if it doesn't exist
    rrdtool_create($rrd_filename, $rrd_create);
  }
  rrdtool_update($rrd_filename, array($snmpdata[0]['iscsi64Ops'], $snmpdata[0]['fcp64Ops'], $snmpdata[0]['misc64NfsOps'], $snmpdata[0]['misc64CifsOps'], $snmpdata[0]['misc64HttpOps'], $snmpdata[0]['misc64NetRcvdBytes'], $snmpdata[0]['misc64NetSentBytes'], $snmpdata[0]['misc64DiskReadBytes'], $snmpdata[0]['misc64DiskWriteBytes'], $snmpdata[0]['misc64TapeReadBytes'], $snmpdata[0]['misc64TapeWriteBytes']));

  $graphs['netapp_ops'] = TRUE;
  $graphs['netapp_disk_io'] = TRUE;
  $graphs['netapp_net_io'] = TRUE;
  $graphs['netapp_tape_io'] = TRUE;

  unset($snmpdata); unset($rrd_filename); unset($rrd_create);

?>
