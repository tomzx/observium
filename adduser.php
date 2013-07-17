#!/usr/bin/env php
<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    observium
 * @subpackage cli
 * @author     Adam Armstrong <adama@memetic.org>
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */

chdir(dirname($argv[0]));

include("includes/defaults.inc.php");
include("config.php");
include("includes/definitions.inc.php");
include("includes/functions.php");

if (file_exists('html/includes/authentication/' . $config['auth_mechanism'] . '.inc.php'))
{
  include('html/includes/authentication/' . $config['auth_mechanism'] . '.inc.php');
}
else
{
  print_error("ERROR: no valid auth_mechanism defined.");
  exit();
}

if (auth_usermanagement())
{
  if (isset($argv[1]) && isset($argv[2]) && isset($argv[3]))
  {
    if (!auth_user_exists($argv[1]))
    {
      if (adduser($argv[1],$argv[2],$argv[3],@$argv[4]))
      {
        print_success("User ".$argv[1]." added successfully.");
      }
      else
      {
        print_error("User ".$argv[1]." creation failed!");
      }
    }
    else
    {
      print_warning("User ".$argv[1]." already exists!");
    }
  }
  else
  {
    print_message("%gObservium v".$config['version']."
%WAdd User%n

USAGE:
adduser.php <username> <password> <level 1-10> [email]

EXAMPLE:
%WADMIN%n:   adduser.php <username> <password> 10 [email]
%WRW user%n: adduser.php <username> <password> 7  [email]
%WRO user%n: adduser.php <username> <password> 1  [email]

%rInvalid arguments!%n", 'color');
  }
}
else
{
  print_error("Auth module does not allow adding users!");
}

?>
