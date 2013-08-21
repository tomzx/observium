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
  if (preg_match("/^CN/", $sysDescr)) { $os = "ciena"; }
}

// EOF
