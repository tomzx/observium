<?php

/**
 * Observium
 *
 *   This files is part of Observium.
 *
 * @package    observium
 * @subpackage applications
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */

$app_graphs['default'] = array('powerdns_latency'  => 'PowerDNS - Latency',
                'powerdns_fail' => 'PowerDNS - Corrupt / Failed / Timed out',
                'powerdns_packetcache' => 'PowerDNS - Packet Cache',
                'powerdns_querycache' => 'PowerDNS - Query Cache',
                'powerdns_recursing' => 'PowerDNS - Recursing Queries and Answers',
                'powerdns_queries' => 'PowerDNS - Total UDP/TCP Queries and Answers',
                'powerdns_queries_udp' => 'PowerDNS - Detail UDP IPv4/IPv6 Queries and Answers');
