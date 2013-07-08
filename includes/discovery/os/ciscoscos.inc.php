<?php

if (!$os)
{
  if (preg_match("/Cisco Service Control/", $sysDescr)) { $os = "ciscoscos"; }
}

//EOF
