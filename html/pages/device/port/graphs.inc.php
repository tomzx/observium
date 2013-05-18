<?php
$rrdfile = get_port_rrdfilename($device, $port);

if (file_exists($rrdfile))
{
  $iid = $id;
  echo("<div class=graphhead>Interface Traffic</div>");
  $graph_array['type'] = "port_bits";

  print_graph_row_port($graph_array, $port);


  echo("<div class=graphhead>Interface Packets</div>");
  $graph_array['type'] = "port_upkts";

  print_graph_row_port($graph_array, $port);

  echo("<div class=graphhead>Interface Non Unicast</div>");
  $graph_array['type'] = "port_nupkts";

  print_graph_row_port($graph_array, $port);

  echo("<div class=graphhead>Interface Errors</div>");
  $graph_array['type'] = "port_errors";

  print_graph_row_port($graph_array, $port);

  if (is_file(get_port_rrdfilename($device, $port, "dot3")))
  {
    echo("<div class=graphhead>Ethernet Errors</div>");
    $graph_array['type'] = "port_etherlike";

    print_graph_row_port($graph_array, $port);

  }
}

?>
