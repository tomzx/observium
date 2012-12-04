<?php

include("includes/graphs/common.inc.php");

$rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/arista-netstats-sw-ip.rrd";

$rrd_options .= " DEF:ipReasmReqds=$rrd_filename:ReasmReqds:AVERAGE";
$rrd_options .= " DEF:ipReasmOKs=$rrd_filename:ReasmOKs:AVERAGE";
$rrd_options .= " DEF:ipReasmFails=$rrd_filename:ReasmFails:AVERAGE";
$rrd_options .= " DEF:ipFragFails=$rrd_filename:OutFragFails:AVERAGE";
$rrd_options .= " DEF:ipFragCreates=$rrd_filename:OutFragCreates:AVERAGE";

$rrd_options .= " DEF:MipReasmOKs=$rrd_filename:ReasmOKs:MAX";
$rrd_options .= " DEF:MipReasmReqds=$rrd_filename:ReasmReqds:MAX";
$rrd_options .= " DEF:MipReasmFails=$rrd_filename:ReasmFails:MAX";
$rrd_options .= " DEF:MipFragFails=$rrd_filename:OutFragFails:MAX";
$rrd_options .= " DEF:MipFragCreates=$rrd_filename:OutFragCreates:MAX";

$rrd_options .= " CDEF:FragFails_n=ipFragFails,-1,*";
$rrd_options .= " CDEF:FragCreates_n=ipFragCreates,-1,*";

$rrd_options .= " COMMENT:'                 Current  Average  Maximum\\n'";

$rrd_options .= " LINE1.25:FragFails_n#cc0000:'Frag Fail    '";
$rrd_options .= " GPRINT:ipFragFails:LAST:%6.2lf%s";
$rrd_options .= " GPRINT:ipFragFails:AVERAGE:%6.2lf%s";
$rrd_options .= " GPRINT:MipFragFails:MAX:%6.2lf%s\\\\n";

$rrd_options .= " LINE1.25:FragCreates_n#00cc:'Frag Create  '";
$rrd_options .= " GPRINT:ipFragCreates:LAST:%6.2lf%s";
$rrd_options .= " GPRINT:ipFragCreates:AVERAGE:%6.2lf%s";
$rrd_options .= " GPRINT:MipFragCreates:MAX:%6.2lf%s\\\\n";

$rrd_options .= " LINE1.25:ipReasmOKs#006600:'Reasm OK     '";
$rrd_options .= " GPRINT:ipReasmOKs:LAST:%6.2lf%s";
$rrd_options .= " GPRINT:ipReasmOKs:AVERAGE:%6.2lf%s";
$rrd_options .= " GPRINT:MipReasmOKs:MAX:%6.2lf%s\\\\n";

$rrd_options .= " LINE1.25:ipReasmFails#660000:'Reasm Fail   '";
$rrd_options .= " GPRINT:ipReasmFails:LAST:%6.2lf%s";
$rrd_options .= " GPRINT:ipReasmFails:AVERAGE:%6.2lf%s";
$rrd_options .= " GPRINT:MipReasmFails:MAX:%6.2lf%s\\\\n";

$rrd_options .= " LINE1.25:ipReasmReqds#000066:'Reasm Reqd   '";
$rrd_options .= " GPRINT:ipReasmReqds:LAST:%6.2lf%s";
$rrd_options .= " GPRINT:ipReasmReqds:AVERAGE:%6.2lf%s";
$rrd_options .= " GPRINT:MipReasmReqds:MAX:%6.2lf%s\\\\n";

?>
