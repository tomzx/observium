<?php

/////////////////////////////////////////////////////////
//  NO CHANGES TO THIS FILE, IT IS NOT USER-EDITABLE   //
/////////////////////////////////////////////////////////
//               YES, THAT MEANS YOU                   //
/////////////////////////////////////////////////////////

// Debugging Include. This isn't in SVN.
if($debug && file_exists($config['install_dir']."/includes/ref.inc.php")) { include_once($config['install_dir']."/includes/ref.inc.php"); }

// Alert Graphs
## FIXME - this is ugly

$config['alert_graphs']['port']['ifInOctets_rate']  = array('type' => 'port_bits', 'id' => '@port_id');
$config['alert_graphs']['port']['ifOutOctets_rate'] = array('type' => 'port_bits', 'id' => '@port_id');
$config['alert_graphs']['port']['ifInOctets_perc']  = array('type' => 'port_bits', 'id' => '@port_id');
$config['alert_graphs']['port']['ifOutOctets_perc'] = array('type' => 'port_bits', 'id' => '@port_id');
$config['alert_graphs']['mempool']['mempool_perc']  = array('type' => 'mempool_usage', 'id' => '@mempool_id');
$config['alert_graphs']['sensor']['sensor_value']   = array('type' => 'sensor_graph', 'id' => '@sensor_id');
$config['alert_graphs']['mempool']['processor_usage']  = array('type' => 'processor_usage', 'id' => '@processor_id');

$config['os']['default']['over'][0]['graph']       = "device_processor";
$config['os']['default']['over'][0]['text']        = "Processors";
$config['os']['default']['over'][1]['graph']       = "device_mempool";
$config['os']['default']['over'][1]['text']        = "Memory";

$os_group = "unix";
$config['os_group'][$os_group]['type']              = "server";
$config['os_group'][$os_group]['processor_stacked'] = 1;
$config['os_group'][$os_group]['over'][0]['graph']  = "device_processor";
$config['os_group'][$os_group]['over'][0]['text']   = "Processors";
$config['os_group'][$os_group]['over'][1]['graph']  = "device_ucd_memory";
$config['os_group'][$os_group]['over'][1]['text']   = "Memory";

$os = "generic";
$config['os'][$os]['text']              = "Generic Device";

$os = "vyatta";
$config['os'][$os]['text']              = "Vyatta";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['ifname']            = 1;
$config['os'][$os]['snmp']['max-rep']   = 100;
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "Processors";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";

// Linux-based OSes here please.

$os = "linux";
$config['os'][$os]['type']              = "server";
$config['os'][$os]['group']             = "unix";
$config['os'][$os]['text']              = "Linux";
$config['os'][$os]['ifXmcbc']           = 1;
$config['os'][$os]['snmp']['max-rep']   = 100;
$config['os']['linux']['over']['0'] = array('text' => 'CPU Load',      'graph' =>  "device_processor");
$config['os']['linux']['over']['1'] = array('text' => 'Memory',  'graph' =>  "device_ucd_memory");
$config['os']['linux']['over']['2'] = array('text' => 'Storage', 'graph' =>  "device_storage");
$config['os']['linux']['over']['3'] = array('text' => 'Traffic', 'graph' =>  "device_bits");

$os = "qnap";
$config['os'][$os]['type']              = "storage";
$config['os'][$os]['group']             = "unix";
$config['os'][$os]['text']              = "QNAP TurboNAS";
$config['os'][$os]['ifXmcbc']           = 1;

$os = "endian";
$config['os'][$os]['text']              = "Endian";
$config['os'][$os]['type']              = "firewall";
$config['os'][$os]['group']             = "unix";
$config['os'][$os]['ifname']            = 1;
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "Processors";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";

$os = "ciscosmblinux";
$config['os'][$os]['type']              = "wireless";
$config['os'][$os]['group']             = "unix";
$config['os'][$os]['text']              = "Cisco SMB Linux";
$config['os'][$os]['icon']              = "cisco";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "Processors";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";

// Other Unix-based OSes here please.

$os = "freebsd";
$config['os'][$os]['type']              = "server";
$config['os'][$os]['group']             = "unix";
$config['os'][$os]['text']              = "FreeBSD";

$os = "openbsd";
$config['os'][$os]['type']              = "server";
$config['os'][$os]['group']             = "unix";
$config['os'][$os]['text']              = "OpenBSD";

$os = "netbsd";
$config['os'][$os]['type']              = "server";
$config['os'][$os]['group']             = "unix";
$config['os'][$os]['text']              = "NetBSD";

$os = "dragonfly";
$config['os'][$os]['type']              = "server";
$config['os'][$os]['group']             = "unix";
$config['os'][$os]['text']              = "DragonflyBSD";

$os = "netware";
$config['os'][$os]['type']              = "server";
$config['os'][$os]['text']              = "Novell Netware";
$config['os'][$os]['icon']              = "novell";

$os = "monowall";
$config['os'][$os]['group']             = "unix";
$config['os'][$os]['text']              = "m0n0wall";
$config['os'][$os]['type']              = "firewall";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "Processors";
$config['os'][$os]['over'][2]['graph']  = "device_ucd_memory";
$config['os'][$os]['over'][2]['text']   = "Memory";

$os = "pfsense";
$config['os'][$os]['group']             = "unix";
$config['os'][$os]['text']              = "pfSense";
$config['os'][$os]['type']              = "firewall";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "Processors";
$config['os'][$os]['over'][2]['graph']  = "device_ucd_memory";
$config['os'][$os]['over'][2]['text']   = "Memory";

$os = "freenas";
$config['os'][$os]['group']             = "unix";
$config['os'][$os]['text']              = "FreeNAS";
$config['os'][$os]['type']              = "storage";
$config['os'][$os]['over'][0]['graph']  = "device_processor";
$config['os'][$os]['over'][0]['text']   = "Processors";
$config['os'][$os]['over'][1]['graph']  = "device_ucd_memory";
$config['os'][$os]['over'][1]['text']   = "Memory";
$config['os'][$os]['over'][2]['graph']  = "device_storage";
$config['os'][$os]['over'][2]['text']   = "Storage";

$os = "nas4free";
$config['os'][$os]['group']             = "unix";
$config['os'][$os]['text']              = "NAS4Free";
$config['os'][$os]['type']              = "storage";
$config['os'][$os]['over'][0]['graph']  = "device_processor";
$config['os'][$os]['over'][0]['text']   = "Processors";
$config['os'][$os]['over'][1]['graph']  = "device_ucd_memory";
$config['os'][$os]['over'][1]['text']   = "Memory";
$config['os'][$os]['over'][2]['graph']  = "device_storage";
$config['os'][$os]['over'][2]['text']   = "Storage";

$os = "solaris";
$config['os'][$os]['group']             = "unix";
$config['os'][$os]['text']              = "Sun Solaris";
$config['os'][$os]['type']              = "server";

$os = "aix";
$config['os'][$os]['group']             = "unix";
$config['os'][$os]['text']              = "AIX";
$config['os'][$os]['type']              = "server";
$config['os'][$os]['ifAliasSemicolon']  = TRUE;             // Split on semicolon and take the first element.
$config['os'][$os]['over']['0'] = array('text' => 'CPU Load',      'graph' =>  "device_processor");
$config['os'][$os]['over']['1'] = array('text' => 'Memory',  'graph' =>  "device_ucd_memory");
$config['os'][$os]['over']['2'] = array('text' => 'Storage', 'graph' =>  "device_storage");
$config['os'][$os]['over']['3'] = array('text' => 'Traffic', 'graph' =>  "device_bits");


$os = "adva";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['text']              = "Adva Optical";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";

$os = "opensolaris";
$config['os'][$os]['type']              = "server";
$config['os'][$os]['group']             = "unix";
$config['os'][$os]['text']              = "Sun OpenSolaris";

$os = "openindiana";
$config['os'][$os]['type']              = "server";
$config['os'][$os]['group']             = "unix";
$config['os'][$os]['text']              = "OpenIndiana";

$os = "nexenta";
$config['os'][$os]['type']              = "server";
$config['os'][$os]['group']             = "unix";
$config['os'][$os]['text']              = "NexentaOS";

$os = "equallogic";
$config['os'][$os]['type']              = "storage";
$config['os'][$os]['group']             = "unix";
$config['os'][$os]['icon']              = "dell";
$config['os'][$os]['text']              = "Storage Array Firmware";

// Alcatel

$os = "aos";
$config['os'][$os]['group']             = "aos";
$config['os'][$os]['text']              = "Alcatel-Lucent OS";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['ifXmcbc']           = 1;
$config['os'][$os]['ifname']            = 1;
$config['os'][$os]['snmp']['max-rep']   = 100;
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['icon']              = "alcatellucent";

$os = "timos";
$config['os'][$os]['group']             = "timos";
$config['os'][$os]['text']              = "Alcatel-Lucent TimOS";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['ifXmcbc']           = 1;
$config['os'][$os]['snmp']['max-rep']   = 100;
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['icon']              = "alcatellucent";

// Cisco

$os = "ios";
$config['os'][$os]['group']             = "cisco";
$config['os'][$os]['text']              = "Cisco IOS";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['ifXmcbc']           = 1;
$config['os'][$os]['snmp']['max-rep']   = 100;
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";
$config['os'][$os]['icon']              = "cisco";

$os = "acsw";
#$config['os'][$os]['group']            = "cisco";
$config['os'][$os]['text']              = "Cisco ACE";
$config['os'][$os]['ifname']            = 1;
$config['os'][$os]['type']              = "loadbalancer";
$config['os'][$os]['icon']              = "cisco";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";

$os = "cat1900";
$config['os'][$os]['group']             = "cat1900";
$config['os'][$os]['text']              = "Cisco Catalyst 1900";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "cisco-old";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";

$os = "iosxe";
$config['os'][$os]['group']             = "cisco";
$config['os'][$os]['text']              = "Cisco IOS-XE";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['ifXmcbc']           = 1;
# $config['os'][$os]['snmp']['max-rep']   = 100;
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";
$config['os'][$os]['icon']              = "cisco";

$os = "iosxr";
$config['os'][$os]['group']             = "cisco";
$config['os'][$os]['text']              = "Cisco IOS-XR";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['ifXmcbc']           = 1;
$config['os'][$os]['icon']              = "cisco";
$config['os'][$os]['snmp']['max-rep']   = 100;
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";

$os = "asa";
$config['os'][$os]['group']             = "cisco";
$config['os'][$os]['text']              = "Cisco ASA";
$config['os'][$os]['ifname']            = 1;
$config['os'][$os]['type']              = "firewall";
$config['os'][$os]['icon']              = "cisco";
$config['os'][$os]['snmp']['max-rep']   = 100;
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";

$os = "pixos";
$config['os'][$os]['group']             = "cisco";
$config['os'][$os]['text']              = "Cisco PIX-OS";
$config['os'][$os]['ifname']            = 1;
$config['os'][$os]['type']              = "firewall";
$config['os'][$os]['icon']              = "cisco";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";

$os = "nxos";
$config['os'][$os]['group']             = "cisco";
$config['os'][$os]['text']              = "Cisco NX-OS";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "cisco";
$config['os'][$os]['snmp']['max-rep']   = 100;
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";

$os = "sanos";
$config['os'][$os]['group']             = "cisco";
$config['os'][$os]['text']              = "Cisco SAN-OS";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "cisco";
$config['os'][$os]['snmp']['max-rep']   = 100;
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";

$os = "catos";
$config['os'][$os]['group']             = "cisco";
$config['os'][$os]['text']              = "Cisco CatOS";
$config['os'][$os]['ifname']            = 1;
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "cisco-old";
$config['os'][$os]['snmp']['max-rep']   = 20;
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";

$os = "wlc";
$config['os'][$os]['text']              = "Cisco WLC";
$config['os'][$os]['type']              = "wireless";
$config['os'][$os]['ifname']            = 1;
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";
$config['os'][$os]['icon']              = "cisco";

// Cisco IronPort

$os = "asyncos";
$config['os'][$os]['group']             = "cisco";
$config['os'][$os]['text']              = "Cisco IronPort";
$config['os'][$os]['type']              = "server";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
$config['os'][$os]['icon']              = "cisco";

// Cisco Small Business (Linksys)

$os = "ciscosb";
$config['os'][$os]['group']             = "cisco";
$config['os'][$os]['text']              = "Cisco Small Business";
$config['os'][$os]['ifname']            = 1;
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "linksys";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";

// Cisco Service Control OS / SCE

$os = "ciscoscos";
$config['os'][$os]['group']             = "cisco";
$config['os'][$os]['text']              = "Cisco Service Control OS";
$config['os'][$os]['ifname']            = 1;
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "cisco";
$config['os'][$os]['snmp']['max-rep']   = 100;
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";

// Huawei

$os = "vrp";
$config['os'][$os]['group']             = "vrp";
$config['os'][$os]['text']              = "Huawei VRP";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "huawei";

// ZTE

$os = "zxr10";
$config['os'][$os]['group']             = "zxr10";
$config['os'][$os]['text']              = "ZTE ZXR10";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "zte";

// Korenix

$os = "korenix-jetnet";
$config['os'][$os]['text']              = "Korenix Jetnet";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "korenix";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";


// Supermicro Switch

$os = "supermicro-switch";
$config['os'][$os]['group']             = "supermicro";
$config['os'][$os]['text']              = "Supermicro Switch";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "supermicro";
$config['os'][$os]['ifname']            = 1;

// Juniper

$os = "junos";
$config['os'][$os]['text']              = "Juniper JunOS";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "juniper";
$config['os'][$os]['snmp']['max-rep']   = 50;
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";
$config['os'][$os]['discovery_blacklist'] = array('entity-sensor', 'entity-physical');

$os = "junose";
$config['os'][$os]['text']              = "Juniper JunOSe";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "juniper";
$config['os'][$os]['snmp']['max-rep']   = 50;
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";

$os = "jwos";
$config['os'][$os]['text']              = "Juniper JWOS";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "juniper";

$os = "screenos";
$config['os'][$os]['text']              = "Juniper ScreenOS";
$config['os'][$os]['type']              = "firewall";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";

$os = "juniperive";
$config['os'][$os]['text']              = "Juniper IVE";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "juniper";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";

// Fortinet

$os = "fortigate";
$config['os'][$os]['text']              = "Fortinet Fortigate";
$config['os'][$os]['type']              = "firewall";
$config['os'][$os]['icon']              = "fortinet";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_fortigate_cpu";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";

// Ciena

$os = "ciena";
$config['os'][$os]['text']              = "SAOS";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "ciena";
$config['os'][$os]['ifXmcbc']           = 1;
$config['os'][$os]['ifname']            = 1;
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Device Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory Usage";

// Mikrotik

$os = "routeros";
$config['os'][$os]['text']              = "Mikrotik RouterOS";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['nobulk']            = 1;
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";

// Brocade / Foundry

$os = "ironware";
$config['os'][$os]['text']              = "Brocade IronWare";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "brocade";
$config['os'][$os]['snmp']['max-rep']   = 100;
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";

$os = "fabos";
$config['os'][$os]['text']              = "Brocade FabricOS";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "brocade";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";

$os = "nos";
$config['os'][$os]['text']              = "Brocade NOS";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "brocade";
$config['os'][$os]['ifname']            = 1;
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";

// Extreme

$os = "xos";
$config['os'][$os]['text']              = "Extreme XOS";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['ifname']            = 1;
$config['os'][$os]['group']             = "extremeware";
$config['os'][$os]['snmp']['max-rep']   = 100;
$config['os'][$os]['icon']              = "extreme";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
#$config['os'][$os]['over'][1]['graph']  = "device_processor";
#$config['os'][$os]['over'][1]['text']   = "CPU Usage";
#$config['os'][$os]['over'][2]['graph']  = "device_mempool";
#$config['os'][$os]['over'][2]['text']   = "Memory";

$os = "extremeware";
$config['os'][$os]['text']              = "Extremeware";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['ifname']            = 1;
$config['os'][$os]['icon']              = "extreme";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";

// Bluecoat

$os = "packetshaper";
$config['os'][$os]['text']              = "Blue Coat Packetshaper";
$config['os'][$os]['type']              = "network";

// Force 10

$os = "ftos";
$config['os'][$os]['text']              = "Force10 FTOS";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "force10";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";

// Avaya

$os = "avaya-ers";
$config['os'][$os]['text']              = "ERS Firmware";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "avaya";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";

// Arista

$os = "arista_eos";
$config['os'][$os]['text']              = "Arista EOS";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "arista";
$config['os'][$os]['snmp']['max-rep']   = 100;
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";

// Citrix

$os = "netscaler";
$config['os'][$os]['text']              = "Citrix Netscaler";
$config['os'][$os]['type']              = "loadbalancer";
$config['os'][$os]['icon']              = "citrix";
$config['os'][$os]['snmp']['max-rep']   = 100;
$config['os'][$os]['over'][0]['graph']  = "device_netscaler_tcp_conn";
$config['os'][$os]['over'][0]['text']   = "TCP Connections";
$config['os'][$os]['over'][1]['graph']  = "device_bits";
$config['os'][$os]['over'][1]['text']   = "Traffic";
$config['os'][$os]['over'][2]['graph']  = "device_processor";
$config['os'][$os]['over'][2]['text']   = "CPU Usage";

// F5

$os = "f5";
$config['os'][$os]['text']              = "F5 BIG-IP";
$config['os'][$os]['type']              = "loadbalancer";
$config['os'][$os]['icon']              = "f5";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";

// Proxim

$os = "proxim";
$config['os'][$os]['text']              = "Proxim";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "proxim";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";

// Dell

$os = "powerconnect";
$config['os'][$os]['text']              = "Dell PowerConnect";
$config['os'][$os]['ifname']            = 1;
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "dell";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";

$os = "radlan";
$config['os'][$os]['text']              = "Radlan";
$config['os'][$os]['ifname']            = 1;
$config['os'][$os]['type']              = "network";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
#$config['os'][$os]['over'][2]['graph']  = "device_mempool";
#$config['os'][$os]['over'][2]['text']   = "Memory";

$os = "powervault";
$config['os'][$os]['text']              = "Dell PowerVault";
$config['os'][$os]['icon']              = "dell";

$os = "drac";
$config['os'][$os]['text']              = "Dell DRAC";
$config['os'][$os]['icon']              = "dell";

// Broadcom

$os = "bcm963";
$config['os'][$os]['text']              = "Broadcom BCM963xx";
$config['os'][$os]['icon']              = "broadcom";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";

// Motorola

$os = "netopia";
$config['os'][$os]['text']              = "Motorola Netopia";
$config['os'][$os]['type']              = "network";

// Tranzeo

$os = "tranzeo";
$config['os'][$os]['text']              = "Tranzeo";
$config['os'][$os]['type']              = "wireless";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";

// D-Link

$os = "dlink";
$config['os'][$os]['text']              = "D-Link Switch";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "dlink";
$config['os'][$os]['ifname']            = 1;
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "Processors";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";

$os = "dlinkap";
$config['os'][$os]['text']              = "D-Link Access Point";
$config['os'][$os]['type']              = "wireless";
$config['os'][$os]['icon']              = "dlink";

// AXIS

$os = "axiscam";
$config['os'][$os]['text']              = "AXIS Network Camera";
$config['os'][$os]['icon']              = "axis";

$os = "axisdocserver";
$config['os'][$os]['text']              = "AXIS Network Document Server";
$config['os'][$os]['icon']              = "axis";

// Gamatronic

$os = "gamatronicups";
$config['os'][$os]['text']              = "Gamatronic UPS Stack";
$config['os'][$os]['type']              = "power";

// Powerware

$os = "powerware";
$config['os'][$os]['text']              = "Powerware UPS";
$config['os'][$os]['type']              = "power";
$config['os'][$os]['icon']              = "eaton";
$config['os'][$os]['over'][0]['graph']  = "device_voltage";
$config['os'][$os]['over'][0]['text']   = "Voltage";
$config['os'][$os]['over'][1]['graph']  = "device_current";
$config['os'][$os]['over'][1]['text']   = "Current";
$config['os'][$os]['over'][2]['graph']  = "device_frequency";
$config['os'][$os]['over'][2]['text']   = "Freq";

// Delta

$os = "deltaups";
$config['os'][$os]['text']              = "Delta UPS";
$config['os'][$os]['type']              = "power";
$config['os'][$os]['icon']              = "delta";

// Liebert

$os = "liebert";
$config['os'][$os]['text']              = "Liebert";
$config['os'][$os]['type']              = "power";
$config['os'][$os]['icon']              = "liebert";

$os = "engenius";
$config['os'][$os]['type']              = "wireless";
$config['os'][$os]['text']              = "EnGenius Access Point";
$config['os'][$os]['icon']              = "engenius";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_wifi_clients";
$config['os'][$os]['over'][1]['text']   = "Wireless clients";

// Apple

$os = "airport";
$config['os'][$os]['type']              = "wireless";
$config['os'][$os]['text']              = "Apple AirPort";
$config['os'][$os]['icon']              = "apple";

// Microsoft

$os = "windows";
$config['os'][$os]['text']              = "Microsoft Windows";
$config['os'][$os]['ifname']            = 1;
$config['os'][$os]['processor_stacked'] = 1;

// Blade Network Technologies

$os = "bnt";
$config['os'][$os]['text']              = "Blade Network Technologies";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "bnt";

// NetAPP

$os = "netapp";
$config['os'][$os]['text']              = "NetApp";
$config['os'][$os]['type']              = "storage";
$config['os'][$os]['icon']              = "netapp";
$config['os'][$os]['snmp']['max-rep']   = 50;
$config['os'][$os]['over'][0]['graph']  = "device_netapp_net_io";
$config['os'][$os]['over'][0]['text']   = "Network Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_netapp_ops";
$config['os'][$os]['over'][1]['text']   = "Operations";
$config['os'][$os]['over'][2]['graph']  = "device_netapp_disk_io";
$config['os'][$os]['over'][2]['text']   = "Disk I/O";

// Arris

$os = "arris-d5";
$config['os'][$os]['text']              = "Arris D5";
$config['os'][$os]['type']              = "video";
$config['os'][$os]['icon']              = "arris";

$os = "arris-c3";
$config['os'][$os]['text']              = "Arris C3";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "arris";


// HP / 3Com

$os = "3com";
$config['os'][$os]['text']              = "3Com OS";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "3com";
$config['os'][$os]['snmp']['max-rep']   = 100;
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";

$os = "procurve";
$config['os'][$os]['text']              = "HP ProCurve";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "hp";
$config['os'][$os]['snmp']['max-rep']   = 100;
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";

$os = "h3c";
$config['os'][$os]['text']              = "H3C Comware";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "h3c";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";

$os = "hh3c";
$config['os'][$os]['text']              = "HP Comware";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "hp";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";

$os = "speedtouch";
$config['os'][$os]['text']              = "Thomson Speedtouch";
$config['os'][$os]['ifname']            = 1;
$config['os'][$os]['type']              = "network";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";

$os = "sonicwall";
$config['os'][$os]['text']              = "SonicWALL";
$config['os'][$os]['type']              = "firewall";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";

$os = "zywall";
$config['os'][$os]['text']              = "ZyXEL ZyWALL";
$config['os'][$os]['type']              = "firewall";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['icon']              = "zyxel";

$os = "prestige";
$config['os'][$os]['text']              = "ZyXEL Prestige";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "zyxel";

$os = "zyxeles";
$config['os'][$os]['text']              = "ZyXEL Ethernet Switch";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "zyxel";

$os = "zyxelnwa";
$config['os'][$os]['text']              = "ZyXEL NWA";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "zyxel";

$os = "ies";
$config['os'][$os]['text']              = "ZyXEL DSLAM";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "zyxel";

$os = "allied";
$config['os'][$os]['text']              = "AlliedWare";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['ifname']            = 1;
$config['os'][$os]['snmp']['max-rep']   = 100;
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";

$os = "mgeups";
$config['os'][$os]['text']              = "MGE UPS";
$config['os'][$os]['group']             = "ups";
$config['os'][$os]['type']              = "power";
$config['os'][$os]['icon']              = "mge";
$config['os'][$os]['over'][0]['graph']  = "device_current";
$config['os'][$os]['over'][0]['text']   = "Current";

$os = "mgepdu";
$config['os'][$os]['text']              = "MGE PDU";
$config['os'][$os]['type']              = "power";
$config['os'][$os]['icon']              = "mge";

$os = "apc";
$config['os'][$os]['text']              = "APC OS";
$config['os'][$os]['type']              = "power";
$config['os'][$os]['over'][0]['graph']  = "device_current";
$config['os'][$os]['over'][0]['text']   = "Current";

$os = "netbotz";
$config['os'][$os]['text']              = "Netbotz Environment sensor";
$config['os'][$os]['type']              = "environment";
$config['os'][$os]['over'][0]['graph']  = "device_temperature";
$config['os'][$os]['over'][0]['text']   = "Temperature";
$config['os'][$os]['over'][1]['graph']  = "device_humidity";
$config['os'][$os]['over'][1]['text']   = "Humidity";

$os = "pcoweb";
$config['os'][$os]['text']              = "Carel pCOWeb";
$config['os'][$os]['type']              = "environment";
$config['os'][$os]['over'][0]['graph']  = "device_temperature";
$config['os'][$os]['over'][0]['text']   = "Temperature";
$config['os'][$os]['over'][1]['graph']  = "device_humidity";
$config['os'][$os]['over'][1]['text']   = "Humidity";
$config['os'][$os]['icon']              = "carel";
$config['os'][$os]['icons'][]           = "uniflair";

$os = "netvision";
$config['os'][$os]['text']              = "Socomec Net Vision";
$config['os'][$os]['type']              = "power";
$config['os'][$os]['over'][0]['graph']  = "device_current";
$config['os'][$os]['over'][0]['text']   = "Current";

$os = "areca";
$config['os'][$os]['text']              = "Areca RAID Subsystem";
$config['os'][$os]['over'][0]['graph']  = "";
$config['os'][$os]['over'][0]['text']   = "";

$os = "netmanplus";
$config['os'][$os]['text']              = "NetMan Plus";
$config['os'][$os]['group']             = "ups";
$config['os'][$os]['nobulk']            = 1;
$config['os'][$os]['type']              = "power";
$config['os'][$os]['over'][0]['graph']  = "device_current";
$config['os'][$os]['over'][0]['text']   = "Current";

$os = "akcp";
$config['os'][$os]['text']              = "AKCP SensorProbe";
$config['os'][$os]['type']              = "environment";
$config['os'][$os]['over'][0]['graph']  = "device_temperature";
$config['os'][$os]['over'][0]['text']   = "temperature";

$os = "minkelsrms";
$config['os'][$os]['text']              = "Minkels RMS";
$config['os'][$os]['type']              = "environment";
$config['os'][$os]['over'][0]['graph']  = "device_temperature";
$config['os'][$os]['over'][0]['text']   = "temperature";

$os = "ipoman";
$config['os'][$os]['text']              = "Ingrasys iPoMan";
$config['os'][$os]['type']              = "power";
$config['os'][$os]['icon']              = "ingrasys";
$config['os'][$os]['over'][0]['graph']  = "device_current";
$config['os'][$os]['over'][0]['text']   = "Current";
$config['os'][$os]['over'][1]['graph']  = "device_power";
$config['os'][$os]['over'][1]['text']   = "Power";

$os = "wxgoos";
$config['os'][$os]['text']              = "ITWatchDogs Goose";
$config['os'][$os]['type']              = "environment";
$config['os'][$os]['over'][0]['graph']  = "device_temperature";
$config['os'][$os]['over'][0]['text']   = "temperature";

$os = "papouch";
$config['os'][$os]['text']              = "Papouch Probe";
$config['os'][$os]['type']              = "environment";
$config['os'][$os]['over'][0]['graph']  = "device_temperature";
$config['os'][$os]['over'][0]['text']   = "temperature";

$os = "cometsystem-p85xx";
$config['os'][$os]['text']              = "Comet System P85xx";
$config['os'][$os]['type']              = "environment";
$config['os'][$os]['icon']              = "comet";
$config['os'][$os]['over'][0]['graph']  = "device_temperature";
$config['os'][$os]['over'][0]['text']   = "temperature";

$os = "dell-laser";
$config['os'][$os]['group']             = "printer";
$config['os'][$os]['text']              = "Dell Laser";
$config['os'][$os]['ifname']            = 1;
$config['os'][$os]['type']              = "printer";
$config['os'][$os]['icon']              = "dell";
$config['os'][$os]['over'][0]['graph']  = "device_toner";
$config['os'][$os]['over'][0]['text']   = "Toner";

$os = "ricoh";
$config['os'][$os]['group']             = "printer";
$config['os'][$os]['text']              = "Ricoh Printer";
$config['os'][$os]['type']              = "printer";
$config['os'][$os]['icon']              = "ricoh";
$config['os'][$os]['over'][0]['graph']  = "device_toner";
$config['os'][$os]['over'][0]['text']   = "Toner";

$os = "nrg";
$config['os'][$os]['group']             = "printer";
$config['os'][$os]['text']              = "NRG Printer";
$config['os'][$os]['type']              = "printer";
$config['os'][$os]['icon']              = "nrg";
$config['os'][$os]['over'][0]['graph']  = "device_toner";
$config['os'][$os]['over'][0]['text']   = "Toner";

$os = "epson";
$config['os'][$os]['group']             = "printer";
$config['os'][$os]['text']              = "Epson Printer";
$config['os'][$os]['type']              = "printer";
$config['os'][$os]['icon']              = "epson";
$config['os'][$os]['over'][0]['graph']  = "device_toner";
$config['os'][$os]['over'][0]['text']   = "Toner";

$os = "xerox";
$config['os'][$os]['group']             = "printer";
$config['os'][$os]['text']              = "Xerox Printer";
$config['os'][$os]['ifname']            = 1;
$config['os'][$os]['type']              = "printer";
$config['os'][$os]['over'][0]['graph']  = "device_toner";
$config['os'][$os]['over'][0]['text']   = "Toner";

$os = "jetdirect";
$config['os'][$os]['group']             = "printer";
$config['os'][$os]['text']              = "HP Printer";
$config['os'][$os]['ifname']            = 1;
$config['os'][$os]['type']              = "printer";
$config['os'][$os]['icon']              = "hp";
$config['os'][$os]['over'][0]['graph']  = "device_toner";
$config['os'][$os]['over'][0]['text']   = "Toner";

$os = "richoh";
$config['os'][$os]['group']             = "printer";
$config['os'][$os]['text']              = "Ricoh Printer";
$config['os'][$os]['type']              = "printer";
$config['os'][$os]['over'][0]['graph']  = "device_toner";
$config['os'][$os]['over'][0]['text']   = "Toner";

$os = "okilan";
$config['os'][$os]['group']             = "printer";
$config['os'][$os]['text']              = "OKI Printer";
$config['os'][$os]['over'][0]['graph']  = "device_toner";
$config['os'][$os]['over'][0]['text']   = "Toner";
$config['os'][$os]['type']              = "printer";
$config['os'][$os]['icon']              = "oki";

$os = "brother";
$config['os'][$os]['group']             = "printer";
$config['os'][$os]['text']              = "Brother Printer";
$config['os'][$os]['type']              = "printer";
$config['os'][$os]['over'][0]['graph']  = "device_toner";
$config['os'][$os]['over'][0]['text']   = "Toner";

$os = "konica";
$config['os'][$os]['group']             = "printer";
$config['os'][$os]['text']              = "Konica-Minolta Printer";
$config['os'][$os]['type']              = "printer";
$config['os'][$os]['over'][0]['graph']  = "device_toner";
$config['os'][$os]['over'][0]['text']   = "Toner";

$os = "kyocera";
$config['os'][$os]['group']             = "printer";
$config['os'][$os]['text']              = "Kyocera Mita Printer";
$config['os'][$os]['over'][0]['graph']  = "device_toner";
$config['os'][$os]['over'][0]['text']   = "Toner";
$config['os'][$os]['ifname']            = 1;
$config['os'][$os]['type']              = "printer";

$os = "samsung";
$config['os'][$os]['group']             = "printer";
$config['os'][$os]['text']              = "Samsung Printer";
$config['os'][$os]['type']              = "printer";
$config['os'][$os]['over'][0]['graph']  = "device_toner";
$config['os'][$os]['over'][0]['text']   = "Toner";

$os = "sentry3";
$config['os'][$os]['text']              = "ServerTech Sentry3";
$config['os'][$os]['type']              = "power";
$config['os'][$os]['over'][0]['graph']  = "device_current";
$config['os'][$os]['over'][0]['text']   = "Current";
$config['os'][$os]['icon']              = "servertech";

$os = "raritan";
$config['os'][$os]['text']              = "Raritan PDU";
$config['os'][$os]['type']              = "power";
$config['os'][$os]['over'][0]['graph']  = "device_current";
$config['os'][$os]['over'][0]['text']   = "Current";
$config['os'][$os]['icon']              = "raritan";

$os = "vmware";
$config['os'][$os]['type']              = "server";
$config['os'][$os]['text']              = "VMware";
$config['os'][$os]['ifXmcbc']           = 1;
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";

$os = "mrvld";
$config['os'][$os]['group']             = "mrv";
$config['os'][$os]['text']              = "MRV LambdaDriver";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "mrv";

$os = "poweralert";
$config['os'][$os]['text']              = "Tripp Lite PowerAlert";
$config['os'][$os]['type']              = "power";
$config['os'][$os]['over'][0]['graph']  = "device_current";
$config['os'][$os]['over'][0]['text']   = "Current";
$config['os'][$os]['icon']              = "tripplite";

$os = "avocent";
$config['os'][$os]['text']              = "Avocent";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "avocent";

$os = "symbol";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['text']              = "Symbol AP";
$config['os'][$os]['icon']              = "symbol";

$os = "firebox";
$config['os'][$os]['text']              = "Watchguard Firebox";
$config['os'][$os]['type']              = "firewall";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
$config['os'][$os]['icon']              = "watchguard";

$os = "panos";
$config['os'][$os]['text']              = "PanOS";
$config['os'][$os]['type']              = "firewall";
$config['os'][$os]['icon']              = "panos";
$config['os'][$os]['snmp']['max-rep']   = 100;
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";


$os = "arubaos";
$config['os'][$os]['text']              = "ArubaOS";
$config['os'][$os]['type']              = "wireless";
$config['os'][$os]['icon']              = "arubaos";
$config['os'][$os]['over'][0]['graph']  = "device_arubacontroller_numaps";
$config['os'][$os]['over'][0]['text']   = "Number of APs";
$config['os'][$os]['over'][1]['graph']  = "device_arubacontroller_numclients";
$config['os'][$os]['over'][1]['text']   = "Number of Clients";

$os = "dsm";
$config['os'][$os]['text']              = "Synology DSM";
$config['os'][$os]['group']             = "unix";
$config['os'][$os]['type']              = "storage";
$config['os'][$os]['icon']              = "synology";

// Ubiquiti

$os = "airos";
$config['os'][$os]['text']              = "Ubiquiti AirOS";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "ubiquiti";
$config['os'][$os]['nobulk']            = 1;
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";

$os = "edgeos";
$config['os'][$os]['text']              = "Ubiquiti EdgeOS";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['group']             = "unix";
$config['os'][$os]['icon']              = "ubiquiti";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "Processors";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";

$os = "draytek";
$config['os'][$os]['text']              = "Draytek";
$config['os'][$os]['type']              = "firewall";
$config['os'][$os]['icon']              = "draytek";

foreach ($config['os'] as $this_os => $blah)
{
  if (isset($config['os'][$this_os]['group']))
  {
    $this_os_group = $config['os'][$this_os]['group'];
    if (isset($config['os_group'][$this_os_group]))
    {
      foreach ($config['os_group'][$this_os_group] as $property => $value)
      {
        if (!isset($config['os'][$this_os][$property]))
        {
          $config['os'][$this_os][$property] = $value;
        }
      }
    }
  }
}

// Graph Types

$config['graph_sections'] = array('general', 'system', 'firewall', 'netstats', 'wireless', 'storage', 'vpdn', 'load balancer', 'appliance', 'poller', 'netapp');

$config['graph_types']['device']['wifi_clients']['section'] = 'wireless';
$config['graph_types']['device']['wifi_clients']['order'] = '0';
$config['graph_types']['device']['wifi_clients']['descr'] = 'Wireless Clients';

/// NetApp graphs

$config['graph_types']['device']['netapp_ops']     = array('section' => 'netapp', 'descr' => 'NetApp Operations', 'order' => '0');
$config['graph_types']['device']['netapp_net_io']  = array('section' => 'netapp', 'descr' => 'NetApp Network I/O', 'order' => '1');
$config['graph_types']['device']['netapp_disk_io'] = array('section' => 'netapp', 'descr' => 'NetApp Disk I/O', 'order' => '2');
$config['graph_types']['device']['netapp_tape_io'] = array('section' => 'netapp', 'descr' => 'NetApp Tape I/O', 'order' => '3');

/// Poller graphs
$config['graph_types']['device']['poller_perf']['section'] = 'poller';
$config['graph_types']['device']['poller_perf']['order'] = '0';
$config['graph_types']['device']['poller_perf']['descr'] = 'Poller Duration';

$config['graph_types']['device']['ping']['section'] = 'poller';
$config['graph_types']['device']['ping']['order'] = '0';
$config['graph_types']['device']['ping']['descr'] = 'Ping Response';

$config['graph_types']['device']['ping_snmp']['section'] = 'poller';
$config['graph_types']['device']['ping_snmp']['order'] = '0';
$config['graph_types']['device']['ping_snmp']['descr'] = 'SNMP Response';

$config['graph_types']['device']['agent']['section'] = 'poller';
$config['graph_types']['device']['agent']['order'] = '0';
$config['graph_types']['device']['agent']['descr'] = 'Agent Execution Time';

$config['graph_types']['device']['netstat_arista_sw_ip'] = array(
 'section' => 'netstats', 'order' => '0', 'descr' => "Software forwarded IPv4 Statistics");
$config['graph_types']['device']['netstat_arista_sw_ip_frag'] = array(
 'section' => 'netstats', 'order' => '0', 'descr' => "Software forwarded IPv4 Fragmentation Statistics");

$config['graph_types']['device']['netstat_arista_sw_ip6'] = array(
 'section' => 'netstats', 'order' => '0', 'descr' => "Software forwarded IPv6 Statistics");
$config['graph_types']['device']['netstat_arista_sw_ip6_frag'] = array(
 'section' => 'netstats', 'order' => '0', 'descr' => "Software forwarded IPv6 Fragmentation Statistics");

$config['graph_types']['device']['cipsec_flow_bits']['section'] = 'firewall';
$config['graph_types']['device']['cipsec_flow_bits']['order'] = '0';
$config['graph_types']['device']['cipsec_flow_bits']['descr'] = 'IPSec Tunnel Traffic Volume';
$config['graph_types']['device']['cipsec_flow_pkts']['section'] = 'firewall';
$config['graph_types']['device']['cipsec_flow_pkts']['order'] = '0';
$config['graph_types']['device']['cipsec_flow_pkts']['descr'] = 'IPSec Tunnel Traffic Packets';
$config['graph_types']['device']['cipsec_flow_stats']['section'] = 'firewall';
$config['graph_types']['device']['cipsec_flow_stats']['order'] = '0';
$config['graph_types']['device']['cipsec_flow_stats']['descr'] = 'IPSec Tunnel Statistics';
$config['graph_types']['device']['cipsec_flow_tunnels']['section'] = 'firewall';
$config['graph_types']['device']['cipsec_flow_tunnels']['order'] = '0';
$config['graph_types']['device']['cipsec_flow_tunnels']['descr'] = 'IPSec Active Tunnels';
$config['graph_types']['device']['cras_sessions']['section'] = 'firewall';
$config['graph_types']['device']['cras_sessions']['order'] = '0';
$config['graph_types']['device']['cras_sessions']['descr'] = 'Remote Access Sessions';
$config['graph_types']['device']['fortigate_sessions']['section'] = 'firewall';
$config['graph_types']['device']['fortigate_sessions']['order'] = '0';
$config['graph_types']['device']['fortigate_sessions']['descr'] = 'Active Sessions';
$config['graph_types']['device']['fortigate_cpu']['section'] = 'system';
$config['graph_types']['device']['fortigate_cpu']['order'] = '0';
$config['graph_types']['device']['fortigate_cpu']['descr'] = 'CPU';
$config['graph_types']['device']['screenos_sessions']['section'] = 'firewall';
$config['graph_types']['device']['screenos_sessions']['order'] = '0';
$config['graph_types']['device']['screenos_sessions']['descr'] = 'Active Sessions';
$config['graph_types']['device']['panos_sessions']['section'] = 'firewall';
$config['graph_types']['device']['panos_sessions']['order'] = '0';
$config['graph_types']['device']['panos_sessions']['descr'] = 'Active Sessions';

$config['graph_types']['device']['juniperive_users']['section'] = 'appliance';
$config['graph_types']['device']['juniperive_users']['order'] = '0';
$config['graph_types']['device']['juniperive_users']['descr'] = 'Concurrent Users';
$config['graph_types']['device']['juniperive_meetings']['section'] = 'appliance';
$config['graph_types']['device']['juniperive_meetings']['order'] = '0';
$config['graph_types']['device']['juniperive_meetings']['descr'] = 'Meetings';
$config['graph_types']['device']['juniperive_connections']['section'] = 'appliance';
$config['graph_types']['device']['juniperive_connections']['order'] = '0';
$config['graph_types']['device']['juniperive_connections']['descr'] = 'Connections';
$config['graph_types']['device']['juniperive_storage']['section'] = 'appliance';
$config['graph_types']['device']['juniperive_storage']['order'] = '0';
$config['graph_types']['device']['juniperive_storage']['descr'] = 'Storage';

$config['graph_types']['device']['bits']['section'] = 'netstats';
$config['graph_types']['device']['bits']['order'] = '0';
$config['graph_types']['device']['bits']['descr'] = 'Total Traffic';
$config['graph_types']['device']['ipsystemstats_ipv4']['section'] = 'netstats';
$config['graph_types']['device']['ipsystemstats_ipv4']['order'] = '0';
$config['graph_types']['device']['ipsystemstats_ipv4']['descr'] = 'IPv4 Packet Statistics';
$config['graph_types']['device']['ipsystemstats_ipv4_frag']['section'] = 'netstats';
$config['graph_types']['device']['ipsystemstats_ipv4_frag']['order'] = '0';
$config['graph_types']['device']['ipsystemstats_ipv4_frag']['descr'] = 'IPv4 Fragmentation Statistics';
$config['graph_types']['device']['ipsystemstats_ipv6']['section'] = 'netstats';
$config['graph_types']['device']['ipsystemstats_ipv6']['order'] = '0';
$config['graph_types']['device']['ipsystemstats_ipv6']['descr'] = 'IPv6 Packet Statistics';
$config['graph_types']['device']['ipsystemstats_ipv6_frag']['section'] = 'netstats';
$config['graph_types']['device']['ipsystemstats_ipv6_frag']['order'] = '0';
$config['graph_types']['device']['ipsystemstats_ipv6_frag']['descr'] = 'IPv6 Fragmentation Statistics';
$config['graph_types']['device']['netstat_icmp_info']['section'] = 'netstats';
$config['graph_types']['device']['netstat_icmp_info']['order'] = '0';
$config['graph_types']['device']['netstat_icmp_info']['descr'] = 'ICMP Informational Statistics';
$config['graph_types']['device']['netstat_icmp']['section'] = 'netstats';
$config['graph_types']['device']['netstat_icmp']['order'] = '0';
$config['graph_types']['device']['netstat_icmp']['descr'] = 'ICMP Statistics';
$config['graph_types']['device']['netstat_ip']['section'] = 'netstats';
$config['graph_types']['device']['netstat_ip']['order'] = '0';
$config['graph_types']['device']['netstat_ip']['descr'] = 'IP Statistics';
$config['graph_types']['device']['netstat_ip_frag']['section'] = 'netstats';
$config['graph_types']['device']['netstat_ip_frag']['order'] = '0';
$config['graph_types']['device']['netstat_ip_frag']['descr'] = 'IP Fragmentation Statistics';
$config['graph_types']['device']['netstat_snmp']['section'] = 'netstats';
$config['graph_types']['device']['netstat_snmp']['order'] = '0';
$config['graph_types']['device']['netstat_snmp']['descr'] = 'SNMP Statistics';
$config['graph_types']['device']['netstat_snmp_pkt']['section'] = 'netstats';
$config['graph_types']['device']['netstat_snmp_pkt']['order'] = '0';
$config['graph_types']['device']['netstat_snmp_pkt']['descr'] = 'SNMP Packet Type Statistics';

$config['graph_types']['device']['netstat_tcp']['section'] = 'netstats';
$config['graph_types']['device']['netstat_tcp']['order'] = '0';
$config['graph_types']['device']['netstat_tcp']['descr'] = 'TCP Statistics';
$config['graph_types']['device']['netstat_udp']['section'] = 'netstats';
$config['graph_types']['device']['netstat_udp']['order'] = '0';
$config['graph_types']['device']['netstat_udp']['descr'] = 'UDP Statistics';

$config['graph_types']['device']['fdb_count']['section'] = 'system';
$config['graph_types']['device']['fdb_count']['order'] = '0';
$config['graph_types']['device']['fdb_count']['descr'] = 'FDB Table Usage';
$config['graph_types']['device']['hr_processes']['section'] = 'system';
$config['graph_types']['device']['hr_processes']['order'] = '0';
$config['graph_types']['device']['hr_processes']['descr'] = 'Running Processes';
$config['graph_types']['device']['hr_users']['section'] = 'system';
$config['graph_types']['device']['hr_users']['order'] = '0';
$config['graph_types']['device']['hr_users']['descr'] = 'Users Logged In';
$config['graph_types']['device']['mempool']['section'] = 'system';
$config['graph_types']['device']['mempool']['order'] = '0';
$config['graph_types']['device']['mempool']['descr'] = 'Memory Pool Usage';
$config['graph_types']['device']['processor']['section'] = 'system';
$config['graph_types']['device']['processor']['order'] = '0';
$config['graph_types']['device']['processor']['descr'] = 'Processors';
$config['graph_types']['device']['storage']['section'] = 'system';
$config['graph_types']['device']['storage']['order'] = '0';
$config['graph_types']['device']['storage']['descr'] = 'Filesystem Usage';
$config['graph_types']['device']['temperature']['section'] = 'system';
$config['graph_types']['device']['temperature']['order'] = '0';
$config['graph_types']['device']['temperature']['descr'] = 'temperature';
$config['graph_types']['device']['ucd_cpu']['section'] = 'system';
$config['graph_types']['device']['ucd_cpu']['order'] = '0';
$config['graph_types']['device']['ucd_cpu']['descr'] = 'Detailed Processors';
$config['graph_types']['device']['ucd_load']['section'] = 'system';
$config['graph_types']['device']['ucd_load']['order'] = '0';
$config['graph_types']['device']['ucd_load']['descr'] = 'Load Averages';
$config['graph_types']['device']['ucd_memory']['section'] = 'system';
$config['graph_types']['device']['ucd_memory']['order'] = '0';
$config['graph_types']['device']['ucd_memory']['descr'] = 'Detailed Memory';
$config['graph_types']['device']['ucd_swap_io']['section'] = 'system';
$config['graph_types']['device']['ucd_swap_io']['order'] = '0';
$config['graph_types']['device']['ucd_swap_io']['descr'] = 'Swap I/O Activity';
$config['graph_types']['device']['ucd_io']['section'] = 'system';
$config['graph_types']['device']['ucd_io']['order'] = '0';
$config['graph_types']['device']['ucd_io']['descr'] = 'System I/O Activity';
$config['graph_types']['device']['ucd_contexts']['section'] = 'system';
$config['graph_types']['device']['ucd_contexts']['order'] = '0';
$config['graph_types']['device']['ucd_contexts']['descr'] = 'Context Switches';
$config['graph_types']['device']['ucd_interrupts']['section'] = 'system';
$config['graph_types']['device']['ucd_interrupts']['order'] = '0';
$config['graph_types']['device']['ucd_interrupts']['descr'] = 'Interrupts';
$config['graph_types']['device']['uptime']['section'] = 'system';
$config['graph_types']['device']['uptime']['order'] = '0';
$config['graph_types']['device']['uptime']['descr'] = 'System Uptime';

$config['graph_types']['device']['ksm_pages']['section']           = 'system';
$config['graph_types']['device']['ksm_pages']['order']             = '0';
$config['graph_types']['device']['ksm_pages']['descr']             = 'KSM Shared Pages';

$config['graph_types']['device']['vpdn_sessions_l2tp']['section']  = 'vpdn';
$config['graph_types']['device']['vpdn_sessions_l2tp']['order']    = '0';
$config['graph_types']['device']['vpdn_sessions_l2tp']['descr']    = 'VPDN L2TP Sessions';

$config['graph_types']['device']['vpdn_tunnels_l2tp']['section']   = 'vpdn';
$config['graph_types']['device']['vpdn_tunnels_l2tp']['order']     = '0';
$config['graph_types']['device']['vpdn_tunnels_l2tp']['descr']     = 'VPDN L2TP Tunnels';

$config['graph_types']['device']['netscaler_tcp_conn']['section']  = 'load balancer';
$config['graph_types']['device']['netscaler_tcp_conn']['order']    = '0';
$config['graph_types']['device']['netscaler_tcp_conn']['descr']    = 'TCP Connections';

$config['graph_types']['device']['netscaler_tcp_bits']['section']  = 'load balancer';
$config['graph_types']['device']['netscaler_tcp_bits']['order']    = '0';
$config['graph_types']['device']['netscaler_tcp_bits']['descr']    = 'TCP Traffic';

$config['graph_types']['device']['netscaler_tcp_pkts']['section']  = 'load balancer';
$config['graph_types']['device']['netscaler_tcp_pkts']['order']    = '0';
$config['graph_types']['device']['netscaler_tcp_pkts']['descr']    = 'TCP Packets';

$config['graph_types']['device']['netscalersvc_bits']['descr']     = 'Aggregate Service Traffic';
$config['graph_types']['device']['netscalersvc_pkts']['descr']     = 'Aggregate Service Packets';
$config['graph_types']['device']['netscalersvc_conns']['descr']    = 'Aggregate Service Connections';
$config['graph_types']['device']['netscalersvc_reqs']['descr']     = 'Aggregate Service Requests';

$config['graph_types']['device']['netscalervsvr_bits']['descr']    = 'Aggregate vServer Traffic';
$config['graph_types']['device']['netscalervsvr_pkts']['descr']    = 'Aggregate vServer Packets';
$config['graph_types']['device']['netscalervsvr_conns']['descr']   = 'Aggregate vServer Connections';
$config['graph_types']['device']['netscalervsvr_reqs']['descr']    = 'Aggregate vServer Requests';
$config['graph_types']['device']['netscalervsvr_hitmiss']['descr'] = 'Aggregate vServer Hits/Misses';

$config['graph_types']['device']['asyncos_workq']['section'] = 'appliance';
$config['graph_types']['device']['asyncos_workq']['order'] = '0';
$config['graph_types']['device']['asyncos_workq']['descr'] = 'Work Queue Messages';

$config['graph_descr']['device_smokeping_in_all'] = "This is an aggregate graph of the incoming smokeping tests to this host. The line corresponds to the average RTT. The shaded area around each line denotes the standard deviation.";
$config['graph_descr']['device_processor']        = "This is an aggregate graph of all processors in the system.";

$config['graph_descr']['application_unbound_queries'] = "DNS queries to the recursive resolver. The unwanted replies could be innocent duplicate packets, late replies, or spoof threats.";
$config['graph_descr']['application_unbound_queue']   = "The queries that did not hit the cache and need recursion service take up space in the requestlist. If there are too many queries, first queries get overwritten, and at last resort dropped.";
$config['graph_descr']['application_unbound_memory']  = "The memory used by unbound.";
$config['graph_descr']['application_unbound_qtype']   = "Queries by DNS RR type queried for.";
$config['graph_descr']['application_unbound_class']   = "Queries by DNS RR class queried for.";
$config['graph_descr']['application_unbound_opcode']  = "Queries by DNS opcode in the query packet.";
$config['graph_descr']['application_unbound_rcode']   = "Answers sorted by return value. RRSets bogus is the number of RRSets marked bogus per second by the validator.";
$config['graph_descr']['application_unbound_flags']   = "This graphs plots the flags inside incoming queries. For example, if QR, AA, TC, RA, Z flags are set, the query can be rejected. RD, AD, CD and DO are legitimately set by some software.";

// Device Types

$i = 0;
$config['device_types'][$i]['text'] = 'Servers';
$config['device_types'][$i]['type'] = 'server';
$config['device_types'][$i]['icon'] = 'oicon-server';

$i++;
$config['device_types'][$i]['text'] = 'Workstations';
$config['device_types'][$i]['type'] = 'workstation';
$config['device_types'][$i]['icon'] = 'oicon-computer';

$i++;
$config['device_types'][$i]['text'] = 'Network';
$config['device_types'][$i]['type'] = 'network';
$config['device_types'][$i]['icon'] = 'oicon-network-hub';

$i++;
$config['device_types'][$i]['text'] = 'Wireless';
$config['device_types'][$i]['type'] = 'wireless';
$config['device_types'][$i]['icon'] = 'oicon-wi-fi-zone';

$i++;
$config['device_types'][$i]['text'] = 'Firewalls';
$config['device_types'][$i]['type'] = 'firewall';
$config['device_types'][$i]['icon'] = 'oicon-wall-brick';

$i++;
$config['device_types'][$i]['text'] = 'Power';
$config['device_types'][$i]['type'] = 'power';
$config['device_types'][$i]['icon'] = 'oicon-plug';

$i++;
$config['device_types'][$i]['text'] = 'Environment';
$config['device_types'][$i]['type'] = 'environment';
$config['device_types'][$i]['icon'] = 'oicon-water';

$i++;
$config['device_types'][$i]['text'] = 'Load Balancers';
$config['device_types'][$i]['type'] = 'loadbalancer';
$config['device_types'][$i]['icon'] = 'oicon-arrow-split';

$i++;
$config['device_types'][$i]['text'] = 'Video';
$config['device_types'][$i]['type'] = 'video';
$config['device_types'][$i]['icon'] = 'oicon-surveillance-camera';

$i++;
$config['device_types'][$i]['text'] = 'Storage';
$config['device_types'][$i]['type'] = 'storage';
$config['device_types'][$i]['icon'] = 'oicon-database';

if (isset($config['enable_printers']) && $config['enable_printers'])
{
  $i++;
  $config['device_types'][$i]['text'] = 'Printers';
  $config['device_types'][$i]['type'] = 'printer';
  $config['device_types'][$i]['icon'] = 'oicon-printer-color';
}

// FIXME - remove this old variable from use

$config['sensor_classes'] = array('current' => 'A',
                           'frequency' => 'Hz',
                           'humidity' => '%',
                           'fanspeed' => 'RPM',
                           'power' => 'W',
                           'voltage' => 'V',
                           'temperature' => 'C',
                           'dbm' => 'dBm');

// FIXME - different icons for power/volt/current

$config['sensor_types']['current']     = array( 'symbol' => 'A',   'text' => 'Amperes', 'icon' => 'oicon-current');
$config['sensor_types']['frequency']   = array( 'symbol' => 'Hz',  'text' => 'Hertz',   'icon' => 'oicon-frequency');
$config['sensor_types']['humidity']    = array( 'symbol' => '%',   'text' => 'Percent', 'icon' => 'oicon-water');
$config['sensor_types']['fanspeed']    = array( 'symbol' => 'RPM', 'text' => 'RPM',     'icon' => 'oicon-weather-wind');
$config['sensor_types']['power']       = array( 'symbol' => 'W',   'text' => 'Watts',   'icon' => 'oicon-power');
$config['sensor_types']['voltage']     = array( 'symbol' => 'V',   'text' => 'Volts',   'icon' => 'oicon-voltage');
$config['sensor_types']['temperature'] = array( 'symbol' => 'C',   'text' => 'Celsius', 'icon' => 'oicon-thermometer-high');
$config['sensor_types']['dbm']         = array( 'symbol' => 'dBm', 'text' => 'dBm',     'icon' => 'oicon-arrow-incident-red');

$config['routing_types']['ospf']       = array( 'text' => 'OSPF');
$config['routing_types']['cef']       = array( 'text' => 'CEF');
$config['routing_types']['bgp']       = array( 'text' => 'BGP');
$config['routing_types']['vrf']       = array( 'text' => 'VRFs');

////////////////////////////////
// No changes below this line //
////////////////////////////////

$config['version']  = "0.SVN.ERROR";

$svn_new = TRUE;
if (file_exists($config['install_dir'] . '/.svn/entries'))
{
  $svn = File($config['install_dir'] . '/.svn/entries');
  if ((int)$svn[0] < 12)
  {
    // SVN version < 1.7
    $svn_rev = trim($svn[3]);
    list($svn_date) = explode("T", trim($svn[9]));
    $svn_new = FALSE;
  }
}
if ($svn_new)
{
  // SVN version >= 1.7
  $xml = simplexml_load_string(shell_exec($config['svn'] . ' info --xml'));
  if ($xml != false)
  {
    $svn_rev = $xml->entry->commit->attributes()->revision;
    $svn_date = $xml->entry->commit->date;
  }
}
if (!empty($svn_rev))
{
  list($svn_year, $svn_month, $svn_day) = explode("-", $svn_date);
  $config['version'] = "0." . ($svn_year-2000) . "." . ($svn_month+0) . "." . $svn_rev;
}

if (isset($config['rrdgraph_def_text']))
{
  $config['rrdgraph_def_text'] = str_replace("  ", " ", $config['rrdgraph_def_text']);
  $config['rrd_opts_array'] = explode(" ", trim($config['rrdgraph_def_text']));
}

// Set default paths.
if (!isset($config['html_dir'])) { $config['html_dir'] = $config['install_dir'] . '/html'; }
if (!isset($config['rrd_dir']))  { $config['rrd_dir']  = $config['install_dir'] . '/rrd'; }
if (!isset($config['log_file'])) { $config['log_file'] = $config['install_dir'] . '/observium.log'; }
if (!isset($config['temp_dir'])) { $config['temp_dir'] = '/tmp'; }
/// FIXME. I really do not understand why a separate option $config['mibdir']. -- mike
if (!isset($config['mibdir']))   { $config['mibdir']   = $config['install_dir'] . '/mibs'; }
$config['mib_dir'] = $config['mibdir'];

if (isset($config['cdp_autocreate']))
{
  $config['dp_autocreate'] = $config['cdp_autocreate'];
}

// Detect if we're on CLI or WEB.
if (php_sapi_name() !== 'cli' || !isset($_SERVER["argv"][0]) || isset($_SERVER['REQUEST_METHOD'])  || isset($_SERVER['REMOTE_ADDR']))
{
  $cli = FALSE;
} else {
  $cli = TRUE;
}


// If we're on SSL, let's properly detect it
function is_ssl()
{
  if ( isset($_SERVER['HTTPS']) )
  {
      if ( 'on' == strtolower($_SERVER['HTTPS']) )
          return true;
      if ( '1' == $_SERVER['HTTPS'] )
          return true;
  } elseif ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) )
  {
    return true;
  }
  return false;
}
if (is_ssl())
{
  $config['base_url'] = preg_replace('/^http:/','https:', $config['base_url']);
}

// Connect to database
$observium_link = mysql_connect($config['db_host'], $config['db_user'], $config['db_pass']);
if (!$observium_link)
{
  include_once("common.php");
  print_error("MySQL Error: " . mysql_error());
  die;
}
$observium_db = mysql_select_db($config['db_name'], $observium_link);

// Connect to statsd

#if($config['statsd']['enable'])
#{
#  $log = new \StatsD\Client($config['statsd']['host'].':'.$config['statsd']['port']);
#}

// Set some times needed by loads of scripts (it's dynamic, so we do it here!)
$config['time']['now']        = time();
$config['time']['fourhour']   = $config['time']['now'] - 14400;    //time() - (4 * 60 * 60);
$config['time']['sixhour']    = $config['time']['now'] - 21600;    //time() - (6 * 60 * 60);
$config['time']['twelvehour'] = $config['time']['now'] - 43200;    //time() - (12 * 60 * 60);
$config['time']['day']        = $config['time']['now'] - 86400;    //time() - (24 * 60 * 60);
$config['time']['twoday']     = $config['time']['now'] - 172800;   //time() - (2 * 24 * 60 * 60);
$config['time']['week']       = $config['time']['now'] - 604800;   //time() - (7 * 24 * 60 * 60);
$config['time']['twoweek']    = $config['time']['now'] - 1209600;  //time() - (2 * 7 * 24 * 60 * 60);
$config['time']['month']      = $config['time']['now'] - 2678400;  //time() - (31 * 24 * 60 * 60);
$config['time']['twomonth']   = $config['time']['now'] - 5356800;  //time() - (2 * 31 * 24 * 60 * 60);
$config['time']['threemonth'] = $config['time']['now'] - 8035200;  //time() - (3 * 31 * 24 * 60 * 60);
$config['time']['sixmonth']   = $config['time']['now'] - 16070400; //time() - (6 * 31 * 24 * 60 * 60);
$config['time']['year']       = $config['time']['now'] - 31536000; //time() - (365 * 24 * 60 * 60);
$config['time']['twoyear']    = $config['time']['now'] - 63072000; //time() - (2 * 365 * 24 * 60 * 60);

// IPMI sensor type mappings
$config['ipmi_unit']['Volts']     = 'voltage';
$config['ipmi_unit']['degrees C'] = 'temperature';
$config['ipmi_unit']['RPM']       = 'fanspeed';
$config['ipmi_unit']['Watts']     = 'power';
$config['ipmi_unit']['discrete']  = '';

// INCLUDE THE VMWARE DEFINITION FILE.
require_once("vmware_guestid.inc.php");

// End includes/definitions.inc.php
