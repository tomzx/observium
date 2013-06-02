<?php

$pagetitle[] = 'Deleted ports';

if ($vars['purge'] == 'all')
{
  foreach (dbFetchRows('SELECT * FROM `ports` AS P, `devices` as D WHERE P.`deleted` = "1" AND D.device_id = P.device_id') as $port)
  {
    if (port_permitted($port['port_id'], $port['device_id']))
    {
      delete_port($port['port_id']);
      echo('<div class=infobox>Deleted '.generate_device_link($port).' - '.generate_port_link($port).'</div>');
    }
  }
} elseif ($vars['purge']) {
  $port = dbFetchRow('SELECT * from `ports` AS P, `devices` AS D WHERE `port_id` = ? AND D.device_id = P.device_id', array($vars['purge']));
  if (port_permitted($port['port_id'], $port['device_id']))
  delete_port($port['port_id']);
  echo('<div class="infobox">Deleted '.generate_device_link($port).' - '.generate_port_link($port).'</div>');
}

echo('<table class="table table-striped table-bordered table-condensed">');
echo('<thead><tr><th>Device</th><th>Port</th><th>Description</th><th style="text-align: right;"><a href="deleted-ports/purge=all/"><button class="btn btn-danger btn-small"><i class="icon-remove icon-white"></i> Purge All</button></a></th></tr></thead>');

foreach (dbFetchRows('SELECT * FROM `ports` AS P, `devices` as D WHERE P.`deleted` = "1" AND D.device_id = P.device_id') as $port)
{
  humanize_port($port);
  if (port_permitted($port['port_id'], $port['device_id']))
  {
    echo('<tr class="list">');
    echo('<td width="200" class="strong">'.generate_device_link($port).'</td>');
    echo('<td width="350" class="strong">'.generate_port_link($port).'</td>');
    echo('<td>'.$port['ifAlias'].'</td>');
    echo('<td width="100"><a href="deleted-ports/purge='.$port['port_id'].'/"><button class="btn btn-danger btn-small"><i class="icon-remove icon-white"></i> Purge</button></a></td>');
  }
}

echo('</table>');

?>
