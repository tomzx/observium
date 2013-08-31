<div class="row">
<div class="col-md-12">

<?php
unset($search, $devices, $parts);

// Select devices only with Inventory parts
foreach (dbFetchRows('SELECT D.device_id AS device_id, `hostname`, `entPhysicalModelName`
                     FROM `entPhysical` AS E
                     LEFT JOIN `devices` AS D ON D.device_id = E.device_id
                     GROUP BY device_id, entPhysicalModelName;') as $data)
{
  $device_id = $data['device_id'];
  // Exclude not permited devices
  if (isset($cache['devices']['id'][$device_id]))
  {
    if ($cache['devices']['id'][$device_id]['disabled'] && !$config['web_show_disabled']) { continue; }
    $devices[$device_id] = $data['hostname'];
    if ($data['entPhysicalModelName'] != '')
    {
      $parts[$data['entPhysicalModelName']] = $data['entPhysicalModelName'];
    }
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
//Parts field
ksort($parts);
$search[] = array('type'    => 'multiselect',
                  'width'   => '160px',
                  'name'    => 'Parts',
                  'id'      => 'parts',
                  'value'   => $vars['parts'],
                  'values'  => $parts);
//Serial field
$search[] = array('type'    => 'text',
                  'width'   => '160px',
                  'name'    => 'Serial',
                  'id'      => 'serial',
                  'value'   => $vars['serial']);
//Description field
$search[] = array('type'    => 'text',
                  'width'   => '160px',
                  'name'    => 'Desc',
                  'id'      => 'description',
                  'value'   => $vars['description']);

print_search_simple($search, 'Inventory');

// Pagination
$vars['pagination'] = TRUE;
if(!$vars['pagesize']) { $vars['pagesize'] = 100; }
if(!$vars['pageno']) { $vars['pageno'] = 1; }

print_inventory($vars);

$pagetitle[] = 'Inventory';

?>

  </div> <!-- col-md-12 -->
</div> <!-- row -->
