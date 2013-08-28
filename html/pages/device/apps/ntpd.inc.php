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

// Set variables
$rrd_server      = $config['rrd_dir'] . "/" . $device['hostname'] . "/app-ntpd-server-".$app['app_id'].".rrd";
$rrd_client      = $config['rrd_dir'] . "/" . $device['hostname'] . "/app-ntpd-client-".$app['app_id'].".rrd";
$ntpd_type       = (file_exists($rrd_server) ? "server" : "client");

// Test if this is a server or client install and set app_sections accordingly
if ($ntpd_type == "server") {
  $app_sections  = array('server' => "System",
                        'buffer' => "Buffer",
                        'packets' => "Packets");
}

$app_graphs['default'] = array('ntpd_stats'  => 'NTP Client - Statistics',
                          'ntpd_freq' => 'NTP Client - Frequency');

$app_graphs['server'] = array('ntpd_stats'  => 'NTPD Server - Statistics',
                          'ntpd_freq' => 'NTPD Server - Frequency',
                          'ntpd_uptime' => 'NTPD Server - Uptime',
                          'ntpd_stratum' => 'NTPD Server - Stratum');

$app_graphs['buffer'] = array('ntpd_buffer' => 'NTPD Server - Buffer');

$app_graphs['packets'] = array('ntpd_bits' => 'NTPD Server - Packets Sent/Received',
                           'ntpd_packets' => 'NTPD Server - Packets Dropped/Ignored');
