<?php

if (preg_match('/^RoomAlert(\d+\w)/', $poll_device['sysDescr'], $regexp_result))
{
  $hardware = "RoomAlert " . $regexp_result[1];
}

// EOF
