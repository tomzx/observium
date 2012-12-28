<?php

switch ($vars['api']) {
  case "errorcodes":
    include("pages/api/errorcodes.inc.php");
    break;
  default:
    include("pages/api/manual.inc.php");
}

?>
