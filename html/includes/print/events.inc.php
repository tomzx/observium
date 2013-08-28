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
 * Display events.
 *
 * Display pages with device/port/system events on some formats.
 * Examples:
 * print_events() - display last 10 events from all devices
 * print_events(array('pagesize' => 99)) - display last 99 events from all device
 * print_events(array('pagesize' => 10, 'pageno' => 3, 'pagination' => TRUE)) - display 10 events from page 3 with pagination header
 * print_events(array('pagesize' => 10, 'device' = 4)) - display last 10 events for device_id 4
 * print_events(array('short' => TRUE)) - show small block with last events
 *
 * @param array $vars
 * @return none
 *
 */
function print_events($vars)
{
  // Short events? (no pagination, small out)
  $short = (isset($vars['short']) && $vars['short']);
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
          $where .= ' AND E.device_id = ?';
          $param[] = $value;
          break;
        case 'port':
          $where .= ' AND E.reference = ?';
          $param[] = $value;
          break;
        case 'type':
          if (!is_array($value)) { $value = array($value); }
          $where .= ' AND (';
          foreach ($value as $v)
          {
            $where .= "E.type = ? OR ";
            $param[] = $v;
          }
          $where = substr($where, 0, -4) . ')';
          break;
        case 'message':
          foreach (explode(',', $value) as $val)
          {
            $param[] = '%'.$val.'%';
            $cond[] = "`$var` LIKE ?";
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

  $query = 'FROM `eventlog` AS E ';
  $query .= 'LEFT JOIN `devices` AS D ON E.device_id = D.device_id ';
  $query .= $query_perms;
  $query .= $where . $query_device . $query_user;
  $query_count = 'SELECT COUNT(event_id) '.$query;

  /// FIXME Mike: bad table column `type` they intersect with table `devices`
  $query = 'SELECT STRAIGHT_JOIN E.device_id, E.timestamp, E.message, E.type, E.reference '.$query;
  $query .= ' ORDER BY `event_id` DESC ';
  $query .= "LIMIT $start,$pagesize";

  // Query events
  $entries = dbFetchRows($query, $param);

  // Query events count
  if ($pagination && !$short) { $count = dbFetchCell($query_count, $param); }

  $list = array('device' => FALSE, 'port' => FALSE);
  if (!isset($vars['device']) || empty($vars['device']) || $vars['page'] == 'eventlog') { $list['device'] = TRUE; }
  if ($short || !isset($vars['port']) || empty($vars['port'])) { $list['port'] = TRUE; }

  $string = '<table class="table table-bordered table-striped table-hover table-condensed-more">' . PHP_EOL;
  if (!$short)
  {
    $string .= '  <thead>' . PHP_EOL;
    $string .= '    <tr>' . PHP_EOL;
    $string .= '      <th>Date</th>' . PHP_EOL;
    if ($list['device']) { $string .= '      <th>Device</th>' . PHP_EOL; }
    if ($list['port'])   { $string .= '      <th>Entity</th>' . PHP_EOL; }
    $string .= '      <th>Message</th>' . PHP_EOL;
    $string .= '    </tr>' . PHP_EOL;
    $string .= '  </thead>' . PHP_EOL;
  }
  $string   .= '  <tbody>' . PHP_EOL;

  foreach ($entries as $entry)
  {

    $icon = geteventicon($entry['message']);
    if ($icon) { $icon = '<img src="images/16/' . $icon . '" />'; }

    $string .= '  <tr>' . PHP_EOL;
    if ($short)
    {
      $string .= '    <td width="100" class="syslog">';
      $unixtime = strtotime($entry['timestamp']);
      $timediff = time() - $unixtime;
      $string .= overlib_link('', formatUptime($timediff, "short-3"), format_timestamp($entry['timestamp']), NULL) . '</td>' . PHP_EOL;
    } else {
      $string .= '    <td width="160">';
      $string .= format_timestamp($entry['timestamp']) . '</td>' . PHP_EOL;
    }

    if ($list['device'])
    {
      $dev = device_by_id_cache($entry['device_id']);
      $device_vars = array('page'    => 'device',
                           'device'  => $entry['device_id'],
                           'tab'     => 'logs',
                           'section' => 'eventlog');
      $string .= '    <td class="entity">' . generate_device_link($dev, shorthost($dev['hostname']), $device_vars) . '</td>' . PHP_EOL;
    }
    if ($list['port'])
    {
      if ($entry['type'] == 'interface')
      {
        $this_if = getifbyid($entry['reference']);
        humanize_port($this_if);
        $entry['link'] = '<span class="entity">' . generate_port_link($this_if, makeshortif($this_if['label'])) . '</span>';
      } else {
        $entry['link'] = 'System';
      }
      if (!$short) { $string .= '    <td>' . $entry['link'] . '</td>' . PHP_EOL; }
    }
    if ($short)
    {
      $string .= '    <td class="syslog">' . $entry['link'] . ' ';
    } else {
      $string .= '    <td>';
    }
    $string .= htmlspecialchars($entry['message']) . '</td>' . PHP_EOL;
    $string .= '  </tr>' . PHP_EOL;
  }

  $string .= '  </tbody>' . PHP_EOL;
  $string .= '</table>';

  // Print pagination header
  if ($pagination && !$short) { echo pagination($vars, $count); }

  // Print events
  echo $string;
}

/**
 * Display short events.
 *
 * This is use function:
 * print_events(array('short' => TRUE))
 *
 * @param array $vars
 * @return none
 *
 */
function print_events_short($var)
{
  $var['short'] = TRUE;
  print_events($var);
}

?>
