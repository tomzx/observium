<?php

if(!isset($vars['section'])) { $vars['section'] = 'eventlog'; }

$sections = array('eventlog', 'syslog');

$navbar['brand'] = "Logging";
$navbar['class'] = "navbar-narrow";

foreach ($sections as $section)
{
  $type = strtolower($section);
  if (!isset($vars['section'])) { $vars['section'] = $section; }

  if ($vars['section'] == $section) { $navbar['options'][$section]['class'] = "active"; }
  $navbar['options'][$section]['url'] = generate_url(array('page' => 'device', 'device' => $device['device_id'], 'tab' => 'logs',  'section' => $section));
  $navbar['options'][$section]['text'] = nicecase($section);
}

print_navbar($navbar);

switch ($vars['section'])
{
  case 'syslog':
  case 'eventlog':
    include('pages/device/logs/'.$vars['section'].'.inc.php');
    break;
  default:
    echo('<h2>Error. No section '.$vars['section'].'.<br /> Please report this to observium developers.</h2>');
    break;
}

?>
