<?php
unset($search, $types);

//Message field
$search[] = array('type'    => 'text',
                  'name'    => 'Message',
                  'id'      => 'message',
                  'value'   => $vars['message']);
//Type field
$types[''] = 'All Types';
$types['system'] = 'System';
foreach (dbFetchRows('SELECT `type` FROM `eventlog` WHERE `device_id` = ? GROUP BY `type` ORDER BY `type`', array($vars['device'])) as $data)
{
  $type = $data['type'];
  $types[$type] = ucfirst($type);
}
$search[] = array('type'    => 'select',
                  'name'    => 'Type',
                  'id'      => 'type',
                  'width'   => '130px',
                  'value'   => $vars['type'],
                  'values'  => $types);

print_search_simple($search, 'Eventlog');

/// Pagination
$vars['pagination'] = TRUE;
if(!$vars['pagesize']) { $vars['pagesize'] = "100"; }
if(!$vars['pageno']) { $vars['pageno'] = "1"; }

print_events($vars);

$pagetitle[] = "Events";

?>
