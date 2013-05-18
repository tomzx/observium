<?php

echo('<h2>'.$app['app_instance'].'</h2>');

$graphs = array('drbd_network_bits' => 'Network Traffic',
                'drbd_disk_bits' => 'Disk Traffic',
                'drbd_unsynced' => 'Unsynced Data',
                'drbd_queue' => 'Queues');

foreach ($graphs as $key => $text)
{

  $graph_array['to']     = $config['time']['now'];
  $graph_array['id']     = $app['app_id'];
  $graph_array['type']   = "application_".$key;

  echo('<h4>'.$text.'</h4>');

  echo("<tr bgcolor='$row_colour'><td colspan=5>");

  include("includes/print-graphrow.inc.php");

  echo("</td></tr>");
}

?>
