<?php

if (!$os)
{
  if (preg_match("/IronPort.*\ AsyncOS/", $sysDescr)) { $os = "asyncos"; }
}

//EOF
