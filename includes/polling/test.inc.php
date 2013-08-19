<?php

print_vars(snmpwalk_cache_oid ($device, "system", array()));

print_vars(snmp_cache_oid ("system", $device, array()));

?>
