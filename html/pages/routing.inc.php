<?php

$pagetitle[] = "Routing";

if ($_GET['optb'] == "graphs" || $_GET['optc'] == "graphs") { $graphs = "graphs"; } else { $graphs = "nographs"; }

#$datas[] = 'overview';

// $routing_count is populated by print-menubar.inc.php

$navbar['brand'] = "Routing";
$navbar['class'] = "navbar-narrow";

foreach ($routing_count as $type => $value)
{
  if($value > 0)
  {
    if (!$vars['protocol']) { $vars['protocol'] = $type; }
    if ($vars['protocol'] == $type) { $navbar['options'][$type]['class'] = "active"; }

    $navbar['options'][$type]['url']  = generate_url(array('page' => 'routing', 'protocol' => $type));
    $navbar['options'][$type]['text'] = $config['routing_types'][$type]['text'].' ('.$routing_count[$type].')';
  }
}
print_navbar($navbar);

switch ($vars['protocol'])
{
  case 'bgp':
  case 'vrf':
  case 'cef':
  case 'ospf':
    include('pages/routing/'.$vars['protocol'].'.inc.php');
    break;
  default:
    bug();
    break;
}

?>
