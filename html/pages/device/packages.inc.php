<?php

echo("<table class=\"table table-bordered table-striped\">\n");
echo("  <thead>\n");
echo("    <tr>\n");
echo("      <th style=\"width: 300px;\">Package name</th>\n");
echo("      <th>Version</th>\n");
echo("      <th>Architecture</th>\n");
echo("      <th>Type</th>\n");
echo("      <th>Size</th>\n");
echo("    </tr>\n");
echo("  </thead>\n");
echo("  <tbody>\n");

$i=0;
foreach (dbFetchRows("SELECT * FROM `packages` WHERE `device_id` = ? ORDER BY `name`", array($device['device_id'])) as $entry)
{
  echo("    <tr>\n");
  echo("      <td><a href=\"". generate_url($vars, array('name' => $entry['name']))."\">".$entry['name']."</a></td>\n");
  if ($build != '') { $dbuild = '-'.$entry['build']; } else { $dbuild = ''; }
  echo("      <td>".$entry['version'].$dbuild."</td>\n");
  echo("      <td>".$entry['arch']."</td>\n");
  echo("      <td>".$entry['manager']."</td>\n");
  echo("      <td>".format_si($entry['size'])."</td>\n");
  echo("    </tr>\n");

  $i++;
}

echo("  </tbody>\n");
echo("</table>\n");

?>
