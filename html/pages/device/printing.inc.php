<?php

$graph_title = "Toner";
$graph_type = "device_toner";

include("includes/print-device-graph.php");

unset($graph_array);

if (get_dev_attrib($device, "pagecount_oid"))
{
  $graph_title = "Pagecounter";
  $graph_type = "device_pagecount";

  include("includes/print-device-graph.php");
}

$pagetitle[] = "Printing";

?>

