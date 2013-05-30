<?php

if ($poll_device['sysDescr'] == "SNMP TME") { $hardware = "TME"; }
else if ($poll_device['sysDescr'] == "TME") { $hardware = "TME"; }
else if ($poll_device['sysDescr'] == "TH2E") { $hardware = "TH2E"; }

?>