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

unset($graph_array);

if (get_dev_attrib($device, "imagingdrum_c_oid"))
{
  $graph_title = "Imaging Drums";
  $graph_type = "device_imagingdrums";

  include("includes/print-device-graph.php");
}
elseif (get_dev_attrib($device, "imagingdrum_oid"))
{
  $graph_title = "Imaging Drum";
  $graph_type = "device_imagingdrum";

  include("includes/print-device-graph.php");
}

unset($graph_array);

if (get_dev_attrib($device, "fuser_oid"))
{
  $graph_title = "Fuser";
  $graph_type = "device_fuser";

  include("includes/print-device-graph.php");
}

unset($graph_array);

if (get_dev_attrib($device, "transferroller_oid"))
{
  $graph_title = "Transfer Roller";
  $graph_type = "device_transferroller";

  include("includes/print-device-graph.php");
}

unset($graph_array);

if (get_dev_attrib($device, "wastebox_oid"))
{
  $graph_title = "Waste Toner Box";
  $graph_type = "device_wastebox";

  include("includes/print-device-graph.php");
}

$pagetitle[] = "Printing";

?>
