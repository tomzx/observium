<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    observium
 * @subpackage webgui
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */


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
  //$priorities[''] = 'All Priorities';
  foreach (syslog_priorities() as $p => $priority)
  {
    if ($p > 7) { continue; }
    $priorities[$p] = ucfirst($priority['name']);
  }
  $search[] = array('type'    => 'multiselect',
                    'name'    => 'Priorities',
                    'id'      => 'priority',
                    'width'   => '160px',
                    'subtext' => TRUE,
                    'value'   => $vars['priority'],
                    'values'  => $priorities);
  //Program field
  //$programs[''] = 'All Programs';
  foreach (dbFetchRows('SELECT `program` FROM `syslog` WHERE `device_id` = ? GROUP BY `program` ORDER BY `program`', array($vars['device'])) as $data)
  {
    $program = ($data['program']) ? $data['program'] : '[[EMPTY]]';
    $programs[$program] = $program;
  }
  $search[] = array('type'    => 'multiselect',
                    'name'    => 'Programs',
                    'id'      => 'program',
                    'width'   => '160px',
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
?>

  <div class="alert alert-info">
    <h3>No syslog entries found!</h3>
    <p>This device does not have any syslog entries.
       Check that the syslog daemon and Observium configuration options are set correctly, that this device is configured to send syslog to Observium and that there are no firewalls blocking the messages.</p>
    <p>
       See <a href="http://www.observium.org/wiki/Category:Documentation" target="_blank">documentation</a> and
       <a href="http://www.observium.org/wiki/Configuration_Options#Syslog_Settings" target="_blank">configuration options</a> for more information.
    </p>
  </div>

<?php
}

$pagetitle[] = 'Syslog';

?>
