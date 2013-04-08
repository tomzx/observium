<?php

include("includes/graphs/common.inc.php");

  $rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/app-nfs-".$app['app_id'].".rrd";

  $array = array(
        "null",      "read",      "write",   "commit",      "open",        "open_conf",
        "open_noat", "open_dgrd", "close",   "setattr",     "fsinfo",      "renew",
        "setclntid", "confirm",   "lock",
        "lockt",     "locku",     "access",  "getattr",     "lookup",      "lookup_root",
        "remove",    "rename",    "link",    "symlink",     "create",      "pathconf",
        "statfs",    "readlink",  "readdir", "server_caps", "delegreturn", "getacl",
        "setacl",    "fs_locations",
        "rel_lkowner", "secinfo",
        /* nfsv4.1 client ops */
        "exchange_id",
        "create_ses",
        "destroy_ses",
        "sequence",
        "get_lease_t",
        "reclaim_comp",
        "layoutget",
        "getdevinfo",
        "layoutcommit",
        "layoutreturn",
        "getdevlist",
  );

$i = 0;
if (is_file($rrd_filename))
{
  foreach ($array as $name)
  {
    $rrd_list[$i]['filename'] = $rrd_filename;
    $rrd_list[$i]['descr'] = $name;
    $rrd_list[$i]['ds'] = 'proc4'.$name;
    $i++;
  }
} else { echo("file missing: $file");  }

$colours   = "mixed";
$nototal   = 0;
$unit_text = "Rows";

include("includes/graphs/generic_multi_simplex_seperated.inc.php");

?>
