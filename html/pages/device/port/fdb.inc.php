<?php

echo("<table class=\"table table-striped table-condensed\" style=\"margin-top: 10px;\">\n");
echo("  <thead>\n");
echo("    <tr>\n");
echo("      <th>VLAN</th>\n");
echo("      <th>MAC Address</th>\n");
echo("      <th>Remote Host</th>\n");
echo("      <th>Remote Port</th>\n");
echo("      <th>IP Addresses</th>\n");
echo("    </tr>\n");
echo("  </thead>\n");
echo("  <tbody>\n");

$i = "1";

foreach (dbFetchRows("SELECT * FROM ports AS P, devices AS D WHERE P.device_id = D.device_id") as $portp)
{
#  $macs[$port[]]
}

foreach (dbFetchRows("SELECT * FROM `vlans_fdb` WHERE port_id = ?", array($port['port_id'])) as $fdb)
{
  if (!is_integer($i/2)) { $bg_colour = $list_colour_a; } else { $bg_colour = $list_colour_b; }

  $fdb_host = dbFetchRow("SELECT * FROM `ports` AS P, devices AS D WHERE P.ifPhysAddress = ? AND D.device_id = P.device_id", array($fdb['mac_address']));

  if ($fdb_host) { $fdb_name = generate_device_link($fdb_host); } else { unset($fdb_name); }
  if ($fdb_host) { $fdb_if = generate_port_link($fdb_host); } else { unset($fdb_if); }
  if ($fdb_host['device_id'] == $device['device_id']) { $fdb_name = "Localhost"; }
  if ($fdb_host['port_id'] == $fdb['port_id']) { $fdb_if = "Local Port"; }

  echo("
  <tr bgcolor=$bg_colour>
    <td width=160>VLAN".$fdb['vlan_id']."</td>
    <td width=160>".formatmac($fdb['mac_address'])."</td>
    <td width=280>$fdb_name</td>
    <td><strong>$fdb_if</strong></td>
    <td width=160>");
  foreach (dbFetchRows("SELECT ip_address FROM ip_mac WHERE mac_address = ? GROUP BY ip_address", array($fdb['mac_address'])) as $ip)
  {
    echo($ip['ip_address']."<br />");
  }
    echo("</td>

  </tr>");
  $i++;
}

echo("  </tbody>\n");
echo("</table>\n");

?>
