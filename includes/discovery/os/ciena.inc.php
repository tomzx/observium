<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    obserium
 * @subpackage discovery
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */


if (!$os)
{
  if (strstr($sysObjectID, ".1.3.6.1.4.1.6141.1")) { $os = "ciena"; }
  if (preg_match("/^CN/", $sysDescr)) { $os = "ciena"; }
}

// EOF
