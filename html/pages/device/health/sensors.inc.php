<?php

echo('<table class="table table-striped-two table-condensed">');

$row = 1;

$sql  = "SELECT *, `sensors`.`sensor_id` AS `sensor_id`";
$sql .= " FROM  `sensors`";
$sql .= " LEFT JOIN  `sensors-state` ON  `sensors`.sensor_id =  `sensors-state`.sensor_id";
$sql .= " WHERE `sensor_class` = ? AND `device_id` = ?";

foreach (dbFetchRows($sql, array($class, $device['device_id'])) as $sensor)
{

  echo("<tr>
          <th width=500>" . $sensor['sensor_descr'] . "</td>
          <th>" . $sensor['sensor_type'] . "</td>
          <td width=50>" . format_si($sensor['sensor_value']) .$unit. "</td>
          <td width=50>" . format_si($sensor['sensor_limit']) . $unit . "</td>
          <td width=50>" . format_si($sensor['sensor_limit_low']) . $unit ."</td>
        </tr>\n");
  echo("<tr><td colspan='5'>");

  $graph_array['id'] = $sensor['sensor_id'];
  $graph_array['type'] = $graph_type;

  include("includes/print-graphrow.inc.php");

  echo("</td></tr>");

  $row++;
}

echo("</table>");

?>
