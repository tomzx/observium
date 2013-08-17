<?php

$success = 0;
$rad = radius_auth_open();

foreach ($config['auth_radius_server'] as $server)
{
  if (radius_add_server($rad, $server, $config['auth_radius_port'], $config['auth_radius_secret'], $config['auth_radius_timeout'], $config['auth_radius_retries']))
  {
    $success = 1;
  }
}

if (!$success)
{
  print_error("Fatal error: Could not connect to configured RADIUS server(s).");
  exit;
}

function authenticate($username,$password)
{
  global $config, $rad;

  if ($username && $rad)
  {
    radius_create_request($rad, RADIUS_ACCESS_REQUEST);
    radius_put_string($rad, 1, $username);
    radius_put_string($rad, 2, $password);
    radius_put_string($rad, 4, $_SERVER['SERVER_ADDR']);
    
    $response = radius_send_request($rad);
    if ($response == RADIUS_ACCESS_ACCEPT)
    {
      return 1;
    }
  }

  return 0;
}

function auth_can_change_password($username = "")
{
  return 0;
}

function auth_change_password($username,$newpassword)
{
  # Not supported
}

function auth_can_logout()
{
  return TRUE;
}

function auth_usermanagement()
{
  return 0;
}

function adduser($username, $password, $level, $email = "", $realname = "", $can_modify_passwd = '1')
{
  # Not supported
  return 0;
}

function auth_user_exists($username)
{
  return 0;
}

function auth_user_level($username)
{
  return 10;
}

function auth_user_id($username)
{
  return -1;
}

function deluser($username)
{
  # Not supported
  return 0;
}

function auth_user_list()
{
  $userlist = array();
  return $userlist;
}

?>
