<?php

if (!isset($_SESSION['username']))
{
  $_SESSION['username'] = '';
}

function authenticate($username,$password)
{
  global $config;

  if (isset($_SERVER['REMOTE_USER']))
  {
    $_SESSION['username'] = mres($_SERVER['REMOTE_USER']);

    $row = @dbFetchRow("SELECT username FROM `users` WHERE `username`=?", array($_SESSION['username']));
    if (isset($row['username']) && $row['username'] == $_SESSION['username'])
    {
      return 1;
    }
    else
    {
      $_SESSION['username'] = $config['http_auth_guest'];
      return 1;
    }
  }
  return 0;
}

function auth_can_logout()
{
  return FALSE;
}

function auth_can_change_password($username = "")
{
  return 0;
}

function auth_change_password($username,$newpassword)
{
  # Not supported
}

function auth_usermanagement()
{
  return 1;
}

function adduser($username, $password, $level, $email = "", $realname = "", $can_modify_passwd = '1')
{
  return dbInsert(array('username' => $username, 'password' => $password, 'level' => $level, 'email' => $email, 'realname' => $realname), 'users');
}

function auth_user_exists($username)
{
  // FIXME this doesn't seem right? (adama)
  return dbFetchCell("SELECT * FROM `users` WHERE `username` = ?", array($username));
}

function auth_user_level($username)
{
  return dbFetchCell("SELECT `level` FROM `users` WHERE `username`= ?", array($username));
}

function auth_user_id($username)
{
  return dbFetchCell("SELECT `user_id` FROM `users` WHERE `username`= ?", array($username));
}

function deluser($username)
{
  # Not supported
  return 0;
}

function auth_user_list()
{
  return dbFetchRows("SELECT * FROM `users`");
}

?>
