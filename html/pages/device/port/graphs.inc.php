<?php
$rrdfile = get_port_rrdfilename($device, $port);

if (file_exists($rrdfile))
{
  $iid = $id;
  echo("<div class=graphhead>Interface Traffic</div>");
  $graph_type = "port_bits";

  include("includes/print-interface-graphs.inc.php");

  echo("<div class=graphhead>Interface Packets</div>");
  $graph_type = "port_upkts";

  include("includes/print-interface-graphs.inc.php");

  echo("<div class=graphhead>Interface Non Unicast</div>");
  $graph_type = "port_nupkts";

  include("includes/print-interface-graphs.inc.php");

  echo("<div class=graphhead>Interface Errors</div>");
  $graph_type = "port_errors";

  include("includes/print-interface-graphs.inc.php");

  if (is_file(get_port_rrdfilename($device, $port, "dot3")))
  {
    echo("<div class=graphhead>Ethernet Errors</div>");
    $graph_type = "port_etherlike";

    include("includes/print-interface-graphs.inc.php");
  }
}

?>
