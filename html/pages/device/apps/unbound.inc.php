<?php

global $config;

$graphs = array('unbound_queries' => 'Unbound - DNS traffic and cache hits',
                'unbound_queue'   => 'Unbound - Queue statistics',
                'unbound_memory'  => 'Unbound - Memory statistics',
                'unbound_qtype'   => 'Unbound - Queries by Query type',
                'unbound_rcode'   => 'Unbound - Queries by Return code',
                'unbound_opcode'  => 'Unbound - Queries by Operation code',
                'unbound_class'   => 'Unbound - Queries by Query class',
                'unbound_flags'   => 'Unbound - Queries by Flags');

foreach ($graphs as $key => $text)
{
  $graph_type            = $key;
  $graph_array['height'] = "100";
  $graph_array['width']  = "215";
  $graph_array['to']     = $config['time']['now'];
  $graph_array['id']     = $app['app_id'];
  $graph_array['type']   = "application_".$key;

  echo('<h3>'.$text.'</h3>');

  echo("<tr bgcolor='$row_colour'><td colspan=5>");

  include("includes/print-graphrow.inc.php");

  echo("</td></tr>");
}

?>