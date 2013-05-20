<div class="row">
<div class="span12">

<?php

///FIXME. Mike: should be more checks, at least a confirmation click.
//if ($vars['action'] == "expunge" && $_SESSION['userlevel'] >= '10')
//{
//  dbFetchCell("TRUNCATE TABLE `syslog`");
//  print_message('Syslog truncated');
//}

unset($search, $devices, $priorities, $programs);

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
                  //'name'    => 'Priority',
                  'id'      => 'priority',
                  'width'   => '130px',
                  'value'   => $vars['priority'],
                  'values'  => $priorities);
//Program field
$programs[''] = 'All Programs';
$where = ($vars['device_id']) ? 'WHERE `device_id` = ' . $vars['device_id'] : '';
foreach (dbFetchRows('SELECT `program` FROM `syslog` ' . $where . ' GROUP BY `program` ORDER BY `program`') as $data)
{
  $program = ($data['program']) ? $data['program'] : '[[EMPTY]]';
  $programs[$program] = $program;
}
$search[] = array('type'    => 'select',
                  //'name'    => 'Program',
                  'id'      => 'program',
                  'width'   => '130px',
                  'value'   => $vars['program'],
                  'values'  => $programs);
//Device field
$devices[''] = 'All Devices';
// Show devices only with syslog messages
foreach (dbFetchRows('SELECT S.`device_id` AS `device_id`, hostname FROM `syslog` AS S
                      LEFT JOIN `devices` AS D ON S.device_id = D.device_id
                      GROUP BY `hostname` ORDER BY `hostname`') as $data)
{
  $device_id = $data['device_id'];
  // Exclude not permited devices
  if (isset($cache['devices']['id'][$device_id]))
  {
    if ($cache['devices']['id'][$device_id]['disabled'] && !$config['web_show_disabled']) { continue; }
    $devices[$device_id] = $data['hostname'];
  }
}
$search[] = array('type'    => 'select',
                  'name'    => 'Device',
                  'id'      => 'device_id',
                  'width'   => '140px',
                  'value'   => $vars['device_id'],
                  'values'  => $devices);
$search[] = array('type'    => 'newline');
$search[] = array('type'    => 'datetime',
                  'id'      => 'timestamp',
                  'presets' => TRUE,
                  'min'     => dbFetchCell('SELECT MIN(`timestamp`) FROM `syslog`'),
                  'max'     => dbFetchCell('SELECT MAX(`timestamp`) FROM `syslog`'),
                  'from'    => $vars['timestamp_from'],
                  'to'      => $vars['timestamp_to']);

print_search_simple($search, 'Syslog');

// Pagination
$vars['pagination'] = TRUE;
if(!$vars['pagesize']) { $vars['pagesize'] = 100; }
if(!$vars['pageno']) { $vars['pageno'] = 1; }

// Print syslog
print_syslogs($vars);

$pagetitle[] = 'Syslog';

?>
  </div> <!-- span12 -->

</div> <!-- row -->
