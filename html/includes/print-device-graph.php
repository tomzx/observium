<?php

if(empty($graph_array['type'])) { $graph_array['type'] = $graph_type; }
if(empty($graph_array['device']))   { $graph_array['device'] = $device['device_id']; }

// FIXME not css alternating yet
if (is_integer($g_i/2)) { $row_colour = $list_colour_a; } else { $row_colour = $list_colour_b; }

echo('<tr><td>');

echo('<h4>'.$graph_title.'</h4>');

include("includes/print-graphrow.inc.php");

echo('</td></tr>');

$g_i++;

?>

