<?php

global $config;

$app_sections = array('stats' => "Server statistics",
                      'auth' => "Authoritative",
                      'resolv' => "Resolving",
                      'queries' => "Queries");

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
    echo(generate_link($app_section_text,$vars,array('app_section'=>$app_section)));
    if ($vars['app_section'] == $app_section) { echo("</span>"); }
    $sep = " | ";
}
print_optionbar_end();

$graphs['stats'] = array('bind_req_in'  => "Incoming requests",
                         'bind_answers' => "Answers Given",
                         'bind_updates' => "Dynamic Updates",
                         'bind_req_proto' => "Request protocol details");

$graphs['auth'] = array('bind_zone_maint' => "Zone maintenance");

$graphs['resolv'] = array('bind_resolv_queries' => "Queries",
                          'bind_resolv_errors' => "Errors",
                          'bind_resolv_rtt' => "Query RTT",
                          'bind_resolv_dnssec' => "DNSSEC validation");
                          

$graphs['queries'] = array('bind_query_rejected' => "Rejected queries",
                           'bind_query_in' => "Incoming queries",
                           'bind_query_out' => "Outgoing queries");

foreach ($graphs[$vars['app_section']] as $key => $text) {
    $graph_type            = $key;
    $graph_array['to']     = $config['time']['now'];
    $graph_array['id']     = $app['app_id'];
    $graph_array['type']   = "application_".$key;
    echo("<h4>".$text."</h3>");
    echo("<tr bgcolor='$row_colour'><td colspan=5>");

    include("includes/print-graphrow.inc.php");

    echo("</td></tr>");
}

?>
