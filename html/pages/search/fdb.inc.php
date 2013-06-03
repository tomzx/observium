<div class="row">
<div class="span12">

<?php
unset($search, $devices);

$devices[''] = 'All Devices';
// Select the devices only with ARP/NDP tables
foreach (dbFetchRows('SELECT D.device_id AS device_id, `hostname`
                     FROM `vlans_fdb` AS V
                     LEFT JOIN `devices` AS D ON V.device_id = D.device_id
                     GROUP BY `device_id`
                     ORDER BY `hostname`;') as $data)
{
  $device_id = $data['device_id'];
  // Exclude not permited devices
  if (isset($cache['devices']['id'][$device_id]))
  {
    if ($cache['devices']['id'][$device_id]['disabled'] && !$config['web_show_disabled']) { continue; }
    $devices[$device_id] = $data['hostname'];
  }
}
//Device field
$search[] = array('type'    => 'select',
                  'name'    => 'Device',
                  'id'      => 'device_id',
                  'value'   => $vars['device_id'],
                  'values'  => $devices);

$search[] = array('type'    => 'text',
                  'name'    => 'MAC Address',
                  'id'      => 'address',
                  'value'   => $vars['address']);

print_search_simple($search);

// Pagination
$vars['pagination'] = TRUE;
if(!$vars['pagesize']) { $vars['pagesize'] = 100; }
if(!$vars['pageno']) { $vars['pageno'] = 1; }

print_fdbtable($vars);

$pagetitle[] = "FDB Search";

?>

  </div> <!-- span12 -->
</div> <!-- row -->