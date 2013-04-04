<?php

// Draw generic bits graph
// args: ds_in, ds_out, rrd_filename, bg, legend, from, to, width, height, inverse, previous

include("includes/graphs/common.inc.php");

if($format == "octets" || $format == "bytes")
{
  $units = "Bps";
  $format = "bytes";
  $units_descr = "Bytes/s";
} else {
  $units = "bps";
  $format = "bits";
  $units_descr = "Bits/s";
}

$i = 0;
$units_descr = rrdtool_escape($units_descr, 9);
$rrd_options .= " COMMENT:'$units_descr  Now       Ave      Max      95th %\\n'";



if ($rrd_filename) { $rrd_filename_out = $rrd_filename; $rrd_filename_in = $rrd_filename; }
if ($inverse) { $in = 'out'; $out = 'in'; } else { $in = 'in'; $out = 'out'; }

if ($multiplier)
{
  $rrd_options .= " DEF:p".$out."octets=".$rrd_filename_out.":".$ds_out.":AVERAGE";
  $rrd_options .= " DEF:p".$in."octets=".$rrd_filename_in.":".$ds_in.":AVERAGE";
  $rrd_options .= " DEF:p".$out."octets_max=".$rrd_filename_out.":".$ds_out.":MAX";
  $rrd_options .= " DEF:p".$in."octets_max=".$rrd_filename_in.":".$ds_in.":MAX";
  $rrd_options .= " CDEF:inoctets=pinoctets,$multiplier,*";
  $rrd_options .= " CDEF:outoctets=poutoctets,$multiplier,*";
  $rrd_options .= " CDEF:inoctets_max=pinoctets_max,$multiplier,*";
  $rrd_options .= " CDEF:outoctets_max=poutoctets_max,$multiplier,*";
} else {
  $rrd_options .= " DEF:".$out."octets=".$rrd_filename_out.":".$ds_out.":AVERAGE";
  $rrd_options .= " DEF:".$in."octets=".$rrd_filename_in.":".$ds_in.":AVERAGE";
  $rrd_options .= " DEF:".$out."octets_max=".$rrd_filename_out.":".$ds_out.":MAX";
  $rrd_options .= " DEF:".$in."octets_max=".$rrd_filename_in.":".$ds_in.":MAX";
}

if($_GET['previous'] == "yes")
{
  if ($multiplier)
  {
    $rrd_options .= " DEF:p".$out."octetsX=".$rrd_filename_out.":".$ds_out.":AVERAGE:start=".$prev_from.":end=".$from;
    $rrd_options .= " DEF:p".$in."octetsX=".$rrd_filename_in.":".$ds_in.":AVERAGE:start=".$prev_from.":end=".$from;
    $rrd_options .= " SHIFT:p".$out."octetsX:$period";
    $rrd_options .= " SHIFT:p".$in."octetsX:$period";
    $rrd_options .= " CDEF:inoctetsX=pinoctetsX,$multiplier,*";
    $rrd_options .= " CDEF:outoctetsX=poutoctetsX,$multiplier,*";
  } else {
    $rrd_options .= " DEF:".$out."octetsX=".$rrd_filename_out.":".$ds_out.":AVERAGE:start=".$prev_from.":end=".$from;
    $rrd_options .= " DEF:".$in."octetsX=".$rrd_filename_in.":".$ds_in.":AVERAGE:start=".$prev_from.":end=".$from;
    $rrd_options .= " SHIFT:".$out."octetsX:$period";
    $rrd_options .= " SHIFT:".$in."octetsX:$period";
  }

  $rrd_options .= " CDEF:octetsX=inoctetsX,outoctetsX,+";
  $rrd_options .= " CDEF:doutoctetsX=outoctetsX,-1,*";
  $rrd_options .= " CDEF:outbitsX=outoctetsX,8,*";
  #$rrd_options .= " CDEF:outbits_maxX=outoctets_maxX,8,*";
  #$rrd_options .= " CDEF:doutoctets_maxX=outoctets_maxX,-1,*";
  $rrd_options .= " CDEF:doutbitsX=doutoctetsX,8,*";
  #$rrd_options .= " CDEF:doutbits_maxX=doutoctets_maxX,8,*";

  $rrd_options .= " CDEF:inbitsX=inoctetsX,8,*";
  #$rrd_options .= " CDEF:inbits_maxX=inoctets_maxX,8,*";
  $rrd_options .= " VDEF:totinX=inoctetsX,TOTAL";
  $rrd_options .= " VDEF:totoutX=outoctetsX,TOTAL";
  $rrd_options .= " VDEF:totX=octetsX,TOTAL";

}

$rrd_options .= " CDEF:octets=inoctets,outoctets,+";
$rrd_options .= " CDEF:doutoctets=outoctets,-1,*";
$rrd_options .= " CDEF:outbits=outoctets,8,*";
$rrd_options .= " CDEF:outbits_max=outoctets_max,8,*";
$rrd_options .= " CDEF:doutoctets_max=outoctets_max,-1,*";
$rrd_options .= " CDEF:doutbits=doutoctets,8,*";
$rrd_options .= " CDEF:doutbits_max=doutoctets_max,8,*";

$rrd_options .= " CDEF:inbits=inoctets,8,*";
$rrd_options .= " CDEF:inbits_max=inoctets_max,8,*";

if ($config['rrdgraph_real_95th']) {
        $rrd_options .= " CDEF:highbits=inoctets,outoctets,MAX,8,*";
        $rrd_options .= " VDEF:95thhigh=highbits,95,PERCENT";
}

$rrd_options .= " VDEF:totin=inoctets,TOTAL";
$rrd_options .= " VDEF:totout=outoctets,TOTAL";
$rrd_options .= " VDEF:tot=octets,TOTAL";

$rrd_options .= " VDEF:95thin=inbits,95,PERCENT";
$rrd_options .= " VDEF:95thout=outbits,95,PERCENT";
$rrd_options .= " VDEF:d95thout=doutbits,5,PERCENT";

if($format == "octets" || $format == "bytes")
{
  $units = "Bytes/sec";
  $format = "octets";
} else {
  $units = "bits/sec";
  $format = "bits";
}

$rrd_options .= " AREA:in".$format."_max#B6D14B:";
$rrd_options .= " AREA:in".$format."#92B73F:'In '";
$rrd_options .= " LINE1.25:in".$format."#4A8328:'In '";
$rrd_options .= " GPRINT:in".$format.":LAST:%6.2lf%s";
$rrd_options .= " GPRINT:in".$format.":AVERAGE:%6.2lf%s";
$rrd_options .= " GPRINT:in".$format."_max:MAX:%6.2lf%s";
$rrd_options .= " GPRINT:95thin:%6.2lf%s\\\\n";

$rrd_options .= " AREA:dout".$format."_max#A0A0E5:";
$rrd_options .= " AREA:dout".$format."#7075B8:'Out'";
$rrd_options .= " LINE1.25:dout".$format."#323B7C:Out";
$rrd_options .= " GPRINT:out".$format.":LAST:%6.2lf%s";
$rrd_options .= " GPRINT:out".$format.":AVERAGE:%6.2lf%s";
$rrd_options .= " GPRINT:out".$format."_max:MAX:%6.2lf%s";
$rrd_options .= " GPRINT:95thout:%6.2lf%s\\\\n";

if ($config['rrdgraph_real_95th']) {
        $rrd_options .= " HRULE:95thhigh#FF0000:\"Highest\"";
        $rrd_options .= " GPRINT:95thhigh:\"%30.2lf%s\\n\"";
}

$rrd_options .= " GPRINT:tot:'Total %6.2lf%s'";
$rrd_options .= " GPRINT:totin:'(In %6.2lf%s'";
$rrd_options .= " GPRINT:totout:'Out %6.2lf%s)\\\\l'";
$rrd_options .= " LINE1:95thin#aa0000";
$rrd_options .= " LINE1:d95thout#aa0000";

if($_GET['previous'] == "yes")
{
  $rrd_options .= " LINE1.25:in".$format."X#009900:'Prev In \\\\n'";
  $rrd_options .= " LINE1.25:dout".$format."X#000099:'Prev Out'";
}

?>
