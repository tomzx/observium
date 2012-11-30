<?php

$entries = dbFetchRows("SELECT *,DATE_FORMAT(datetime, '%D %b %Y %T') as humandate  FROM `eventlog` WHERE `host` = ? AND `type` = 'interface' AND `reference` = '".$port['port_id']."' ORDER BY `datetime` DESC LIMIT 0,250", array($device['device_id']));
echo("<table class=\"table table-striped table-condensed\" style=\"margin-top: 10px;\">\n");
echo("  <thead>\n");
echo("    <tr>\n");
echo("      <th>Date</th>\n");
echo("      <th>Message</th>\n");
echo("    </tr>\n");
echo("  </thead>\n");
echo("  <tbody>\n");
foreach ($entries as $entry) { include("includes/print-event.inc.php"); }
echo("  </tbody>\n");
echo("</table>\n");

$pagetitle[] = "Events";

?>
