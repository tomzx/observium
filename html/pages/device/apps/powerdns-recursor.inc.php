<?php

global $config;

$graphs = array('powerdns-recursor_queries' => 'PowerDNS Recursor - Questions and answers per second',
                'powerdns-recursor_tcpqueries' => 'PowerDNS Recursor - TCP Questions and answers per second, unauthorized packets/s',
                'powerdns-recursor_errors' => 'PowerDNS Recursor - Packet errors per second',
                'powerdns-recursor_limits' => 'PowerDNS Recursor - Limitations per second',
                'powerdns-recursor_latency' => 'PowerDNS Recursor - Questions answered within latency',
                'powerdns-recursor_outqueries' => 'PowerDNS Recursor - Questions vs Outqueries',
                'powerdns-recursor_qalatency' => 'PowerDNS Recursor - Question/Answer latency in ms',
                'powerdns-recursor_timeouts' => 'PowerDNS Recursor - Corrupt / Failed / Timed out',
                'powerdns-recursor_cache' => 'PowerDNS Recursor - Cache sizes',
                'powerdns-recursor_load' => 'PowerDNS Recursor - Concurrent Queries',
/*                'powerdns-recursor_hitrate' => 'PowerDNS Recursor - Cache hitrate',*/ // FIXME have to fix up the graph def before uncomment
/*                'powerdns-recursor_cpuload' => 'PowerDNS Recursor - CPU load',*/ // FIXME have to fix up the graph def before uncomment
               );

foreach ($graphs as $key => $text)
{
  $graph_type            = $key;
  $graph_array['to']     = $config['time']['now'];
  $graph_array['id']     = $app['app_id'];
  $graph_array['type']   = "application_".$key;

  echo('<h4>'.$text.'</h3>');

  echo("<tr bgcolor='$row_colour'><td colspan=5>");

  include("includes/print-graphrow.inc.php");

  echo("</td></tr>");
}

?>
