<?php

if(is_numeric($vars['vsvr']))
{

#print_optionbar_start();
#echo("<span style='font-weight: bold;'>VServer</span> &#187; ");
#echo('<a href="'.generate_url($vars, array('vsvr' => NULL)).'">All</a>');
#print_optionbar_end();

$graph_types = array("bits"   => "Bits",
                     "pkts"   => "Packets",
                     "conns"  => "Connections",
                     "reqs"   => "Requests",
                     "hitmiss" => "Hit/Miss");

echo("<div style='margin: 5px;'>");
echo("<table class=\"table table-striped table-condensed\" style=\"margin-top: 10px;\">\n");
foreach (dbFetchRows("SELECT * FROM `netscaler_vservers` WHERE `device_id` = ? AND `vsvr_id` = ? ORDER BY `vsvr_name`", array($device['device_id'], $vars['vsvr'])) as $vsvr)
{

  if ($vsvr['vsvr_state'] == "up") { $vsvr_class="green"; } else { $vsvr_class="red"; }

  echo("<tr>");
  echo('<td width=320 class=list-large><a href="'.generate_url($vars, array('vsvr' => $vsvr['vsvr_id'], 'view' => NULL, 'graph' => NULL)).'">' . $vsvr['vsvr_name'] . '</a></td>');
  echo("<td width=320 class=list-small>" . $vsvr['vsvr_ip'] . ":" . $vsvr['vsvr_port'] . "</a></td>");
  echo("<td width=100 class=list-small><span class='".$vsvr_class."'>" . $vsvr['vsvr_state'] . "</span></td>");
  echo("<td width=320 class=list-small>" . format_si($vsvr['vsvr_bps_in']*8) . "bps</a></td>");
  echo("<td width=320 class=list-small>" . format_si($vsvr['vsvr_bps_out']*8) . "bps</a></td>");
  echo("</tr>");

  $svcs = dbFetchRows("SELECT * FROM `netscaler_services_vservers` AS SV, `netscaler_services` AS S WHERE S.svc_name = SV.svc_name AND SV.vsvr_name = '".$vsvr['vsvr_name']."'");
  if(count($svcs))
  {
    echo('<tr><td colspan="5">');
    echo("<table class=\"table table-striped table-condensed\" style=\"margin-top: 10px;\">\n");
    echo("  <thead>\n");
    echo("    <th>Service</th>");
    echo("    <th>Address</th>");
    echo("    <th>Status</th>");
    echo("    <th>Input</th>");
    echo("    <th>Output</th>");

    echo("  </thead>");
    foreach ($svcs as $svc)
    {
      if ($svc['svc_state'] == "up") { $svc_class="green"; unset($svc_row); } else { $svc_class="red"; $svc_row = "error"; }
      echo('<tr class="'.$svc_row.'">');
      echo('<td width=320 class=list-large><a href="'.generate_url($vars, array('svc' => $svc['svc_id'], 'view' => NULL, 'graph' => NULL)).'">' . $svc['svc_name'] . '</a></td>');
      echo("<td width=320 class=list-small>" . $svc['svc_ip'] . ":" . $svc['svc_port'] . "</a></td>");
      echo("<td width=100 class=list-small><span class='".$svc_class."'>" . $svc['svc_state'] . "</span></td>");
      echo("<td width=320 class=list-small>" . format_si($svc['svc_bps_in']*8) . "bps</a></td>");
      echo("<td width=320 class=list-small>" . format_si($svc['svc_bps_out']*8) . "bps</a></td>");
      echo("</td></tr>");
    }
    echo("</table>");
    echo("</tr>");
  }

  foreach ($graph_types as $graph_type => $graph_text)
  {
    $i++;
    echo('<tr class="list-bold">');
    echo('<td colspan="5">');
    $graph_type = "netscalervsvr_" . $graph_type;
    $graph_array['height'] = "100";
    $graph_array['width']  = "213";
    $graph_array['to']     = $config['time']['now'];
    $graph_array['id']     = $vsvr['vsvr_id'];
    $graph_array['type']   = $graph_type;

    echo('<h3>'.$graph_text.'</h3>');

    include("includes/print-graphrow.inc.php");

    echo("
    </td>
    </tr>");
  }
}

echo("</table></div>");

} else {

print_optionbar_start();

echo("<span style='font-weight: bold;'>VServers</span> &#187; ");

$menu_options = array('basic' => 'Basic',
                      'services' => 'Services',
                      );

if (!$vars['view']) { $vars['view'] = "basic"; }

$sep = "";
foreach ($menu_options as $option => $text)
{
  if ($vars['view'] == $option) { echo("<span class='pagemenu-selected'>"); }
  echo('<a href="'.generate_url($vars, array('view' => $option, 'graph' => NULL)).'">'.$text.'</a>');
  if ($vars['view'] == $option) { echo("</span>"); }
  echo(" | ");
}

unset($sep);
echo(' Graphs: ');
$graph_types = array("bits"   => "Bits",
                     "pkts"   => "Packets",
                     "conns"  => "Connections",
                     "reqs"   => "Requests",
                     "hitmiss" => "Hit/Miss");

foreach ($graph_types as $type => $descr)
{
  echo("$type_sep");
  if ($vars['graph'] == $type) { echo("<span class='pagemenu-selected'>"); }
  echo('<a href="'.generate_url($vars, array('view' => 'graphs', 'graph' => $type)).'">'.$descr.'</a>');
  if ($vars['graph'] == $type) { echo("</span>"); }
  $type_sep = " | ";
}

print_optionbar_end();

echo("<div style='margin: 5px;'>");
echo("<table class=\"table table-striped table-condensed\" style=\"margin-top: 10px;\">\n");
echo("  <thead>\n");
echo("    <tr>\n");
echo("      <th>VServer</th>\n");
echo("      <th>Address</th>\n");
echo("      <th>Status</th>\n");
echo("      <th>Input</th>\n");
echo("      <th>Output</th>\n");
echo("    </tr>");
echo("  </thead>");
$i = "0";
foreach (dbFetchRows("SELECT * FROM `netscaler_vservers` WHERE `device_id` = ? ORDER BY `vsvr_name`", array($device['device_id'])) as $vsvr)
{
  if (is_integer($i/2)) { $bg_colour = $list_colour_a; } else { $bg_colour = $list_colour_b; }

  if ($vsvr['vsvr_state'] == "up") { $vsvr_class="green"; } else { $vsvr_class="red"; }

  echo("<tr>");
  echo('<td width=320 class=list-large><a href="'.generate_url($vars, array('vsvr' => $vsvr['vsvr_id'], 'view' => NULL, 'graph' => NULL)).'">' . $vsvr['vsvr_name'] . '</a></td>');
  echo("<td width=320 class=list-small>" . $vsvr['vsvr_ip'] . ":" . $vsvr['vsvr_port'] . "</a></td>");
  echo("<td width=100 class=list-small><span class='".$vsvr_class."'>" . $vsvr['vsvr_state'] . "</span></td>");
  echo("<td width=320 class=list-small>" . format_si($vsvr['vsvr_bps_in']*8) . "bps</a></td>");
  echo("<td width=320 class=list-small>" . format_si($vsvr['vsvr_bps_out']*8) . "bps</a></td>");
  echo("</tr>");
  if ($vars['view'] == "services")
  {
   $svcs = dbFetchRows("SELECT * FROM `netscaler_services_vservers` AS SV, `netscaler_services` AS S WHERE S.svc_name = SV.svc_name AND SV.vsvr_name = '".$vsvr['vsvr_name']."'");
   if(count($svcs))
   {
    echo('<tr><td colspan="5">');
    echo("<table class=\"table table-bordered table-striped table-condensed\" style=\"margin-top: 10px;\">\n");
    echo("  <thead>\n");
    echo("    <th>Service</th>");
    echo("    <th>Address</th>");
    echo("    <th>Status</th>");
    echo("    <th>Input</th>");
    echo("    <th>Output</th>");

    echo("  </thead>");
    foreach ($svcs as $svc)
    {
      if ($svc['svc_state'] == "up") { $svc_class="green"; unset($svc_row); $svc_row = "error";} else { $svc_class="red"; $svc_row = "error"; }
      echo('<tr class="'.$svc_row.'">');
      echo('<td width=320 class=list-large><a href="'.generate_url($vars, array('svc' => $svc['svc_id'], 'view' => NULL, 'graph' => NULL)).'">' . $svc['svc_name'] . '</a></td>');
      echo("<td width=320 class=list-small>" . $svc['svc_ip'] . ":" . $svc['svc_port'] . "</a></td>");
      echo("<td width=100 class=list-small><span class='".$svc_class."'>" . $svc['svc_state'] . "</span></td>");
      echo("<td width=320 class=list-small>" . format_si($svc['svc_bps_in']*8) . "bps</a></td>");
      echo("<td width=320 class=list-small>" . format_si($svc['svc_bps_out']*8) . "bps</a></td>");
      echo("</td></tr>");
    }
    echo("</table>");
    echo("</tr>");
   }

  }
  if ($vars['view'] == "graphs")
  {
    echo('<tr class="list-bold" bgcolor="'.$bg_colour.'">');
    echo('<td colspan="5">');
    $graph_type = "netscalervsvr_" . $vars['graph'];
    $graph_array['height'] = "100";
    $graph_array['width']  = "213";
    $graph_array['to']     = $config['time']['now'];
    $graph_array['id']     = $vsvr['vsvr_id'];
    $graph_array['type']   = $graph_type;

    include("includes/print-graphrow.inc.php");

    echo("
    </td>
    </tr>");
  }

echo("</td>");
echo("</tr>");

  $i++;
}

echo("</table></div>");

}

?>
