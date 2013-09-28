<?php

/////////////////////////////////////////////////////////
//  NO CHANGES TO THIS FILE, IT IS NOT USER-EDITABLE   //
/////////////////////////////////////////////////////////
//               YES, THAT MEANS YOU                   //
/////////////////////////////////////////////////////////

// Include OS definitions
include($config['install_dir'].'/includes/definitions/os.inc.php');

// Include Graph Type definitions
include($config['install_dir'].'/includes/definitions/graphtypes.inc.php');

// VMWare guestid => description definitions
include($config['install_dir'].'/includes/definitions/vmware_guestid.inc.php');

// Apps system definitions
include($config['install_dir'].'/includes/definitions/apps.inc.php');

// Entity type definitions
include($config['install_dir'].'/includes/definitions/entities.inc.php');

// Alert Graphs
## FIXME - this is ugly

$config['alert_graphs']['port']['ifInOctets_rate']       = array('type' => 'port_bits', 'id' => '@port_id');
$config['alert_graphs']['port']['ifOutOctets_rate']      = array('type' => 'port_bits', 'id' => '@port_id');
$config['alert_graphs']['port']['ifInOctets_perc']       = array('type' => 'port_percent', 'id' => '@port_id');
$config['alert_graphs']['port']['ifOutOctets_perc']      = array('type' => 'port_percent', 'id' => '@port_id');
$config['alert_graphs']['mempool']['mempool_perc']       = array('type' => 'mempool_usage', 'id' => '@mempool_id');
$config['alert_graphs']['sensor']['sensor_value']        = array('type' => 'sensor_graph', 'id' => '@sensor_id');
$config['alert_graphs']['processor']['processor_usage']  = array('type' => 'processor_usage', 'id' => '@processor_id');

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

// Syslog colour and name translation

$config['syslog']['priorities']['0'] = array('name' => 'emergency',   'color' => '#D94640');
$config['syslog']['priorities']['1'] = array('name' => 'alert',        'color' => '#D94640');
$config['syslog']['priorities']['2'] = array('name' => 'critical',      'color' => '#D94640');
$config['syslog']['priorities']['3'] = array('name' => 'error',        'color' => '#E88126');
$config['syslog']['priorities']['4'] = array('name' => 'warning',      'color' => '#F2CA3F');
$config['syslog']['priorities']['5'] = array('name' => 'notification', 'color' => '#107373');
$config['syslog']['priorities']['6'] = array('name' => 'informational', 'color' => '#499CA6');
$config['syslog']['priorities']['7'] = array('name' => 'debugging',     'color' => '#5AA637');

for ($i = 8; $i < 16; $i++)
{
  $config['syslog']['priorities'][$i] = array('name' => 'other',        'color' => '#D2D8F9');
}

// This is used to provide pretty rewrites for lowercase things we drag out of the db and use in URLs

$config['nicecase'] = array(
    "bgp_peer" => "BGP Peer",
	"cbgp_peer" => "BGP Peer (AFI/SAFI)",
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
	"ge" => "Greater or equal", 
	"le" => "Less or equal", 
	"notequals" => "Doesn't equal",
    "notmatch"  => "Doesn't match",
    "" => "");

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

// If we're on SSL, let's properly detect it
function is_ssl()
{
  if (isset($_SERVER['HTTPS']))
  {
    if ('on' == strtolower($_SERVER['HTTPS'])) { return TRUE; }
    if ('1' == $_SERVER['HTTPS']) { return TRUE; }
  } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT']))
  {
    return TRUE;
  }
  return FALSE;
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

// End includes/definitions.inc.php
