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
                  'name'    => 'Devices',
                  'id'      => 'device_id',
                  'width'   => '160px',
                  'value'   => $vars['device_id'],
                  'values'  => $devices);
//Interface field
$search[] = array('type'    => 'select',
                  'name'    => 'Interface',
                  'id'      => 'interface',
                  'width'   => '160px',
                  'value'   => $vars['interface'],
                  'values'  => array('' => 'All Interfaces', 'Loopback%' => 'Loopbacks', 'Vlan%' => 'Vlans'));
//MAC address field
$search[] = array('type'    => 'text',
                  'name'    => 'MAC Address',
                  'id'      => 'address',
                  'width'   => '160px',
                  'value'   => $vars['address']);

print_search_simple($search, 'MAC Addresses');

// Pagination
$vars['pagination'] = TRUE;
if(!$vars['pagesize']) { $vars['pagesize'] = "100"; }
if(!$vars['pageno']) { $vars['pageno'] = "1"; }

// Print MAC addresses
print_mac_addresses($vars);

$pagetitle[] = 'MAC addresses';

?>

  </div> <!-- col-md-12 -->

</div> <!-- row -->