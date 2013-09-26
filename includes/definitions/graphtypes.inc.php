<?php

// Graph sections is used to categorize /device/graphs/

$config['graph_sections'] = array('general', 'system', 'firewall', 'netstats', 'wireless', 'storage', 'vpdn', 'load balancer', 'appliance', 'poller', 'netapp');

// Graph types

$config['graph_types']['port']['bits']       = array('name' => 'Bits',       'descr' => "Traffic in bits/sec");
$config['graph_types']['port']['upkts']      = array('name' => 'Ucast Pkts', 'descr' => "Unicast packets/sec");
$config['graph_types']['port']['nupkts']     = array('name' => 'NU Pkts',    'descr' => "Non-unicast packets/sec");
$config['graph_types']['port']['pktsize']    = array('name' => 'Pkt Size',   'descr' => "Average packet size");
$config['graph_types']['port']['percent']    = array('name' => 'Percent',    'descr' => "Percent utilization");
$config['graph_types']['port']['errors']     = array('name' => 'Errors',     'descr' => "Errors/sec");
$config['graph_types']['port']['etherlike']  = array('name' => 'Ethernet Errors',     'descr' => "Detailed Errors/sec for Ethernet-like interfaces");


$config['graph_types']['device']['wifi_clients']['section'] = 'wireless';
$config['graph_types']['device']['wifi_clients']['order'] = '0';
$config['graph_types']['device']['wifi_clients']['descr'] = 'Wireless Clients';

// NetApp graphs
$config['graph_types']['device']['netapp_ops']     = array('section' => 'netapp', 'descr' => 'NetApp Operations', 'order' => '0');
$config['graph_types']['device']['netapp_net_io']  = array('section' => 'netapp', 'descr' => 'NetApp Network I/O', 'order' => '1');
$config['graph_types']['device']['netapp_disk_io'] = array('section' => 'netapp', 'descr' => 'NetApp Disk I/O', 'order' => '2');
$config['graph_types']['device']['netapp_tape_io'] = array('section' => 'netapp', 'descr' => 'NetApp Tape I/O', 'order' => '3');

// Poller graphs
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

// End includes/definitions/graphtypes.inc.php
