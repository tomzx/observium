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
 * Display IPv4/IPv6 addresses.
 *
 * Display pages with IP addresses from device Interfaces.
 *
 * @param array $vars
 * @return none
 *
 */

function print_addresses($vars)
{
  // With pagination? (display page numbers in header)
  $pagination = (isset($vars['pagination']) && $vars['pagination']);
  $pageno = (isset($vars['pageno']) && !empty($vars['pageno'])) ? $vars['pageno'] : 1;
  $pagesize = (isset($vars['pagesize']) && !empty($vars['pagesize'])) ? $vars['pagesize'] : 10;
  $start = $pagesize * $pageno - $pagesize;

  switch($vars['search'])
  {
    case '6':
    case 'ipv6':
    case 'v6':
      $address_type = 'ipv6';
      break;
    default:
      $address_type = 'ipv4';
  }

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
          $where .= ' AND I.device_id = ?';
          $param[] = $value;
          break;
        case 'interface':
          $where .= ' AND I.ifDescr LIKE ?';
          $param[] = $value;
          break;
        case 'network':
          $where .= ' AND N.ip_network_id = ?';
          $param[] = $value;
          break;
        case 'address':
          list($addr, $mask) = explode('/', $value);
          if (is_numeric(stripos($addr, ':abcdef'))) { $address_type = 'ipv6'; }
          switch ($address_type)
          {
            case 'ipv6':
              $ip_valid = Net_IPv6::checkIPv6($addr);
              break;
            case 'ipv4':
              $ip_valid = Net_IPv4::validateIP($addr);
              break;
          }
          if ($ip_valid)
          {
            // If address valid -> seek occurrence in network
            if (!$mask) { $mask = ($address_type === 'ipv4') ? '32' : '128'; }
          } else {
            // If address not valid -> seek LIKE
            $where .= ' AND A.ip_address LIKE ?';
            $param[] = '%'.$addr.'%';
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
  $query_device = ' AND D.ignore = 0 ';
  if (!$config['web_show_disabled']) { $query_device .= 'AND D.disabled = 0 '; }

  $query = 'FROM `ip_addresses` AS A ';
  $query .= 'LEFT JOIN `ports`   AS I ON I.port_id   = A.port_id ';
  $query .= 'LEFT JOIN `devices` AS D ON I.device_id = D.device_id ';
  $query .= 'LEFT JOIN `ip_networks` AS N ON N.ip_network_id = A.ip_network_id ';
  $query .= $query_perms;
  $query .= $where . $query_device . $query_user;
  $query_count = 'SELECT COUNT(ip_address_id) ' . $query;
  $query =  'SELECT * ' . $query;
  $query .= ' ORDER BY A.ip_address';
  if ($ip_valid)
  {
    $pagination = FALSE;
  } else {
    $query .= " LIMIT $start,$pagesize";
  }
  // Override by address type
  $query = str_replace(array('ip_address', 'ip_network'), array($address_type.'_address', $address_type.'_network'), $query);
  $query_count = str_replace(array('ip_address', 'ip_network'), array($address_type.'_address', $address_type.'_network'), $query_count);

  // Query addresses
  $entries = dbFetchRows($query, $param);
  // Query address count
  if ($pagination) { $count = dbFetchCell($query_count, $param); }

  $list = array('device' => FALSE);
  if (!isset($vars['device']) || empty($vars['device']) || $vars['page'] == 'search') { $list['device'] = TRUE; }

  $string = '<table class="table table-bordered table-striped table-hover table-condensed">' . PHP_EOL;
  if (!$short)
  {
    $string .= '  <thead>' . PHP_EOL;
    $string .= '    <tr>' . PHP_EOL;
    if ($list['device']) { $string .= '      <th>Device</th>' . PHP_EOL; }
    $string .= '      <th>Interface</th>' . PHP_EOL;
    $string .= '      <th>Address</th>' . PHP_EOL;
    $string .= '      <th>Description</th>' . PHP_EOL;
    $string .= '    </tr>' . PHP_EOL;
    $string .= '  </thead>' . PHP_EOL;
  }
  $string .= '  <tbody>' . PHP_EOL;

  foreach ($entries as $entry)
  {
    $address_show = TRUE;
    if ($ip_valid)
    {
      // If address not in specified network, don't show entry.
      if ($address_type === 'ipv4')
      {
        $address_show = Net_IPv4::ipInNetwork($entry[$address_type.'_address'], $addr . '/' . $mask);
      } else {
        $address_show = Net_IPv6::isInNetmask($entry[$address_type.'_address'], $addr, $mask);
      }
    }

    if ($address_show)
    {
      list($prefix, $length) = explode('/', $entry[$address_type.'_network']);

      if (port_permitted($entry['port_id']))
      {
        humanize_port ($entry);
        if ($entry['ifInErrors_delta'] > 0 || $entry['ifOutErrors_delta'] > 0)
        {
          $port_error = generate_port_link($entry, '<span class="label label-important">Errors</span>', 'port_errors');
        }

        $string .= '  <tr>' . PHP_EOL;
        if ($list['device'])
        {
          $string .= '    <td class="entity" nowrap>' . generate_device_link($entry) . '</td>' . PHP_EOL;
        }
        $string .= '    <td class="entity">' . generate_port_link($entry, makeshortif($entry['label'])) . ' ' . $port_error . '</td>' . PHP_EOL;
        if ($address_type === 'ipv6') { $entry[$address_type.'_address'] = Net_IPv6::compress($entry[$address_type.'_address']); }
        $string .= '    <td>' . $entry[$address_type.'_address'] . '/' . $length . '</td>' . PHP_EOL;
        $string .= '    <td>' . $entry['ifAlias'] . '</td>' . PHP_EOL;
        $string .= '  </tr>' . PHP_EOL;
      }
    }
  }

  $string .= '  </tbody>' . PHP_EOL;
  $string .= '</table>';

  // Print pagination header
  if ($pagination) { echo pagination($vars, $count); }

  // Print addresses
  echo $string;
}

?>
