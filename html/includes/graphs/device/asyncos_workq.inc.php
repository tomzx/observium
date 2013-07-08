<?php

$rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/" . safename("asyncos_workq.rrd");

include("includes/graphs/common.inc.php");

$ds = "DEPTH";

$colour_area = "9999cc";
$colour_line = "0000cc";

$colour_area_max = "9999cc";

$unit_text = "Messages";

include("includes/graphs/generic_simplex.inc.php");

// End
