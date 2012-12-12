<?php

echo('<table class="table table-striped table-condensed">');

$row = 1;

$sql  = "SELECT *, `sensors`.`sensor_id` AS `sensor_id`";
$sql .= " FROM  `sensors`";
$sql .= " LEFT JOIN  `sensors-state` ON  `sensors`.sensor_id =  `sensors-state`.sensor_id";
$sql .= " WHERE `sensor_class` = ? AND `device_id` = ?";

foreach (dbFetchRows($sql, array($class, $device['device_id'])) as $sensor)
{
  if (!is_integer($row/2)) { $row_colour = $list_colour_a; } else { $row_colour = $list_colour_b; }

  echo("<tr class=list-large style=\"background-color: $row_colour; padding: 5px;\">
          <td width=500>" . $sensor['sensor_descr'] . "</td>
          <td>" . $sensor['sensor_type'] . "</td>
          <td width=50>" . format_si($sensor['sensor_value']) .$unit. "</td>
          <td width=50>" . format_si($sensor['sensor_limit']) . $unit . "</td>
          <td width=50>" . format_si($sensor['sensor_limit_low']) . $unit ."</td>
        </tr>\n");
  echo("<tr  bgcolor=$row_colour><td colspan='5'>");

  $graph_array['id'] = $sensor['sensor_id'];
  $graph_array['type'] = $graph_type;

  include("includes/print-graphrow.inc.php");

  echo("</td></tr>");

  $row++;
}

echo("</table>");

?>
