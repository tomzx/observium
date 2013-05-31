<?php

// FIXME - do this in a function and/or do it in graph-realtime.php

if(!isset($vars['interval'])) {
  if ($device['os'] == "linux") {
    $vars['interval'] = "15";
  } else {
    $vars['interval'] = "2";
  }
}

$navbar['class'] = "navbar-narrow";
$navbar['brand'] = "Polling Interval";

foreach (array(0.25, 1, 2, 5, 15, 60) as $interval)
{
  if ($vars['interval'] == $interval) { $navbar['options'][$interval]['class'] = "active"; }
  $navbar['options'][$interval]['url'] = generate_url($link_array,array('view'=>'realtime','interval'=>$interval));
  $navbar['options'][$interval]['text'] = $interval."s";
}

print_navbar($navbar);

?>

<div align="center" style="margin: 30px;">
<object data="graph-realtime.php?type=bits&id=<?php echo($port['port_id'] . "&interval=".$vars['interval']); ?>" type="image/svg+xml" width="1000" height="400">
<param name="src" value="graph.php?type=bits&id=<?php echo($port['port_id'] . "&interval=".$vars['interval']); ?>" />
Your browser does not support the type SVG! You need to either use Firefox or download the Adobe SVG plugin.
</object>
</div>
