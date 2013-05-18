<div class="well info_box">
    <div id="title"><i class="oicon-server"></i> Device Information</div>
    <div id="content">

<?php

if ($config['overview_show_sysDescr'])
{
  echo('<div style="font-family: courier, serif; margin: 3px"><strong>' . $device['sysDescr'] . "</strong></div>");
}

$uptime = $device['uptime'];

if ($device['os'] == "ios") { formatCiscoHardware($device); }
if ($device['features']) { $device['features'] = "(".$device['features'].")"; }
$device['os_text'] = $config['os'][$device['os']]['text'];

echo('<table class="table table-condensed-more table-striped">');

if ($device['hardware'])
{
  echo('<tr>
        <td class="entity">Hardware</td>
        <td>' . $device['hardware']. '</td>
      </tr>');
}

echo('<tr>
        <td class="entity">Operating System</td>
        <td>' . $device['os_text'] . ' ' . $device['version'] . ' ' . $device['features'] . ' </td>
      </tr>');

if ($device['serial'])
{
  echo('<tr>
        <td class="entity">Serial</td>
        <td>' . $device['serial']. '</td>
      </tr>');
}

if ($device['sysContact'])
{
  echo('<tr>
        <td class="entity">Contact</td>');
  if (get_dev_attrib($device,'override_sysContact_bool'))
  {
    echo('
        <td>' . htmlspecialchars(get_dev_attrib($device,'override_sysContact_string')) . '</td>
      </tr>
      <tr>
        <td class="entity">SNMP Contact</td>');
  }
  echo('
        <td>' . htmlspecialchars($device['sysContact']). '</td>
      </tr>');
}

if ($device['location'])
{
  echo('<tr>
        <td class="entity">Location</td>
        <td>' . $device['location']. '</td>
      </tr>');
  if (get_dev_attrib($device,'override_sysLocation_bool') && !empty($device['real_location']))
  {
    echo('<tr>
        <td class="entity">SNMP Location</td>
        <td>' . $device['real_location']. '</td>
      </tr>');
  }
}

if ($uptime)
{
  echo('<tr>
        <td class="entity">Uptime</td>
        <td>' . deviceUptime($device) . '</td>
      </tr>');
}

  echo("</table>");

  echo("</div></div>");

?>
