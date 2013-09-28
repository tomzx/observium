<?php

/* Detection for JDSU OEM Erbium Dotted Fibre Aplifiers */

if (!$os)
{
  if (strstr(snmp_get($device, 'NSCRTV-ROOT::commonDeviceVendorInfo.1', '-OQv'), 'JDSU') && 
      strstr(snmp_get($device, 'NSCRTV-ROOT::commonDeviceName.1', '-OQv'), 'EDFA')) { $os = 'jdsu_edfa'; }
}

?>
