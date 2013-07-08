<?php

if (!$os)
{
  if (preg_match("/^Cisco\ IronPort.*\ AsyncOS/", $sysDescr)) { $os = "asyncos"; }
}

//EOF
