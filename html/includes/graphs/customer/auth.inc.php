<?php

// FIXME - wtfbbq

if ($_SESSION['userlevel'] >= "5" || $auth)
{
  $id = mres($vars['id']);
  $title = "Customer :: ".mres($vars['id']);
  $auth = TRUE;
}

?>
