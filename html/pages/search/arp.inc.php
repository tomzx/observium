<div class="row">
<div class="span12">

<?php
unset($search, $devices);

$devices[''] = 'All Devices';
// Select the devices only with ARP/NDP tables
foreach (dbFetchRows('SELECT D.device_id AS device_id, `hostname`
                     FROM `ip_mac` AS M
                     LEFT JOIN `ports` AS P ON M.port_id = P.port_id
                     LEFT JOIN `devices` AS D ON P.device_id = D.device_id
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
                  'width'   => '130px',
                  'value'   => $vars['device_id'],
                  'values'  => $devices);
//Search by field
$search[] = array('type'    => 'select',
                  'name'    => 'Search By',
                  'id'      => 'searchby',
                  'width'   => '120px',
                  'value'   => $vars['searchby'],
                  'values'  => array('mac' => 'MAC Address', 'ip' => 'IP Address'));
//IP version field
$search[] = array('type'    => 'select',
                  'name'    => 'IP',
                  'id'      => 'ip_version',
                  'width'   => '120px',
                  'value'   => $vars['ip_version'],
                  'values'  => array('' => 'IPv4 & IPv6', '4' => 'IPv4 only', '6' => 'IPv6 only'));
//Address field
$search[] = array('type'    => 'text',
                  'name'    => 'Address',
                  'id'      => 'address',
                  'width'   => '120px',
                  'value'   => $vars['address']);

print_search_simple($search, 'ARP/NDP');

// Pagination
$vars['pagination'] = TRUE;
if(!$vars['pagesize']) { $vars['pagesize'] = 100; }
if(!$vars['pageno']) { $vars['pageno'] = 1; }

print_arptable($vars);

$pagetitle[] = 'ARP/NDP Search';

?>

  </div> <!-- span12 -->
</div> <!-- row -->