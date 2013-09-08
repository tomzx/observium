<?php

/////////////////////////////////////////////////////////
//  NO CHANGES TO THIS FILE, IT IS NOT USER-EDITABLE   //
/////////////////////////////////////////////////////////
//               YES, THAT MEANS YOU                   //
/////////////////////////////////////////////////////////

include($config['install_dir'].'/includes/definitions/os.inc.php');

// Alert Graphs
## FIXME - this is ugly

$config['alert_graphs']['port']['ifInOctets_rate']  = array('type' => 'port_bits', 'id' => '@port_id');
$config['alert_graphs']['port']['ifOutOctets_rate'] = array('type' => 'port_bits', 'id' => '@port_id');
$config['alert_graphs']['port']['ifInOctets_perc']  = array('type' => 'port_bits', 'id' => '@port_id');
$config['alert_graphs']['port']['ifOutOctets_perc'] = array('type' => 'port_bits', 'id' => '@port_id');
$config['alert_graphs']['mempool']['mempool_perc']  = array('type' => 'mempool_usage', 'id' => '@mempool_id');
$config['alert_graphs']['sensor']['sensor_value']   = array('type' => 'sensor_graph', 'id' => '@sensor_id');
$config['alert_graphs']['mempool']['processor_usage']  = array('type' => 'processor_usage', 'id' => '@processor_id');

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

$config['graph_types']['device']['iostat_util']['section']         = 'system';
$config['graph_types']['device']['iostat_util']['order']           = '0';
$config['graph_types']['device']['iostat_util']['descr']           = 'Disk I/O Utilisation';

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

$config['graph_types']['application']['bind_answers']['descr'] = 'BIND Received Answers';
$config['graph_types']['application']['bind_query_in']['descr'] = 'BIND Incoming Queries';
$config['graph_types']['application']['bind_query_out']['descr'] = 'BIND Outgoing Queries';
$config['graph_types']['application']['bind_query_rejected']['descr'] = 'BIND Rejected Queries';
$config['graph_types']['application']['bind_req_in']['descr'] = 'BIND Incoming Requests';
$config['graph_types']['application']['bind_req_proto']['descr'] = 'BIND Request Protocol Details';
$config['graph_types']['application']['bind_resolv_dnssec']['descr'] = 'BIND DNSSEC Validation';
$config['graph_types']['application']['bind_resolv_errors']['descr'] = 'BIND Errors while Resolving';
$config['graph_types']['application']['bind_resolv_queries']['descr'] = 'BIND Resolving Queries';
$config['graph_types']['application']['bind_resolv_rtt']['descr'] = 'BIND Resolving RTT';
$config['graph_types']['application']['bind_updates']['descr'] = 'BIND Dynamic Updates';
$config['graph_types']['application']['bind_zone_maint']['descr'] = 'BIND Zone Maintenance';

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

// Application graph definitions

$config['app']['apache']['top']            = array('bits', 'hits', 'scoreboard', 'cpu');
$config['app']['bind']['top']              = array('req_in', 'answers', 'resolv_errors', 'resolv_rtt');
$config['app']['drbd']['top']              = array('disk_bits', 'network_bits', 'queue', 'unsynced');
$config['app']['mysql']['top']             = array('network_traffic', 'connections', 'command_counters', 'select_types');
$config['app']['memcached']['top']         = array('bits', 'commands', 'data', 'items');
$config['app']['powerdns']['top']          = array('recursing', 'queries', 'querycache', 'latency');
$config['app']['ntpd']['top']              = array('stats', 'freq', 'stratum', 'bits');
$config['app']['postgresql']['top']        = array('xact', 'blks', 'tuples', 'tuples_query');
$config['app']['shoutcast']['top']         = array('multi_stats', 'multi_bits');
$config['app']['nginx']['top']             = array('connections', 'req');
$config['app']['unbound']['top']           = array('queries', 'queue', 'memory', 'qtype');
$config['app']['freeradius']['top']        = array('access');
$config['app']['powerdns-recursor']['top'] = array('queries', 'timeouts', 'cache', 'latency');
$config['app']['exim-mailqueue']['top']    = array('total');
$config['app']['zimbra']['top']            = array('threads','mtaqueue','fdcount');
$config['app']['crashplan']['top']         = array('bits', 'sessions', 'archivesize', 'disk');

// Syslog colour and name translation

  $config['syslog']['priorities']['0'] = array('name' => 'emergency',   'color' => '#D94640');
  $config['syslog']['priorities']['1'] = array('name' => 'alert',        'color' => '#D94640');
  $config['syslog']['priorities']['2'] = array('name' => 'critical',      'color' => '#D94640');
  $config['syslog']['priorities']['3'] = array('name' => 'error',        'color' => '#E88126');
  $config['syslog']['priorities']['4'] = array('name' => 'warning',      'color' => '#F2CA3F');
  $config['syslog']['priorities']['5'] = array('name' => 'notification', 'color' => '#107373');
  $config['syslog']['priorities']['6'] = array('name' => 'informational', 'color' => '#499CA6');
  $config['syslog']['priorities']['7'] = array('name' => 'debugging',     'color' => '#5AA637');
  $config['syslog']['priorities']['8'] = array('name' => 'other',         'color' => '#5AA637');

  for ($i = 8; $i < 16; $i++)
  {
    $config['syslog']['priorities'][$i] = array('name' => 'other',        'color' => '#D2D8F9');
  }

// This is used to provide pretty rewrites for lowercase things we drag out of the db and use in URLs

$config['nicecase'] = array(
    "bgp_peer" => "BGP Peer",
    "netscaler_vsvr" => "Netscaler vServer",
    "netscaler_svc" => "Netscaler Service",
    "mempool" => "Memory",
    "ipsec_tunnels" => "IPSec Tunnels",
    "vrf" => "VRFs",
    "isis" => "IS-IS",
    "cef" => "CEF",
    "eigrp" => "EIGRP",
    "ospf" => "OSPF",
    "bgp" => "BGP",
    "ases" => "ASes",
    "vpns" => "VPNs",
    "dbm" => "dBm",
    "mysql" => "MySQL",
    "powerdns" => "PowerDNS",
    "bind" => "BIND",
    "ntpd" => "NTPd",
    "powerdns-recursor" => "PowerDNS Recursor",
    "freeradius" => "FreeRADIUS",
    "postfix_mailgraph" => "Postfix Mailgraph",
    "" => "");



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
  $xml = simplexml_load_string(shell_exec($config['svn'] . ' info --xml ' . $config['install_dir']));
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
