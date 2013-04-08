<h2>Observium User Managment: Authlog</h2>
<?php

include("usermenu.inc.php");

if ($_SESSION['userlevel'] == '10')
{
  echo("
<table class=\"table table-bordered table-striped table-hover table-condensed table-rounded\">
  <thead>
    <tr>
      <th style=\"width: 200px;\">Date</th>
      <th style=\"width: 200px;\">User</th>
      <th style=\"width: 200px;\">From</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>");

  foreach (dbFetchRows("SELECT *,DATE_FORMAT(datetime, '%D %b %Y %T') as humandate  FROM `authlog` ORDER BY `datetime` DESC LIMIT 0,250") as $entry)
  {
    $class = "";
    if (strstr(strtolower($entry['result']), 'fail', true)) { $class = " class=\"error\""; }
    echo('
    <tr'.$class.'>
      <td>'.$entry['datetime'].'</td>
      <td>'.$entry['user'].'</td>
      <td>'.$entry['address'].'</td>
      <td>'.$entry['result'].'</td>
    </tr>');
  }

  $pagetitle[] = 'Authlog';

  echo("  </tbody>\n");
  echo("</table>\n");
} else {
  include("includes/error-no-perm.inc.php");
}

?>
