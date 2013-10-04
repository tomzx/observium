<?php

define('EDITION', 'community');

$config['product']       = "Observium CE";
$config['product_long']  = "Observium Community Edition";

$config['version']  = "0.SVN.ERROR";

$svn_new = TRUE;
if (file_exists($config['install_dir'] . '/.svn/entries'))
{
  $svn = File($config['install_dir'] . '/.svn/entries');
  if ((int)$svn[0] < 12)
  {
    // SVN version < 1.7
    $svn_rev = trim($svn[3]);
    list($svn_date) = explode("T", trim($svn[9]));
    $svn_new = FALSE;
  }
}
if ($svn_new)
{
  // SVN version >= 1.7
  $xml = simplexml_load_string(shell_exec($config['svn'] . ' info --xml ' . $config['install_dir']));
  if ($xml != false)
  {
    $svn_rev = $xml->entry->commit->attributes()->revision;
    $svn_date = $xml->entry->commit->date;
  }
}
if (!empty($svn_rev))
{
  list($svn_year, $svn_month, $svn_day) = explode("-", $svn_date);
  $config['version'] = "0." . ($svn_year-2000) . "." . ($svn_month+0) . "." . $svn_rev;
}

?>
