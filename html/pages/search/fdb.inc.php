<div class="row">
<div class="span12">

<?php
unset($search, $devices);

$devices[''] = 'All Devices';

foreach (dbFetchRows("SELECT D.device_id AS device_id, `hostname` FROM `vlans_fdb` AS V, `devices` AS D WHERE V.device_id = D.device_id GROUP BY `device_id` ORDER BY `hostname`;") as $data)
{
  if ($data['disabled'] && !$config['web_show_disabled']) { continue; }
  $devices[$data['device_id']] = $data['hostname'];
}

//Device field
$search[] = array('type'    => 'select',
                  'name'    => 'Device',
                  'id'      => 'device_id',
                  'value'   => $vars['device_id'],
                  'values'  => $devices);

$search[] = array('type'    => 'text',
                  'name'    => 'IP Address',
                  'id'      => 'string',
                  'value'   => $vars['address']);

print_search_simple($search, 'FDB Table');

$pagetitle[] = "FDB Search";

echo('<table class="table table-striped table-condensed table-rounded table-bordered">');

$query = "SELECT * FROM `vlans_fdb` AS F, `vlans` as V, `ports` AS P, `devices` AS D WHERE V.vlan_vlan = F.vlan_id AND V.device_id = F.device_id AND P.port_id = F.port_id and D.device_id = F.device_id ";
$query .= " AND F.`mac_address` LIKE ?";
$param = array("%".str_replace(array(':', ' ', '-', '.', '0x'),'',mres($_POST['string']))."%");

if (is_numeric($_POST['device_id']))
{
  $query  .= " AND P.device_id = ?";
  $param[] = $_POST['device_id'];
}
$query .= " ORDER BY F.mac_address";

echo('<thead><tr>
        <th>MAC Address</th>
        <th>Device</th>
        <th>Interface</th>
        <th>VLAN ID</th>
        <th>VLAN Name</th>
      </tr></thead>');

foreach (dbFetchRows($query, $param) as $entry)
{
  if (port_permitted($entry['port_id']))
  {
    //why are they here for?
    //$speed = humanspeed($entry['ifSpeed']);
    //$type = humanmedia($entry['ifType']);

    if ($entry['ifInErrors'] > 0 || $entry['ifOutErrors'] > 0)
    {
      $error_img = generate_port_link($entry,"<img src='images/16/chart_curve_error.png' alt='Interface Errors' border=0>",errors);
    } else { $error_img = ""; }

    $arp_host = dbFetchRow("SELECT * FROM ipv4_addresses AS A, ports AS I, devices AS D WHERE A.ipv4_address = ? AND I.port_id = A.port_id AND D.device_id = I.device_id", array($entry['ipv4_address']));
    if ($arp_host) { $arp_name = generate_device_link($arp_host); } else { unset($arp_name); }
    if ($arp_host) { $arp_if = generate_port_link($arp_host); } else { unset($arp_if); }
    if ($arp_host['device_id'] == $entry['device_id']) { $arp_name = "Localhost"; }
    if ($arp_host['port_id'] == $entry['port_id']) { $arp_if = "Local port"; }

    echo('<tr>
        <td width="160">' . formatMac($entry['mac_address']) . '</td>
        <td width="200" class="entity">' . generate_device_link($entry) . '</td>
        <td class="entity">' . generate_port_link($entry, makeshortif(fixifname($entry['ifDescr']))) . ' ' . $error_img . '</td>
        <td class="entity">VLAN'.$entry['vlan_vlan'].'</td>
        <td class="entity">'.$entry['vlan_name'].'</td>
            </tr>');
  }
}

echo("</table>");

?>
