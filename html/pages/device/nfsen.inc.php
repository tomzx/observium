<?php

$datas = array(
  'Traffic' => 'nfsen_traffic',
  'Packets' => 'nfsen_packets',
  'Flows' => 'nfsen_flows'
);

foreach ($datas as $name=>$type)
{
  $graph_title = $name;
  $graph_array['type'] = "device_".$type;

  include("includes/print-device-graph.php");
}

$pagetitle[] = "Netflow";

?>
