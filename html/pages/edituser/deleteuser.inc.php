<?php

echo('<div style="margin: 10px;">');

if ($_SESSION['userlevel'] < '10') { include("includes/error-no-perm.inc.php"); } else
{
  if (auth_usermanagement())
  {
    if ($vars['action'] == "deleteuser")
    {
      $delete_username = dbFetchCell("SELECT username FROM users WHERE user_id = ?", array($vars['user_id']));

      if ($vars['confirm'] == "yes")
      {
        if (deluser($delete_username))
        {
          echo('<div class="infobox">User "' . $delete_username . '" deleted!</div>');
        }
        else
        {
          echo('<div class="errorbox">Error deleting user "' . $delete_username . '"!</div>');
        }
      }
      else
      {
        echo('<div class="errorbox">You have requested deletion of the user "' . $delete_username . '". This action can not be reversed.<br /><a href="edituser/action=deleteuser/user_id=' . $vars['user_id'] . '/confirm=yes/">Click to confirm</a></div>');
      }
    }
  }
  else
  {
    print_error("Authentication module does not allow user management!");
  }
}

echo("</div>");

?>

