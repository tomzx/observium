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
 * Display syslog messages.
 *
 * Display pages with device syslog messages.
 * Examples:
 * print_syslogs() - display last 10 syslog messages from all devices
 * print_syslogs(array('pagesize' => 99)) - display last 99 syslog messages from all device
 * print_syslogs(array('pagesize' => 10, 'pageno' => 3, 'pagination' => TRUE)) - display 10 syslog messages from page 3 with pagination header
 * print_syslogs(array('pagesize' => 10, 'device' = 4)) - display last 10 syslog messages for device_id 4
 * print_syslogs(array('short' => TRUE)) - show small block with last syslog messages
 *
 * @param array $vars
 * @return none
 *
 */
function print_syslogs($vars)
{
  // Short events? (no pagination, small out)
  $short = (isset($vars['short']) && $vars['short']);
  // With pagination? (display page numbers in header)
  $pagination = (isset($vars['pagination']) && $vars['pagination']);
  $pageno = (isset($vars['pageno']) && !empty($vars['pageno'])) ? $vars['pageno'] : 1;
  $pagesize = (isset($vars['pagesize']) && !empty($vars['pagesize'])) ? $vars['pagesize'] : 10;
  $start = $pagesize * $pageno - $pagesize;

  $priorities = syslog_priorities();

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
        case 'priority':
        case 'program':
          if (!is_array($value)) { $value = array($value); }
          $where .= ' AND (';
          foreach ($value as $v)
          {
            if ($v === '[[EMPTY]]') { $v = ''; }
            $where .= "`$var` = ? OR ";
            $param[] = $v;
          }
          $where = substr($where, 0, -4) . ')';
          break;
        case 'message':
          foreach (explode(',', $value) as $val)
          {
            $param[] = '%'.$val.'%';
            $cond[] = '`msg` LIKE ?';
          }
          $where .= 'AND (';
          $where .= implode(' OR ', $cond);
          $where .= ')';
          break;
        case 'timestamp_from':
          $where .= ' AND `timestamp` > ?';
          $param[] = $value;
          break;
        case 'timestamp_to':
          $where .= ' AND `timestamp` < ?';
          $param[] = $value;
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

  $query = 'FROM `syslog` AS S ';
  $query .= 'LEFT JOIN `devices` AS D ON S.device_id = D.device_id ';
  $query .= $query_perms;
  $query .= $where . $query_user . $query_device;
  $query_count = 'SELECT COUNT(seq) ' . $query;
  $query = 'SELECT STRAIGHT_JOIN * ' . $query;
  $query .= ' ORDER BY `seq` DESC ';
  $query .= "LIMIT $start,$pagesize";

  // Query syslog messages
  $entries = dbFetchRows($query, $param);
  // Query syslog count
  if ($pagination && !$short) { $count = dbFetchCell($query_count, $param); }

  $list = array('device' => FALSE, 'priority' => TRUE); // For now (temporarily) priority always displayed
  if (!isset($vars['device']) || empty($vars['device']) || $vars['page'] == 'syslog') { $list['device'] = TRUE; }
  if ($short || !isset($vars['priority']) || empty($vars['priority'])) { $list['priority'] = TRUE; }

  $string = '<table class="table table-bordered table-striped table-hover table-condensed">' . PHP_EOL;
  if (!$short)
  {
    $string .= '  <thead>' . PHP_EOL;
    $string .= '    <tr>' . PHP_EOL;
    $string .= '      <th>Date</th>' . PHP_EOL;
    if ($list['device']) { $string .= '      <th>Device</th>' . PHP_EOL; }
    if ($list['priority']) { $string .= '      <th>Priority</th>' . PHP_EOL; }
    $string .= '      <th>Message</th>' . PHP_EOL;
    $string .= '    </tr>' . PHP_EOL;
    $string .= '  </thead>' . PHP_EOL;
  }
  $string .= '  <tbody>' . PHP_EOL;

  foreach ($entries as $entry)
  {
    $string .= '  <tr>';
    if ($short)
    {
      $string .= '    <td width="160" class="syslog">';
    } else {
      $string .= '    <td width="160">';
    }
    $string .= format_timestamp($entry['timestamp']) . '</td>' . PHP_EOL;
    if ($list['device'])
    {
      $dev = device_by_id_cache($entry['device_id']);
      $device_vars = array('page'    => 'device',
                           'device'  => $entry['device_id'],
                           'tab'     => 'logs',
                           'section' => 'syslog');
      $string .= '    <td class="entity">' . generate_device_link($dev, shorthost($dev['hostname']), $device_vars) . '</td>' . PHP_EOL;
    }
    if ($list['priority'])
    {
      if (!$short) { $string .= '    <td style="color: ' . $priorities[$entry['priority']]['color'] . ';">(' . $entry['priority'] . ')&nbsp;' . $priorities[$entry['priority']]['name'] . '</td>' . PHP_EOL; }
    }
    if ($short)
    {
      $string .= '    <td class="syslog">';
    } else {
      $string .= '    <td>';
    }
    $entry['program'] = ($entry['program'] === '') ? '[[EMPTY]]' : $entry['program'];
    $string .= '<strong>' . $entry['program'] . ' :</strong> ' . htmlspecialchars($entry['msg']) . '</td>' . PHP_EOL;
    $string .= '  </tr>' . PHP_EOL;
  }

  $string .= '  </tbody>' . PHP_EOL;
  $string .= '</table>' . PHP_EOL;

  // Print pagination header
  if ($pagination && !$short) { echo pagination($vars, $count); }

  // Print syslog
  echo($string);
}

?>
