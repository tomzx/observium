<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    observium
 * @subpackage web
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */

/**
 * Display ARP/NDP table addresses.
 *
 * Display pages with ARP/NDP tables addresses from devices.
 *
 * @param array $vars
 * @return none
 *
 */

function print_arptable($vars)
{
  // With pagination? (display page numbers in header)
  $pagination = (isset($vars['pagination']) && $vars['pagination']);
  $pageno = (isset($vars['pageno']) && !empty($vars['pageno'])) ? $vars['pageno'] : 1;
  $pagesize = (isset($vars['pagesize']) && !empty($vars['pagesize'])) ? $vars['pagesize'] : 10;
  $start = $pagesize * $pageno - $pagesize;

  $param = array();
  $where = ' WHERE 1 ';
  foreach ($vars as $var => $value)
  {
    if ($value != '')
    {
      switch ($var)
      {
        case 'device':
        case 'device_id':
          $where .= ' AND D.device_id = ?';
          $param[] = $value;
          break;
        case 'port':
        case 'port_id':
          $where .= ' AND I.port_id = ?';
          $param[] = $value;
          break;
        case 'ip_version':
          $where .= ' AND ip_version = ?';
          $param[] = $value;
          break;
        case 'address':
          if (isset($vars['searchby']) && $vars['searchby'] == 'ip')
          {
            $where .= ' AND `ip_address` LIKE ?';
            $value = trim($value);
            ///FIXME. Need another conversion ("2001:b08:b08" -> "2001:0b08:0b08") -- mike
            if (Net_IPv6::checkIPv6($value)) { $value = Net_IPv6::uncompress($value, true); }
            $param[] = '%'.$value.'%';
          } else {
            $where .= ' AND `mac_address` LIKE ?';
            $param[] = '%'.str_replace(array(':', ' ', '-', '.', '0x'),'',mres($value)).'%';
          }
          break;
      }
    }
  }

  if ($_SESSION['userlevel'] >= 5)
  {
    $query_perms = '';
    $query_user = '';
  } else {
    $query_perms = 'LEFT JOIN devices_perms AS P ON D.device_id = P.device_id ';
    $query_user = ' AND P.user_id = ? ';
    $param[] = $_SESSION['user_id'];
  }

  // Don't show ignored and disabled devices
  if ($vars['page'] != 'device')
  {
    $query_device = ' AND D.ignore = 0 ';
    if (!$config['web_show_disabled']) { $query_device .= 'AND D.disabled = 0 '; }
  }

  $query = 'FROM `ip_mac` AS M ';
  $query .= 'LEFT JOIN `ports`   AS I ON I.port_id   = M.port_id ';
  $query .= 'LEFT JOIN `devices` AS D ON I.device_id = D.device_id ';
  $query .= $query_perms;
  $query .= $where . $query_device . $query_user;
  $query_count = 'SELECT COUNT(mac_id) ' . $query;
  $query =  'SELECT * ' . $query;
  $query .= ' ORDER BY M.mac_address';
  $query .= " LIMIT $start,$pagesize";

  // Query ARP/NDP table addresses
  $entries = dbFetchRows($query, $param);
  // Query ARP/NDP table address count
  if ($pagination) { $count = dbFetchCell($query_count, $param); }

  $list = array('device' => FALSE, 'port' => FALSE);
  if (!isset($vars['device']) || empty($vars['device']) || $vars['page'] == 'search') { $list['device'] = TRUE; }
  if (!isset($vars['port']) || empty($vars['port']) || $vars['page'] == 'search') { $list['port'] = TRUE; }

  $string = '<table class="table table-bordered table-striped table-hover table-condensed">' . PHP_EOL;
  if (!$short)
  {
    $string .= '  <thead>' . PHP_EOL;
    $string .= '    <tr>' . PHP_EOL;
    $string .= '      <th>MAC Address</th>' . PHP_EOL;
    $string .= '      <th>IP Address</th>' . PHP_EOL;
    if ($list['device']) { $string .= '      <th>Device</th>' . PHP_EOL; }
    if ($list['port']) { $string .= '      <th>Interface</th>' . PHP_EOL; }
    $string .= '      <th>Remote Device</th>' . PHP_EOL;
    $string .= '      <th>Remote Interface</th>' . PHP_EOL;
    $string .= '    </tr>' . PHP_EOL;
    $string .= '  </thead>' . PHP_EOL;
  }
  $string .= '  <tbody>' . PHP_EOL;

  foreach ($entries as $entry)
  {
    if (port_permitted($entry['port_id']))
    {
      humanize_port ($entry);
      $ip_version = $entry['ip_version'];
      $ip_address = ($ip_version == 6) ? Net_IPv6::compress($entry['ip_address']) : $entry['ip_address'];
      $arp_host = dbFetchRow('SELECT * FROM ipv'.$ip_version.'_addresses AS A
                             LEFT JOIN ports AS I ON A.port_id = I.port_id
                             LEFT JOIN devices AS D ON D.device_id = I.device_id
                             WHERE A.ipv'.$ip_version.'_address = ?', array($ip_address));
      $arp_name = ($arp_host) ? generate_device_link($arp_host) : '';
      $arp_if = ($arp_host) ? generate_port_link($arp_host) : '';
      if ($arp_host['device_id'] == $entry['device_id']) { $arp_name = 'Self Device'; }
      if ($arp_host['port_id'] == $entry['port_id']) { $arp_if = 'Self Port'; }

      $string .= '  <tr>' . PHP_EOL;
      $string .= '    <td width="160">' . formatMac($entry['mac_address']) . '</td>' . PHP_EOL;
      $string .= '    <td width="140">' . $ip_address . '</td>' . PHP_EOL;
      if ($list['device'])
      {
        $string .= '    <td class="entity" nowrap>' . generate_device_link($entry) . '</td>' . PHP_EOL;
      }
      if ($list['port'])
      {
        if ($entry['ifInErrors_delta'] > 0 || $entry['ifOutErrors_delta'] > 0)
        {
          $port_error = generate_port_link($entry, '<span class="label label-important">Errors</span>', 'port_errors');
        }
        $string .= '    <td class="entity">' . generate_port_link($entry, makeshortif($entry['label'])) . ' ' . $port_error . '</td>' . PHP_EOL;
      }
      $string .= '    <td class="entity" width="200">' . $arp_name . '</td>' . PHP_EOL;
      $string .= '    <td class="entity">' . $arp_if . '</td>' . PHP_EOL;
      $string .= '  </tr>' . PHP_EOL;
    }
  }

  $string .= '  </tbody>' . PHP_EOL;
  $string .= '</table>';

  // Print pagination header
  if ($pagination) { echo pagination($vars, $count); }

  // Print ARP/NDP table
  echo $string;
}

?>
