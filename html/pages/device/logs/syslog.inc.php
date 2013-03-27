
<hr />

<?php

unset($search, $priorities, $programs);

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

print_search_simple($search);

// Pagination
$vars['pagination'] = TRUE;
if(!$vars['pagesize']) { $vars['pagesize'] = 100; }
if(!$vars['pageno']) { $vars['pageno'] = 1; }

// Print syslog
print_syslogs($vars);

$pagetitle[] = 'Syslog';

?>
