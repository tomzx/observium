<?php

humanize_device($device);

/// These should be summed at poller time
$port_count   = dbFetchCell("SELECT COUNT(*) FROM `ports` WHERE `device_id` = ?", array($device['device_id']));
$sensor_count = dbFetchCell("SELECT COUNT(*) FROM `sensors` WHERE `device_id` = ?", array($device['device_id']));

echo('  <tr class="'.$device['html_row_class'].'" onclick="location.href=\'device/device='.$device['device_id'].'/\'" style="cursor: pointer;">
          <td style="width: 1px; background-color: '.$device['html_tab_colour'].'; margin: 0px; padding: 0px"></td>
          <td width="64"  style="text-align: center; vertical-align: middle;">' . $device['icon'] . '</td>
          <td width="300" ><span class="entity-title">' . generate_device_link($device) . '</span>
          <br />' . truncate($device['location'],32, '') . '</td>'
        );

echo('<td width="55">');
if ($port_count) { echo(' <i class="oicon-network-ethernet"></i> '.$port_count); }
echo('<br />');
if ($sensor_count) { echo(' <i class="oicon-dashboard"></i> '.$sensor_count); }
echo('</td>');
echo('    <td >' . $device['hardware'] . '<br />' . $device['features'] . '</td>');
echo('    <td >' . $device['os_text'] . '<br />' . $device['version'] . '</td>');
echo('    <td >' . deviceUptime($device, 'short') . ' <br />');

echo('    ' . $device['sysName'] . '</td>');

echo(' </tr>');

?>
