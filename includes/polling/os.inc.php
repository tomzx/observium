<?php

if (is_file($config['install_dir'] . "/includes/polling/os/".$device['os'].".inc.php"))
{
  // OS Specific
  include($config['install_dir'] . "/includes/polling/os/".$device['os'].".inc.php");
}
elseif ($device['os_group'] && is_file($config['install_dir'] . "/includes/polling/os/".$device['os_group'].".inc.php"))
{
  // OS Group Specific
  include($config['install_dir'] . "/includes/polling/os/".$device['os_group'].".inc.php");
}
else
{
  echo("Generic :(\n");
}

  $update_fields = array('version', 'features', 'hardware', 'serial', 'icon','kernel', 'distro', 'distro_ver', 'arch');

  foreach($update_fields AS $field)
  {
    if (isset($$field) && $$field != $device[$field])
    {
      $update_array[$field] = $$field;
      log_event($field." -> ".$$field, $device, 'system');
    }
  }


echo("\nHardware: ".$hardware." Version: ".$version." Features: ".$features." Serial: ".$serial."\n");

?>
