<?php

unset($search, $priorities, $programs, $timestamp_min, $timestamp_max);

$timestamp_min = dbFetchCell('SELECT MIN(`timestamp`) FROM `syslog` WHERE `device_id` = ?', array($vars['device']));
if ($timestamp_min)
{
  $timestamp_max = dbFetchCell('SELECT MAX(`timestamp`) FROM `syslog` WHERE `device_id` = ?', array($vars['device']));
  
  //Message field
  $search[] = array('type'    => 'text',
                    'name'    => 'Message',
                    'id'      => 'message',
                    'width'   => '130px',
                    'value'   => $vars['message']);
  //Priority field
  $priorities[''] = 'All Priorities';
  foreach (syslog_priorities() as $p => $priority)
  {
    if ($p > 7) { continue; }
    $priorities[$p] = '(' . $p . ') ' . ucfirst($priority['name']);
  }
  $search[] = array('type'    => 'select',
                    'name'    => 'Priority',
                    'id'      => 'priority',
                    'width'   => '130px',
                    'value'   => $vars['priority'],
                    'values'  => $priorities);
  //Program field
  $programs[''] = 'All Programs';
  foreach (dbFetchRows('SELECT `program` FROM `syslog` WHERE `device_id` = ? GROUP BY `program` ORDER BY `program`', array($vars['device'])) as $data)
  {
    $program = ($data['program']) ? $data['program'] : '[[EMPTY]]';
    $programs[$program] = $program;
  }
  $search[] = array('type'    => 'select',
                    'name'    => 'Program',
                    'id'      => 'program',
                    'width'   => '130px',
                    'value'   => $vars['program'],
                    'values'  => $programs);
  $search[] = array('type'    => 'newline');
  $search[] = array('type'    => 'datetime',
                    'id'      => 'timestamp',
                    'presets' => TRUE,
                    'min'     => $timestamp_min,
                    'max'     => $timestamp_max,
                    'from'    => $vars['timestamp_from'],
                    'to'      => $vars['timestamp_to']);
  
  print_search_simple($search);
  
  // Pagination
  $vars['pagination'] = TRUE;
  if(!$vars['pagesize']) { $vars['pagesize'] = 100; }
  if(!$vars['pageno']) { $vars['pageno'] = 1; }
  
  // Print syslog
  print_syslogs($vars);
} else {
  echo('<div class="alert alert-info"><h3>Device not have syslog events</h3><p>This device does not have any syslog entries. Check that the correct set syslog server and config option. See <a href="http://www.observium.org/wiki/Category:Documentation" target="_blank">documentation</a> and <a href="http://www.observium.org/wiki/Configuration_Options#Syslog_Settings" target="_blank">config options</a>.</p></div>');
}

$pagetitle[] = 'Syslog';

?>
