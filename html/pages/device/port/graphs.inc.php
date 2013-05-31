<?php
$rrdfile = get_port_rrdfilename($device, $port);

if (file_exists($rrdfile))
{
  $iid = $id;
  echo("<h4>Interface Traffic</h4>");
  $graph_array['type'] = "port_bits";

  print_graph_row_port($graph_array, $port);


  echo("<h4>Interface Packets</h4>");
  $graph_array['type'] = "port_upkts";

  print_graph_row_port($graph_array, $port);

  echo("<h4>Interface Non Unicast</h4>");
  $graph_array['type'] = "port_nupkts";

  print_graph_row_port($graph_array, $port);

  echo("<h4>Interface Errors</h4>");
  $graph_array['type'] = "port_errors";

  print_graph_row_port($graph_array, $port);

  if (is_file(get_port_rrdfilename($device, $port, "dot3")))
  {
    echo("<h4>Ethernet Errors</h4>");
    $graph_array['type'] = "port_etherlike";

    print_graph_row_port($graph_array, $port);

  }
}

?>
