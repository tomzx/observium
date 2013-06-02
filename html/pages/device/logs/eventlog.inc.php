<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    observium
 * @subpackage webui
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */


unset($search, $types);

//Message field
$search[] = array('type'    => 'text',
                  'name'    => 'Message',
                  'id'      => 'message',
                  'value'   => $vars['message']);
//Type field
//$types[''] = 'All Types';
$types['system'] = 'System';
foreach (dbFetchRows('SELECT `type` FROM `eventlog` WHERE `device_id` = ? GROUP BY `type` ORDER BY `type`', array($vars['device'])) as $data)
{
  $type = $data['type'];
  $types[$type] = ucfirst($type);
}
$search[] = array('type'    => 'multiselect',
                  'name'    => 'Types',
                  'id'      => 'type',
                  //'width'   => '130px',
                  'value'   => $vars['type'],
                  'values'  => $types);
$search[] = array('type'    => 'newline');
$search[] = array('type'    => 'datetime',
                  'id'      => 'timestamp',
                  'presets' => TRUE,
                  'min'     => dbFetchCell('SELECT MIN(`timestamp`) FROM `eventlog` WHERE `device_id` = ?', array($vars['device'])),
                  'max'     => dbFetchCell('SELECT MAX(`timestamp`) FROM `eventlog` WHERE `device_id` = ?', array($vars['device'])),
                  'from'    => $vars['timestamp_from'],
                  'to'      => $vars['timestamp_to']);

print_search_simple($search, 'Eventlog');

/// Pagination
$vars['pagination'] = TRUE;
if(!$vars['pagesize']) { $vars['pagesize'] = "100"; }
if(!$vars['pageno']) { $vars['pageno'] = "1"; }

print_events($vars);

$pagetitle[] = "Events";

?>
