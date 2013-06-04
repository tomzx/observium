<div class="row">
<div class="span12">

<?php
unset($search, $devices, $vlans, $vlan_names);

// Select devices and vlans only with FDB tables
foreach (dbFetchRows('SELECT D.device_id AS device_id, `hostname`, vlan_vlan, vlan_name
                     FROM `vlans_fdb` AS F
                     LEFT JOIN `vlans` as V ON V.vlan_vlan = F.vlan_id AND V.device_id = F.device_id 
                     LEFT JOIN `devices` AS D ON D.device_id = F.device_id
                     GROUP BY device_id, vlan_vlan;') as $data)
{
  $device_id = $data['device_id'];
  // Exclude not permited devices
  if (isset($cache['devices']['id'][$device_id]))
  {
    if ($cache['devices']['id'][$device_id]['disabled'] && !$config['web_show_disabled']) { continue; }
    $devices[$device_id] = $data['hostname'];
    $vlans[$data['vlan_vlan']] = 'Vlan' . $data['vlan_vlan'];
    $vlan_names[$data['vlan_name']] = $data['vlan_name'];
  }
}
//Device field
natcasesort($devices);
$search[] = array('type'    => 'select',
                  'width'   => '160px',
                  'name'    => 'Devices',
                  'id'      => 'device_id',
                  'value'   => $vars['device_id'],
                  'values'  => $devices);
//Vlans field
ksort($vlans);
$search[] = array('type'    => 'multiselect',
                  'name'    => 'VLANs',
                  'id'      => 'vlan_id',
                  'value'   => $vars['vlan_id'],
                  'values'  => $vlans);
//Vlan names field
natcasesort($vlan_names);
$search[] = array('type'    => 'multiselect',
                  'width'   => '160px',
                  'name'    => 'VLAN names',
                  'id'      => 'vlan_name',
                  'value'   => $vars['vlan_name'],
                  'values'  => $vlan_names);
//MAC address field
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