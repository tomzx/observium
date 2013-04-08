<?php

include("includes/graphs/common.inc.php");

  $rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/app-nfs-".$app['app_id'].".rrd";

  $array = array(
        "null",   "getattr", "setattr",  "lookup", "access",  "readlink",
        "read",   "write",   "create",   "mkdir",  "symlink", "mknod",
        "remove", "rmdir",   "rename",   "link",   "readdir", "readdirplus",
        "fsstat", "fsinfo",  "pathconf", "commit"
  );

$i = 0;
if (is_file($rrd_filename))
{
  foreach ($array as $name)
  {
    $rrd_list[$i]['filename'] = $rrd_filename;
    $rrd_list[$i]['descr'] = $name;
    $rrd_list[$i]['ds'] = 'proc3'.$name;
    $i++;
  }
} else { echo("file missing: $file");  }

$colours   = "mixed";
$nototal   = 0;
$unit_text = "Rows";

include("includes/graphs/generic_multi_simplex_seperated.inc.php");

?>
