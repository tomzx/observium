<div class="row">
<div class="col-md-12">

<?php

///FIXME. Mike: should be more checks, at least a confirmation click.
//if ($vars['action'] == "expunge" && $_SESSION['userlevel'] >= '10')
//{
//  dbFetchCell('TRUNCATE TABLE `eventlog`');
//  print_message('Event log truncated');
//}

unset($search, $devices, $types);

//Message field
$search[] = array('type'    => 'text',
                  'name'    => 'Message',
                  'id'      => 'message',
                  'value'   => $vars['message']);
//Type field
//$types[''] = 'All Types';
$types['system'] = 'System';
$where = ($vars['device_id']) ? 'WHERE `device_id` = ' . $vars['device_id'] : '';
foreach (dbFetchRows('SELECT `type` FROM `eventlog` ' . $where . ' GROUP BY `type` ORDER BY `type`') as $data)
{
  $type = $data['type'];
  $types[$type] = ucfirst($type);
}
$search[] = array('type'    => 'multiselect',
                  'name'    => 'Types',
                  'id'      => 'type',
                  'width'   => '160px',
                  'value'   => $vars['type'],
                  'values'  => $types);
//Device field
//$devices[''] = 'All Devices';
foreach ($cache['devices']['hostname'] as $hostname => $device_id)
{
  if ($cache['devices']['id'][$device_id]['disabled'] && !$config['web_show_disabled']) { continue; }
  $devices[$device_id] = $hostname;
}
$search[] = array('type'    => 'select',
                  'name'    => 'Devices',
                  'id'      => 'device_id',
                  'width'   => '160px',
                  'value'   => $vars['device_id'],
                  'values'  => $devices);
$search[] = array('type'    => 'newline');
$search[] = array('type'    => 'datetime',
                  'id'      => 'timestamp',
                  'presets' => TRUE,
                  'min'     => dbFetchCell('SELECT MIN(`timestamp`) FROM `eventlog`'),
                  'max'     => dbFetchCell('SELECT MAX(`timestamp`) FROM `eventlog`'),
                  'from'    => $vars['timestamp_from'],
                  'to'      => $vars['timestamp_to']);

print_search_simple($search, 'Eventlog');

// Pagination
$vars['pagination'] = TRUE;
if(!$vars['pagesize']) { $vars['pagesize'] = "100"; }
if(!$vars['pageno']) { $vars['pageno'] = "1"; }

// Print events
print_events($vars);

$pagetitle[] = 'Eventlog';

?>

  </div> <!-- col-md-12 -->

</div> <!-- row -->
