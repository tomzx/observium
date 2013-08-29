<?php

if ($_SESSION['userlevel'] == '10')
{
  print_warning("This is a dump of your Observium configuration. To adjust it, please modify your <strong>config.php</strong> file.");
  echo("<pre>");
  print_vars($config);
  echo("</pre>");
} else {
  include("includes/error-no-perm.inc.php");
}

?>
