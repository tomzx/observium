<?php

if (!$os)
{
  if ($sysDescr == "SNMP TME") { $os = "papouch"; }
  else if ($sysDescr == "TME") { $os = "papouch"; }
  else if ($sysDescr == "TH2E") { $os = "papouch"; }
}

?>