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

  $update_fields = array('version', 'features', 'hardware', 'serial', 'kernel', 'distro', 'distro_ver', 'arch', 'asset_tag');

  foreach($update_fields AS $field)
  {
    if ($$field != $device[$field])
    {
      $update_array[$field] = $$field;
      log_event(ucfirst($field)." -> ".$$field, $device, 'system');
    }
  }

echo("\nHardware: ".$hardware." Version: ".$version." Features: ".$features." Serial: ".$serial." Asset: ".$asset_tag."\n");

?>
