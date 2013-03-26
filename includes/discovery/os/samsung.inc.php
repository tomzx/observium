<?php

if (!$os)
{
  if (strstr($sysDescr, 'Samsung ML') || strstr($sysDescr, 'Samsung SC')) { $os = 'samsung'; }
  elseif (strstr(snmp_get($device, 'Printer-MIB::prtGeneralServicePerson.1', '-OQv'), 'Samsung')) { $os = 'samsung'; }
}

?>
