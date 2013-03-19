<?php

if (!$os)
{
  if (stristr($sysDescr, "ProCurve")) { $os = "procurve"; }

  # SNMPv2-MIB::sysDescr.0 = STRING: HP 1810-8G, PL.1.2, eCos-3.0, 1_12_8-customized-h
  if (preg_match("/^HP .* eCos/", $sysDescr)) { $os = "procurve"; }
}

?>