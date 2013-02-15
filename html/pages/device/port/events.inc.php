<?php

$entries = dbFetchRows("SELECT *,DATE_FORMAT(datetime, '%D %b %Y %T') as humandate  FROM `eventlog` WHERE `host` = ? AND `type` = 'interface' AND `reference` = '".$port['port_id']."' ORDER BY `datetime` DESC LIMIT 0,250", array($device['device_id']));

print_events($entries);

$pagetitle[] = "Events";

?>
