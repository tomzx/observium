<?php

echo('<table class="table table-striped table-condensed">');
echo('<thead><tr>
        <th>Server Name</th>
        <th>Port Status</th>
        <th>Operating System</th>
        <th>Memory</th>
        <th>CPU</th>
      </tr></thead>');


foreach (dbFetchRows("SELECT * FROM vminfo WHERE device_id = ? ORDER BY vmwVmDisplayName", array($device['device_id'])) as $vm)
{
  print_vm_row($vm, $device);
}

echo("</table>");

$pagetitle[] = "Virtual Machines";

?>
