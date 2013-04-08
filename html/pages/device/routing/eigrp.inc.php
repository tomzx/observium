<?php

$navbar = array();
$navbar['brand'] = "EIGRP";
$navbar['class'] = "navbar-narrow";

foreach (array("vpns", "ases", "ports", "neighbours") as $type)
{
  if (!$vars['view']) { $vars['view'] = $type; }
  $navbar['options'][$type]['url']  = generate_url(array('page' => 'device', 'device' => $device['device_id'], 'tab' => 'routing', 'proto' => 'eigrp', 'view' => $type ));
  $navbar['options'][$type]['text'] = nicecase($type);
  if ($vars['view'] == $type) { $navbar['options'][$type]['class'] = "active"; }
}
print_navbar($navbar);
unset($navbar);

switch($vars['view'])
{

  case "ports":
    include("eigrp_ports.inc.php");
    break;

}

?>
