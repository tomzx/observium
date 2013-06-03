<?php

$pagetitle[] = "Search";

$sections = array('ipv4' => 'IPv4 Address', 'ipv6' => 'IPv6 Address', 'mac' => 'MAC Address', 'arp' => 'ARP/NDP Tables', 'fdb' => 'FDB Tables');

$navbar['brand'] = "Search";
$navbar['class'] = "navbar-narrow";

foreach ($sections as $section => $text)
{
  $type = strtolower($section);
  if (!isset($vars['search'])) { $vars['search'] = $section; }

  if ($vars['search'] == $section) { $navbar['options'][$section]['class'] = "active"; }
  $navbar['options'][$section]['url'] = generate_url(array('page' => 'search', 'search' => $section));
  $navbar['options'][$section]['text'] = $text;
}

print_navbar($navbar);

/// Little switch to provide some sanity checking.
switch ($vars['search'])
{
  case 'ipv4':
  case 'ipv6':
  case 'mac':
  case 'arp':
  case 'fdb':
    include('pages/search/'.$vars['search'].'.inc.php');
    break;
  default:
    echo("<h2>Error. Please report this to observium developers.</h2>");
    break;
}

?>
