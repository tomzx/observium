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
 * Display Interface MACs addresses.
 *
 * Display pages with MAC addresses from device Interfaces.
 *
 * @param array $vars
 * @return none
 *
 */

function print_mac_addresses($vars)
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
          $where .= ' AND I.device_id = ?';
          $param[] = $value;
          break;
        case 'interface':
          $where .= ' AND I.ifDescr LIKE ?';
          $param[] = $value;
          break;
        case 'address':
          $where .= ' AND I.ifPhysAddress LIKE ?';
          $param[] = '%'.str_replace(array(':', ' ', '-', '.', '0x'),'',mres($value)).'%';
          break;
      }
    }
  }
  $where .= ' AND I.ifPhysAddress IS NOT NULL'; //Exclude empty MACs

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

  $query = 'FROM `ports` AS I ';
  $query .= 'LEFT JOIN `devices` AS D ON I.device_id = D.device_id ';
  $query .= $query_perms;
  $query .= $where . $query_device . $query_user;
  $query_count = 'SELECT COUNT(*) ' . $query;
  $query =  'SELECT * ' . $query;
  $query .= ' ORDER BY I.ifPhysAddress';
  $query .= " LIMIT $start,$pagesize";

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
    $string .= '      <th>MAC Address</th>' . PHP_EOL;
    $string .= '      <th>Description</th>' . PHP_EOL;
    $string .= '    </tr>' . PHP_EOL;
    $string .= '  </thead>' . PHP_EOL;
  }
  $string .= '  <tbody>' . PHP_EOL;

  foreach ($entries as $entry)
  {
    if (port_permitted($entry['port_id']))
    {
      humanize_port ($entry);
      
      $string .= '  <tr>' . PHP_EOL;
      if ($list['device'])
      {
        $string .= '    <td class="entity" nowrap>' . generate_device_link($entry) . '</td>' . PHP_EOL;
      }
      if ($entry['ifInErrors_delta'] > 0 || $entry['ifOutErrors_delta'] > 0)
      {
        $port_error = generate_port_link($entry, '<span class="label label-important">Errors</span>', 'port_errors');
      }
      $string .= '    <td class="entity">' . generate_port_link($entry, makeshortif($entry['label'])) . ' ' . $port_error . '</td>' . PHP_EOL;
      $string .= '    <td width="160">' . formatMac($entry['ifPhysAddress']) . '</td>' . PHP_EOL;
      $string .= '    <td>' . $entry['ifAlias'] . '</td>' . PHP_EOL;
      $string .= '  </tr>' . PHP_EOL;
    }
  }

  $string .= '  </tbody>' . PHP_EOL;
  $string .= '</table>';

  // Print pagination header
  if ($pagination) { echo pagination($vars, $count); }

  // Print MAC addresses
  echo $string;
}

?>
