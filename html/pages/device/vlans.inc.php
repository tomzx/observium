<?php

$link_array = array('page'    => 'device',
                    'device'  => $device['device_id'],
                    'tab' => 'vlans');

if (isset($vars['graph'])) { $graph_type = "port_" . $vars['graph']; } else { $graph_type = "port_bits"; }
if (!$vars['view']) { $vars['view'] = "basic"; }



$navbar['brand'] = 'VLANs';
$navbar['class'] = 'navbar-narrow';

if ($vars['view'] == 'basic') { $navbar['options']['basic']['class'] = 'active'; }
$navbar['options']['basic']['url'] = generate_url($link_array,array('view'=>'basic','graph'=> NULL));
$navbar['options']['basic']['text'] = 'No Graphs';

foreach ($config['graph_types']['port'] as $type => $data)
{

  if ($vars['graph'] == $type && $vars['view'] == "graphs") { $navbar['options'][$type]['class'] = "active"; }
  $navbar['options'][$type]['url'] = generate_url($link_array,array('view'=>'graphs','graph'=>$type));
  $navbar['options'][$type]['text'] = $data['name'];

}

print_navbar($navbar);



echo('<table border="0" cellspacing="0" cellpadding="5" width="100%">');

$i = "1";

foreach (dbFetchRows("SELECT * FROM `vlans` WHERE `device_id` = ? ORDER BY 'vlan_vlan'", array($device['device_id'])) as $vlan)
{
  include("includes/print-vlan.inc.php");

  $i++;
}

echo("</table>");

$pagetitle[] = "VLANs";

?>
