<?php

// Hardcoded for VDX switches that has 2GB of RAM includes all the current models.
// You get percentage from snmp_get on NOS devices, then we devide that by 100 to get 0.xx form on the percentage and multiply that with the total amout of memory.

$mempool['total'] = "2147483648";
$mempool['used'] = (snmp_get($device, "1.3.6.1.4.1.1588.2.1.1.1.26.6.0", "-Ovq")/100)*$mempool['total'];

?>
