<div id="gumax-header">
  <div id="gumax-p-logo">
    <div id="p-logo">
      <a style="background-image: url('<?php echo($config['title_image']); ?>');" accesskey="z" href="<?php echo($config['title_url']); ?>"></a>
    </div>
    <script type="text/javascript"> if (window.isMSIE55) fixalpha(); </script>
  </div>
  <!-- end of gumax-p-logo -->

  <!-- Login Tools -->
  <div id="gumax-p-login">

<?php

$toggle_url_biggraphs = preg_replace('/(\?|\&)big_graphs=(yes|no)/', '', $_SERVER['REQUEST_URI']);
if (strstr($toggle_url_biggraphs,'?')) { $toggle_url_biggraphs .= '&amp;'; } else { $toggle_url_biggraphs .= '?'; }

if($_SESSION['big_graphs'] === 1)
{
  echo('<a href="' . $toggle_url_biggraphs . 'big_graphs=no" title="Switch to normal graphs">Normal Graphs</a> | ');
} else {
  echo('<a href="' . $toggle_url_biggraphs . 'big_graphs=yes" title="Switch to larger graphs">Big Graphs</a> | ');
}


$toggle_url_wide = preg_replace('/(\?|\&)widescreen=(yes|no)/', '', $_SERVER['REQUEST_URI']);
if (strstr($toggle_url_wide,'?')) { $toggle_url_wide .= '&amp;'; } else { $toggle_url_wide .= '?'; }

if($_SESSION['widescreen'] === 1)
{
  echo('<a href="' . $toggle_url_wide . 'widescreen=no" title="Switch to normal screen width layout">Normal width</a> | ');
} else {
  echo('<a href="' . $toggle_url_wide . 'widescreen=yes" title="Switch to wide screen layout">Widescreen</a> | ');
}

if ($_SESSION['authenticated'])
{
  echo("Logged in as <b>".$_SESSION['username']."</b>");
} else {
  echo("Not logged in!");
}

if (Net_IPv6::checkIPv6($_SERVER['REMOTE_ADDR']))
{
  echo(' via <b>IPv6</b>');
} else {
  echo(' via <b>IPv4</b>');
}

if ($_SESSION['authenticated'])
{
  echo(" (<a href='logout/'>Logout</a>)");
}
?>

  </div>
  <div style="float: right;">

<?php
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'])
{
  include("includes/topnav.inc.php");
}
?>
  </div>
</div>

        <!-- //// end of gumax-header //// -->

