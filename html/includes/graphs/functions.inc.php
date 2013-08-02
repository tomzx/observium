<?php

function graph_error($string)
{
  global $vars, $config, $debug, $graphfile;

  $vars['bg'] = "FFBBBB";

  include("includes/graphs/common.inc.php");

  $rrd_options .= " HRULE:0#555555";
  $rrd_options .= " --title='".$string."'";

  rrdtool_graph($graphfile, $rrd_options);

  if ($height > "99")  {
    $woo = shell_exec($rrd_cmd);
    if ($debug) { echo("<pre>".$rrd_cmd."</pre>"); }
    if (is_file($graphfile) && !$debug)
    {
      header('Content-type: image/png');
      $fd = fopen($graphfile,'r'); fpassthru($fd); fclose($fd);
      unlink($graphfile);
#      exit();
    }
  } else {
    if (!$debug) { header('Content-type: image/png'); }
    $im     = imagecreate($width, $height);
    $orange = imagecolorallocate($im, 255, 225, 225);
    $px     = (imagesx($im) - 7.5 * strlen($string)) / 2;
    imagestring($im, 3, $px, $height / 2 - 8, $string, imagecolorallocate($im, 128, 0, 0));
    imagepng($im);
    imagedestroy($im);
#    exit();
  }
}

