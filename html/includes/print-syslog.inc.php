<?php

if (device_permitted($entry['device_id']))
{
  echo("<tr>");

  $entry['hostname'] = shorthost($entry['hostname'], 20);

  if ($vars['page'] != "device")
  {
    echo("<td width=140>" . $entry['date'] . "</td>");
    echo("<td width=160><strong>".generate_device_link($entry)."</strong></td>");
    echo("<td><strong>" . $entry['program'] . " : </strong> " . htmlspecialchars($entry['msg']) . "</td>");
  } else {
    echo("<td colspan=\"3\"><i>" . $entry['date'] . "</i>&nbsp;&nbsp;&nbsp;<strong>" . $entry['program'] . "</strong>&nbsp;&nbsp;&nbsp;" . htmlspecialchars($entry['msg']) . "</td>");
  }

  echo("</tr>");

}

?>
