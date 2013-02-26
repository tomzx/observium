<?php

$graph_type = "storage_usage";

echo('<table class="table table-striped-two table-condensed">');


echo("<thead><tr>
        <th width=250>Drive</th>
        <th width=420>Usage</th>
        <th width=50>Free</th>
        <th></th>
      </tr></thead>");

$row = 1;

$sql  = "SELECT *, `storage`.`storage_id` as `storage_id`";
$sql .= " FROM  `storage`";
$sql .= " LEFT JOIN  `storage-state` ON  `storage`.storage_id =  `storage-state`.storage_id";
$sql .= " WHERE `device_id` = ?";

foreach (dbFetchRows($sql, array($device['device_id'])) as $drive)
{

  $total = $drive['storage_size'];
  $used  = $drive['storage_used'];
  $free  = $drive['storage_free'];
  $perc  = round($drive['storage_perc'], 0);
  $used = formatStorage($used);
  $total = formatStorage($total);
  $free = formatStorage($free);

  $fs_url   = "device/device=".$device['device_id']."/tab=health/metric=storage/";

  $fs_popup  = "onmouseover=\"return overlib('<div class=list-large>".$device['hostname']." - ".$drive['storage_descr'];
  $fs_popup .= "</div><img src=\'graph.php?id=" . $drive['storage_id'] . "&amp;type=".$graph_type."&amp;from=".$config['time']['month']."&amp;to=".$config['time']['now']."&amp;width=400&amp;height=125\'>";
  $fs_popup .= "', RIGHT, FGCOLOR, '#e5e5e5');\" onmouseout=\"return nd();\"";

  $background = get_percentage_colours($percent);

  echo("<tr><th><a href='$fs_url' $fs_popup>" . $drive['storage_descr'] . "</a></td><td>
          <a href='$fs_url' $fs_popup>".print_percentage_bar (400, 20, $perc, "$used / $total", "ffffff", $background['left'], $perc . "%", "ffffff", $background['right'])."</a>
          </td><td>" . $free . "</td><td></td></tr>");

  $graph_array['id'] = $drive['storage_id'];
  $graph_array['type'] = $graph_type;

  echo("<tr><td colspan=6>");

  include("includes/print-graphrow.inc.php");

  echo("</td></tr>");

  $row++;
}

echo("</table>");

?>
