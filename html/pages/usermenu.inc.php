<?php

$links['add']      = generate_url(array('page' => 'adduser'));
$links['edit']     = generate_url(array('page' => 'edituser'));
$links['log']      = generate_url(array('page' => 'authlog'));
$active['add']     = (($vars['page'] == "adduser") ? "active" : "");
$active['edit']    = (($vars['page'] == "edituser") ? "active" : "");
$active['log']     = (($vars['page'] == "authlog") ? "active" : "");
$isUserlist        = (isset($vars['user_id']) ? true : false);

echo("
      <div class=\"navbar\" style=\"margin-top: 10px;\">
        <div class=\"navbar-inner\">
          <a class=\"brand\">Users:</a>
          <ul class=\"nav\">
            <li class=\"".$active['add']." first\"><a href=\"".$links['add']."\"><i class=\"oicon-plus-sign\"></i> Add User</a></li>
            <li class=\"".$active['edit']."\"><a href=\"".$links['edit']."\"><i class=\"oicon-edit\"></i> Edit Users</a></li>
            <li class=\"".$active['log']."\"><a href=\"".$links['log']."\"><i class=\"oicon-calendar\"></i> Authlog</a></li>
          </ul>");
if ($isUserlist) {
  echo("
          <ul class=\"nav pull-right\">
            <li class=\"first\"><a href=\"".$links['edit']."\"><i class=\"oicon-chevron-left\"></i> <strong>Back to userlist</strong></a></li>
          </ul>");
}
echo("
        </div>
      </div>");

?>
