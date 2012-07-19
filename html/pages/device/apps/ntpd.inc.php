<?php

global $config;

$rrd_server = $config['rrd_dir'] . "/" . $device['hostname'] . "/app-ntpd-server-".$app['app_id'].".rrd";
$rrd_client = $config['rrd_dir'] . "/" . $device['hostname'] . "/app-ntpd-client-".$app['app_id'].".rrd";
$ntpd_type = (file_exists($rrd_server) ? "server" : "client");

if ($ntpd_type == "server") {
  $app_sections = array('server' => "System",
                        'buffer' => "Buffer",
                        'packets' => "Packets");
} else {
  $app_sections = array('client' => "System");
}

print_optionbar_start();
echo('<span style="font-weight: bold;">'.$app["app_type"].'</span> &#187; ');
unset($sep);
foreach ($app_sections as $app_section => $app_section_text)
{
  echo($sep);
  if (!$vars['app_section']) { $vars['app_section'] = $app_section; }
  if ($vars['app_section'] == $app_section)
  {
    echo("<span class='pagemenu-selected'>");
  }
  echo(generate_link(ucfirst($app_section),$vars,array('app_section'=>$app_section)));
  if ($vars['app_section'] == $app_section) { echo("</span>"); }
  $sep = " | ";
}
print_optionbar_end();

$graphs['client'] = array('ntpd_stats'  => 'NTP Client - Statistics',
                          'ntpd_freq' => 'NTP Client - Frequency');

$graphs['server'] = array('ntpd_stats'  => 'NTPD Server - Statistics',
                          'ntpd_freq' => 'NTPD Server - Frequency',
                          'ntpd_uptime' => 'NTPD Server - Uptime',
                          'ntpd_stratum' => 'NTPD Server - Stratum');

$graphs['buffer'] = array('ntpd_buffer' => 'NTPD Server - Buffer');

$graphs['packets'] = array('ntpd_bits' => 'NTPD Server - Packets Sent/Received',
                           'ntpd_packets' => 'NTPD Server - Packets Dropped/Ignored');

foreach ($graphs[$vars['app_section']] as $key => $text) {
  $graph_type            = $key;
  $graph_array['height'] = "100";
  $graph_array['width']  = "215";
  $graph_array['to']     = $config['time']['now'];
  $graph_array['id']     = $app['app_id'];
  $graph_array['type']   = "application_".$key;
  echo("<h3>".$text."</h3>");
  echo("<tr bgcolor='$row_colour'><td colspan=5>");

  include("includes/print-graphrow.inc.php");

  echo("</td></tr>");
}

?>

