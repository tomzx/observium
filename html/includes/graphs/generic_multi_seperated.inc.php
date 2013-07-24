<?php

include("includes/graphs/common.inc.php");

$graph_return['valid_options'][] = "previous";
$graph_return['valid_options'][] = "total";
$graph_return['valid_options'][] = "trend";


if ($format == "octets" || $format == "bytes")
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
$units_descr = rrdtool_escape($units_descr, 18);

if (!$noheader)
{
  $rrd_options .= " COMMENT:'$units_descr Cur      Avg     Max'";
  if (!$nototal) { $rrd_options .= " COMMENT:'Total'"; }
  $rrd_options .= " COMMENT:'\\n'";
}

foreach ($rrd_list as $rrd)
{
  if (!$config['graph_colours'][$colours_in][$iter] || !$config['graph_colours'][$colours_out][$iter]) { $iter = 0; }

  $graph_return['rrds'][] = $rrd['filename'];

  $colour_in=$config['graph_colours'][$colours_in][$iter];
  $colour_out=$config['graph_colours'][$colours_out][$iter];

  if ($rrd['colour_area_in']) { $colour_in = $rrd['colour_area_in']; }
  if ($rrd['colour_area_out']) { $colour_out = $rrd['colour_area_out']; }

  $rrd_options .= " DEF:inB".$i."=".$rrd['filename'].":".$rrd['ds_in'].":AVERAGE ";
  $rrd_options .= " DEF:outB".$i."=".$rrd['filename'].":".$rrd['ds_out'].":AVERAGE ";
  $rrd_options .= " CDEF:octets".$i."=inB".$i.",outB".$i.",+";
  $rrd_options .= " CDEF:inbits".$i."=inB".$i.",$multiplier,* ";
  $rrd_options .= " CDEF:outbits".$i."=outB".$i.",$multiplier,*";
  $rrd_options .= " CDEF:outbits".$i."_neg=outbits".$i.",-1,*";
  $rrd_options .= " CDEF:bits".$i."=inbits".$i.",outbits".$i.",+";

  if ($_GET['previous'])
  {
    $rrd_options .= " DEF:inB" . $i . "X=".$rrd['filename'].":".$ds_in.":AVERAGE:start=".$prev_from.":end=".$from;
    $rrd_options .= " DEF:outB" . $i . "X=".$rrd['filename'].":".$ds_out.":AVERAGE:start=".$prev_from.":end=".$from;
    $rrd_options .= " SHIFT:inB" . $i . "X:$period";
    $rrd_options .= " SHIFT:outB" . $i . "X:$period";
    $in_thingX .= $seperatorX . "inB" . $i . "X,UN,0," . "inB" . $i . "X,IF";
    $out_thingX .= $seperatorX . "outB" . $i . "X,UN,0," . "outB" . $i . "X,IF";
    $plusesX .= $plusX;
    $seperatorX = ",";
    $plusX = ",+";
  }

  if (!$args['nototal'])
  {
    $in_thing .= $seperator . "inB" . $i . ",UN,0," . "inB" . $i . ",IF";
    $out_thing .= $seperator . "outB" . $i . ",UN,0," . "outB" . $i . ",IF";
    $pluses .= $plus;
    $seperator = ",";
    $plus = ",+";

    $rrd_options .= " VDEF:totinB".$i."=inB".$i.",TOTAL";
    $rrd_options .= " VDEF:totoutB".$i."=outB".$i.",TOTAL";
    $rrd_options .= " VDEF:tot".$i."=octets".$i.",TOTAL";
  }

  if ($i) { $stack="STACK"; }

  $rrd_options .= " AREA:inbits".$i."#" . $colour_in . ":'" . rrdtool_escape($rrd['descr'], 10) . "In ':$stack";
  $rrd_options .= " GPRINT:inbits".$i.":LAST:%5.2lf%s";
  $rrd_options .= " GPRINT:inbits".$i.":AVERAGE:%5.2lf%s";
  $rrd_options .= " GPRINT:inbits".$i.":MAX:%5.2lf%s";

  if (!$nototal) { $rrd_options .= " GPRINT:totinB".$i.":%5.2lf%s$total_units"; }

  $rrd_options .= " COMMENT:'\\n'";
  $rrd_optionsb .= " AREA:outbits".$i."_neg#" . $colour_out . "::$stack";
  $rrd_options .= "  HRULE:999999999999999#" . $colour_out . ":'" . substr(str_pad('', 10),0,10) . "Out':";
  $rrd_options .= " GPRINT:outbits".$i.":LAST:%5.2lf%s";
  $rrd_options .= " GPRINT:outbits".$i.":AVERAGE:%5.2lf%s";
  $rrd_options .= " GPRINT:outbits".$i.":MAX:%5.2lf%s";

  if (!$nototal) { $rrd_options .= " GPRINT:totoutB".$i.":%5.2lf%s$total_units"; }

  $rrd_options .= " COMMENT:'\\n'";
  $i++; $iter++;
}

if ($_GET['previous'] == "yes")
{
  $rrd_options .= " CDEF:inBX=" . $in_thingX . $plusesX;
  $rrd_options .= " CDEF:outBX=" . $out_thingX . $plusesX;
  $rrd_options .= " CDEF:octetsX=inBX,outBX,+";
  $rrd_options .= " CDEF:doutBX=outBX,-1,*";
  $rrd_options .= " CDEF:inbitsX=inBX,8,*";
  $rrd_options .= " CDEF:outbitsX=outBX,8,*";
  $rrd_options .= " CDEF:bitsX=inbitsX,outbitsX,+";
  $rrd_options .= " CDEF:doutbitsX=doutBX,8,*";
  $rrd_options .= " VDEF:95thinX=inbitsX,95,PERCENT";
  $rrd_options .= " VDEF:95thoutX=outbitsX,95,PERCENT";
  $rrd_options .= " VDEF:d95thoutX=doutbitsX,5,PERCENT";
}

if ($_GET['previous'] == "yes")
{
  $rrd_options .= " AREA:in".$format."X#99999999:";
  $rrd_optionsb .= " AREA:dout".$format."X#99999999:";
  $rrd_options .= " LINE1.25:in".$format."X#666666:";
  $rrd_optionsb .= " LINE1.25:dout".$format."X#666666:";
}

if (!$args['nototal'])
{
  $rrd_options .= " CDEF:inB=" . $in_thing . $pluses;
  $rrd_options .= " CDEF:outB=" . $out_thing . $pluses;
  $rrd_options .= " CDEF:octets=inB,outB,+";
  $rrd_options .= " CDEF:doutB=outB,-1,*";
  $rrd_options .= " CDEF:inbits=inB,8,*";
  $rrd_options .= " CDEF:outbits=outB,8,*";
  $rrd_options .= " CDEF:bits=inbits,outbits,+";
  $rrd_options .= " CDEF:doutbits=doutB,8,*";
  $rrd_options .= " VDEF:95thin=inbits,95,PERCENT";
  $rrd_options .= " VDEF:95thout=outbits,95,PERCENT";
  $rrd_options .= " VDEF:d95thout=doutbits,5,PERCENT";
  $rrd_options .= " VDEF:totin=inB,TOTAL";
  $rrd_options .= " VDEF:avein=inbits,AVERAGE";
  $rrd_options .= " VDEF:totout=outB,TOTAL";
  $rrd_options .= " VDEF:aveout=outbits,AVERAGE";
  $rrd_options .= " VDEF:tot=octets,TOTAL";

  $rrd_options .= " COMMENT:' \\n'";

  $rrd_options .= " HRULE:999999999999999#FFFFFF:'" . substr(str_pad('Total', 10),0,10) . "In ':";
  $rrd_options .= " GPRINT:inbits:LAST:%5.2lf%s";
  $rrd_options .= " GPRINT:inbits:AVERAGE:%5.2lf%s";
  $rrd_options .= " GPRINT:inbits:MAX:%5.2lf%s";
  $rrd_options .= " GPRINT:totin:%5.2lf%s$total_units";
  $rrd_options .= " COMMENT:'\\n'";

  $rrd_options .= " HRULE:999999999999990#FFFFFF:'" . substr(str_pad('', 10),0,10) . "Out':";
  $rrd_options .= " GPRINT:outbits:LAST:%5.2lf%s";
  $rrd_options .= " GPRINT:outbits:AVERAGE:%5.2lf%s";
  $rrd_options .= " GPRINT:outbits:MAX:%5.2lf%s";
  $rrd_options .= " GPRINT:totout:%5.2lf%s$total_units";
  $rrd_options .= " COMMENT:'\\n'";

  $rrd_options .= " HRULE:999999999999990#FFFFFF:'" . substr(str_pad('', 10),0,10) . "Agg':";
  $rrd_options .= " GPRINT:bits:LAST:%5.2lf%s";
  $rrd_options .= " GPRINT:bits:AVERAGE:%5.2lf%s";
  $rrd_options .= " GPRINT:bits:MAX:%5.2lf%s";
  $rrd_options .= " GPRINT:tot:%5.2lf%s$total_units";
  $rrd_options .= " COMMENT:'\\n'";

  if ($_GET['trend'])
  {
    $rrd_options .= " CDEF:smooth_in=inbits,1800,TREND";
    $rrd_options .= " CDEF:predict_in=586400,-7,1800,inbits,PREDICT";
    $rrd_options .= " LINE2:predict_in#FF00FF::dashes=5";

    $rrd_options .= " CDEF:smooth_out=doutbits,1800,TREND";
    $rrd_options .= " CDEF:predict_out=586400,-7,1800,doutbits,PREDICT";
    $rrd_options .= " LINE2:predict_out#FF00FF::dashes=5";

  }


}

if (!$args['nototal'] && $_GET['previous'] == "yes")
{
  $rrd_options .= " VDEF:totinX=inBX,TOTAL";
  $rrd_options .= " VDEF:totoutX=outBX,TOTAL";
  $rrd_options .= " VDEF:totX=octetsX,TOTAL";
  $rrd_options .= " COMMENT:' \\n'";

  $rrd_options .= " HRULE:999999999999999#aaaaaa:'" . substr(str_pad('Total', 10),0,10) . "In ':";
  $rrd_options .= " GPRINT:inbitsX:LAST:%5.2lf%s";
  $rrd_options .= " GPRINT:inbitsX:AVERAGE:%5.2lf%s";
  $rrd_options .= " GPRINT:inbitsX:MAX:%5.2lf%s";
  $rrd_options .= " GPRINT:totinX:%5.2lf%s$total_units";
  $rrd_options .= " COMMENT:'\\n'";

  $rrd_options .= " HRULE:999999999999990#aaaaaa:'" . substr(str_pad('', 10),0,10) . "Out':";
  $rrd_options .= " GPRINT:outbitsX:LAST:%5.2lf%s";
  $rrd_options .= " GPRINT:outbitsX:AVERAGE:%5.2lf%s";
  $rrd_options .= " GPRINT:outbitsX:MAX:%5.2lf%s";
  $rrd_options .= " GPRINT:totoutX:%5.2lf%s$total_units";
  $rrd_options .= " COMMENT:'\\n'";

  $rrd_options .= " HRULE:999999999999990#aaaaaa:'" . substr(str_pad('', 10),0,10) . "Agg':";
  $rrd_options .= " GPRINT:bitsX:LAST:%5.2lf%s";
  $rrd_options .= " GPRINT:bitsX:AVERAGE:%5.2lf%s";
  $rrd_options .= " GPRINT:bitsX:MAX:%5.2lf%s";
  $rrd_options .= " GPRINT:totX:%5.2lf%s$total_units";
  $rrd_options .= " COMMENT:'\\n'";
}



$rrd_options .= $rrd_optionsb;
$rrd_options .= " HRULE:0#999999";

?>
