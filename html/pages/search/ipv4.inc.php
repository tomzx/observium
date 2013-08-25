
<div class="row">
<div class="col-md-12">

<?php
unset($search, $devices);

$devices[''] = 'All Devices';
foreach ($cache['devices']['hostname'] as $hostname => $device_id)
{
  if ($cache['devices']['id'][$device_id]['disabled'] && !$config['web_show_disabled']) { continue; }
  $devices[$device_id] = $hostname;
}
//Device field
$search[] = array('type'    => 'select',
                  'name'    => 'Device',
                  'id'      => 'device_id',
                  'value'   => $vars['device_id'],
                  'values'  => $devices);
//Interface field
$search[] = array('type'    => 'select',
                  'name'    => 'Interface',
                  'id'      => 'interface',
                  'width'   => '130px',
                  'value'   => $vars['interface'],
                  'values'  => array('' => 'All Interfaces', 'Loopback%' => 'Loopbacks', 'Vlan%' => 'Vlans'));
////IP version field
//$search[] = array('type'    => 'select',
//                  'name'    => 'IP',
//                  'id'      => 'ip_version',
//                  'width'   => '120px',
//                  'value'   => $vars['ip_version'],
//                  'values'  => array('' => 'IPv4 & IPv6', '4' => 'IPv4 only', '6' => 'IPv6 only'));
//IP address field
$search[] = array('type'    => 'text',
                  'name'    => 'IP Address',
                  'id'      => 'address',
                  'value'   => $vars['address']);

print_search_simple($search, 'IPv4');

// Pagination
$vars['pagination'] = TRUE;
if(!$vars['pagesize']) { $vars['pagesize'] = "100"; }
if(!$vars['pageno']) { $vars['pageno'] = "1"; }

// Print addresses
print_addresses($vars);

$pagetitle[] = "IPv4 Addresses";

?>

  </div> <!-- col-md-12 -->

</div> <!-- row -->
