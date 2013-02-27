<?php

// FIXME svn stuff still using optc etc, won't work, needs updating!

if ($_SESSION['userlevel'] >= "7")
{

  // Already defined $config_file in device.inc.php

  echo('<div style="clear: both;">');

  print_optionbar_start('', '');

  echo("<span style='font-weight: bold;'>Config</span> &#187; ");

  if (!$vars['rev']) {
    echo('<span class="pagemenu-selected">');
    echo(generate_link('Latest',array('page'=>'device','device'=>$device['device_id'],'tab'=>'showconfig')));
    echo("</span>");
  } else {
    echo(generate_link('Latest',array('page'=>'device','device'=>$device['device_id'],'tab'=>'showconfig')));
  }

  if (function_exists('svn_log')) {

    $sep     = " | ";
    $svnlogs = svn_log($device_config_file, SVN_REVISION_HEAD, NULL, 8);
    $revlist = array();

    foreach ($svnlogs as $svnlog) {

      echo($sep);
      $revlist[] = $svnlog["rev"];

      if ($vars['rev'] == $svnlog["rev"]) { echo('<span class="pagemenu-selected">'); }
      $linktext = "r" . $svnlog["rev"] ." <small>". date("d M H:i", strtotime($svnlog["date"])) . "</small>";
      echo(generate_link($linktext,array('page'=>'device','device'=>$device['device_id'],'tab'=>'showconfig','rev'=>$svnlog["rev"])));

      if ($vars['rev'] == $svnlog["rev"]) { echo("</span>");  }

      $sep = " | ";
    }
  }

  print_optionbar_end();

  if (function_exists('svn_log') && in_array($vars['rev'], $revlist)) {
    list($diff, $errors) = svn_diff($device_config_file, $vars['rev']-1, $device_config_file, $vars['rev']);
    if (!$diff) {
      $text = "No Difference";
    } else {
      $text = "";
      while (!feof($diff)) { $text .= fread($diff, 8192); }
      fclose($diff);
      fclose($errors);
    }
  } else {
    $fh = fopen($device_config_file, 'r') or die("Can't open file");
    $text = fread($fh, filesize($device_config_file));
    fclose($fh);
  }

  if ($config['rancid_ignorecomments'])
  {
    $lines = explode("\n",$text);
    for ($i = 0;$i < count($lines);$i++)
    {
      if ($lines[$i][0] == "#") { unset($lines[$i]); }
    }
    $text = join("\n",$lines);
  }

  echo("<pre class=\"prettyprint linenums\">");
  echo($text);
  echo("</pre>");
  echo("<script type=\"text/javascript\">window.prettyPrint && prettyPrint();</script>");
}

$pagetitle[] = "Config";

?>
