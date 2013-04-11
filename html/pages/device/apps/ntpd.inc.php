<?php

/**
 * Observium
 *
 *   This files is part of Observium.
 *
 * @package    observium
 * @subpackage applications
 * @subpackage ntpd
 * @author     Dennis de Houx <info@all-in-one.be>
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 * @version    1.0.1
 *
 */

global $config;

// Set variables
$rrd_server      = $config['rrd_dir'] . "/" . $device['hostname'] . "/app-ntpd-server-".$app['app_id'].".rrd";
$rrd_client      = $config['rrd_dir'] . "/" . $device['hostname'] . "/app-ntpd-client-".$app['app_id'].".rrd";
$ntpd_type       = (file_exists($rrd_server) ? "server" : "client");
$i               = 0;
if ($ntpd_type == "server") {
  $app_sections  = array('server' => "System",
                        'buffer' => "Buffer",
                        'packets' => "Packets");
} else {
  $app_sections  = array('client' => "System");
}
$navbar          = array();
$navbar['brand'] = "NTPd";
$navbar['class'] = "navbar-narrow";

// Make navbar array
foreach ($app_sections as $type=>$text) {
  $i++;
  if ($ntpd_type == "server") {
    $vars['app_section']            = ((isset($vars['app_section'])) ? $vars['app_section'] : "server");
  } else {
    $vars['app_section']            = ((isset($vars['app_section'])) ? $vars['app_section'] : "client");
  }
  $navbar['options'][$i]['url']   = generate_url(array('page' => 'device', 'device' => $device['device_id'], 'tab' => 'apps', 'app' => 'ntpd', 'instance' => $app['app_id'], 'app_section' => $type));
  $navbar['options'][$i]['text']  = nicecase($text);
  $navbar['options'][$i]['class'] = (($vars['app_section'] == $type) ? "active" : "");
}

// Show navbar
print_navbar($navbar);
unset($navbar);

$graphs['client'] = array('ntpd_stats'  => 'NTP Client - Statistics',
                          'ntpd_freq' => 'NTP Client - Frequency');

$graphs['server'] = array('ntpd_stats'  => 'NTPD Server - Statistics',
                          'ntpd_freq' => 'NTPD Server - Frequency',
                          'ntpd_uptime' => 'NTPD Server - Uptime',
                          'ntpd_stratum' => 'NTPD Server - Stratum');

$graphs['buffer'] = array('ntpd_buffer' => 'NTPD Server - Buffer');

$graphs['packets'] = array('ntpd_bits' => 'NTPD Server - Packets Sent/Received',
                           'ntpd_packets' => 'NTPD Server - Packets Dropped/Ignored');

foreach ($graphs[$vars['app_section']] as $key => $text) {
  $graph_type            = $key;
  $graph_array['to']     = $config['time']['now'];
  $graph_array['id']     = $app['app_id'];
  $graph_array['type']   = "application_".$key;
  echo("<h3>".$text."</h3>");
  echo("<tr bgcolor='$row_colour'><td colspan=5>");

  include("includes/print-graphrow.inc.php");

  echo("</td></tr>");
}

?>
