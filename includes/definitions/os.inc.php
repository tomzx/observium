<?php

/////////////////////////////////////////////////////////
//  NO CHANGES TO THIS FILE, IT IS NOT USER-EDITABLE   //
/////////////////////////////////////////////////////////
//               YES, THAT MEANS YOU                   //
/////////////////////////////////////////////////////////

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

$os = "wut";
$config['os'][$os]['text']              = "Web-Thermograph";
$config['os'][$os]['type']              = "environment";
$config['os'][$os]['over'][0]['graph']  = "device_temperature";
$config['os'][$os]['over'][0]['text']   = "Temperature";
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.5040.1";

// Other Unix-based OSes here please.

$os = "freebsd";
$config['os'][$os]['type']              = "server";
$config['os'][$os]['group']             = "unix";
$config['os'][$os]['text']              = "FreeBSD";
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.8072.3.2.8";

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
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.42.2.1.1";

$os = "aix";
$config['os'][$os]['group']             = "unix";
$config['os'][$os]['text']              = "AIX";
$config['os'][$os]['type']              = "server";
$config['os'][$os]['ifAliasSemicolon']  = TRUE;             // Split on semicolon and take the first element.
$config['os'][$os]['over']['0'] = array('text' => 'CPU Load',      'graph' =>  "device_processor");
$config['os'][$os]['over']['1'] = array('text' => 'Memory',  'graph' =>  "device_ucd_memory");
$config['os'][$os]['over']['2'] = array('text' => 'Storage', 'graph' =>  "device_storage");
$config['os'][$os]['over']['3'] = array('text' => 'Traffic', 'graph' =>  "device_bits");
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.2.3.1.2.1.1.2";
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.2.3.1.2.1.1.3";

$os = "adva";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['text']              = "Adva Optical";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.1671";

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
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.12740.17.1";
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.12740.12.1.1.0";

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
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.9.1.1291";

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
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.9.6.1.82.";
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.9.6.1.83.";
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.9.6.1.11.82.";
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.3955.";

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
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.2011.2.";

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
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.24062.2.1";
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.24062.2.2";
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.24062.2.3";

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
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.2636";

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
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.4874";

$os = "jwos";
$config['os'][$os]['text']              = "Juniper JWOS";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "juniper";
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.8239.1.2.9";

$os = "screenos";
$config['os'][$os]['text']              = "Juniper ScreenOS";
$config['os'][$os]['type']              = "firewall";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_processor";
$config['os'][$os]['over'][1]['text']   = "CPU Usage";
$config['os'][$os]['over'][2]['graph']  = "device_mempool";
$config['os'][$os]['over'][2]['text']   = "Memory";
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.674.3224.1";
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.3224";

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
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.12532";

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
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.12356.15";
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.12356.101.1";

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
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.6141.1";

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
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.1588.2.1.1";

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
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.5951.1";

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
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.11898.2.4.9";

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
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.674.10893.2.102";

$os = "drac";
$config['os'][$os]['text']              = "Dell DRAC";
$config['os'][$os]['icon']              = "dell";
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.674.10892.2";

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
$config['os'][$os]['over'][2]['text']   = "Frequency";
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.534";
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.705.1";

// Delta

$os = "deltaups";
$config['os'][$os]['text']              = "Delta UPS";
$config['os'][$os]['type']              = "power";
$config['os'][$os]['icon']              = "delta";
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.2254.2.4";

// Liebert

$os = "liebert";
$config['os'][$os]['text']              = "Liebert";
$config['os'][$os]['type']              = "power";
$config['os'][$os]['icon']              = "liebert";
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.476.1.42";

// Engenius

$os = "engenius";
$config['os'][$os]['type']              = "wireless";
$config['os'][$os]['text']              = "EnGenius Access Point";
$config['os'][$os]['icon']              = "engenius";
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['over'][1]['graph']  = "device_wifi_clients";
$config['os'][$os]['over'][1]['text']   = "Wireless clients";
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.14125.100.1.3";
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.14125.101.1.3";

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
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.311.1.1.3";

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
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.789.2.1";
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.789.2.3";

// Arris

$os = "arris-d5";
$config['os'][$os]['text']              = "Arris D5";
$config['os'][$os]['type']              = "video";
$config['os'][$os]['icon']              = "arris";
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.4115.1.8.1";

$os = "arris-c3";
$config['os'][$os]['text']              = "Arris C3";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "arris";
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.4115.1.4.3";

// HP / 3Com

$os = "3com";
$config['os'][$os]['text']              = "3Com OS";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "3com";
$config['os'][$os]['snmp']['max-rep']   = 100;
$config['os'][$os]['over'][0]['graph']  = "device_bits";
$config['os'][$os]['over'][0]['text']   = "Traffic";
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.43";

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
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.11.2.3.7.11.";
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.11.2.3.7.8.";

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
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.2011.10";
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.25506.1.";

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
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.25506";

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
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.318.1";

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
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.18928.1";

$os = "netmanplus";
$config['os'][$os]['text']              = "NetMan Plus";
$config['os'][$os]['group']             = "ups";
$config['os'][$os]['nobulk']            = 1;
$config['os'][$os]['type']              = "power";
$config['os'][$os]['over'][0]['graph']  = "device_current";
$config['os'][$os]['over'][0]['text']   = "Current";

$os = "sensorprobe";
$config['os'][$os]['text']              = "AKCP SensorProbe";
$config['os'][$os]['type']              = "environment";
$config['os'][$os]['icon']              = "akcp";
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
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.2468.1.4.2.1";

$os = "wxgoos";
$config['os'][$os]['text']              = "ITWatchDogs Goose";
$config['os'][$os]['type']              = "environment";
$config['os'][$os]['over'][0]['graph']  = "device_temperature";
$config['os'][$os]['over'][0]['text']   = "temperature";
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.17373";

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
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.674.10898.2.100.10";

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
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.11.1";

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
$config['os'][$os]['sysObjectID'][]     = ".1.3.6.1.4.1.388";

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

// Draytek firewall/routers

$os = "draytek";
$config['os'][$os]['text']              = "Draytek";
$config['os'][$os]['type']              = "firewall";
$config['os'][$os]['icon']              = "draytek";

// SmartEdge OS

$os = "seos";
$config['os'][$os]['text']              = "SmartEdge OS";
$config['os'][$os]['type']              = "network";
$config['os'][$os]['icon']              = "ericsson";


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

// End OS Definitions
