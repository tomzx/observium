<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    observium
 * @subpackage webinterface
 * @author     Adam Armstrong <adama@memetic.org>
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */

include("../includes/defaults.inc.php");
include("../config.php");
include_once("../includes/definitions.inc.php");
include("../includes/functions.php");
include("includes/functions.inc.php");
include("includes/functions-print.inc.php");

$runtime_start = utime();

ob_start();

ini_set('allow_url_fopen', 0);
ini_set('display_errors', 0);

$_SERVER['PATH_INFO'] = (isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : $_SERVER['ORIG_PATH_INFO']);

if (strpos($_SERVER['PATH_INFO'], "debug"))
{
  $debug = "1";
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  ini_set('log_errors', 1);
  ini_set('error_reporting', E_ALL ^ E_NOTICE);
} else {
  $debug = FALSE;
  ini_set('display_errors', 0);
  ini_set('display_startup_errors', 0);
  ini_set('log_errors', 0);
  ini_set('error_reporting', 0);
}

// Parse GET variables into $vars for backwards compatibility
// Can probably remove this soon
foreach ($_GET as $key=>$get_var)
{
  if (strstr($key, "opt"))
  {
    list($name, $value) = explode("|", $get_var);
    if (!isset($value)) { $value = "yes"; }
    $vars[$name] = $value;
  }
}

// Parse URI into $vars
$segments = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

foreach ($segments as $pos => $segment)
{
  $segment = urldecode($segment);
  if ($pos == "0" && !strpos($segment, '='))
  {
    $vars['page'] = $segment;
  } else {
    list($name, $value) = explode("=", $segment);

    if ($value == "" || !isset($value))
    {
      $vars[$name] = yes;
    } else {
      $vars[$name] = urldecode($value);
    }
  }
}

foreach ($_GET as $name => $value)
{
  $vars[$name] = urldecode($value);
}

foreach ($_POST as $name => $value)
{
  $vars[$name] = $value;
}

# Preflight checks 
if (!is_dir($config['rrd_dir']))
{
  print_error("RRD Log Directory is missing ({$config['rrd_dir']}).  Graphing may fail.");
}
 
if (!is_dir($config['temp_dir']))
{
  print_error("Temp Directory is missing ({$config['temp_dir']}).  Graphing may fail.");
}
 
if (!is_writable($config['temp_dir']))
{
  print_error("Temp Directory is not writable ({$config['tmp_dir']}).  Graphing may fail.");
}

include("includes/authenticate.inc.php");

if ($vars['widescreen'] == "yes") { $_SESSION['widescreen'] = 1; unset($vars['widescreen']); }
if ($vars['widescreen'] == "no")  { unset($_SESSION['widescreen']); unset($vars['widescreen']); }

if ($vars['big_graphs'] == "yes") { $_SESSION['big_graphs'] = 1; unset($vars['big_graphs']); }
if ($vars['big_graphs'] == "no")  { unset($_SESSION['big_graphs']); unset($vars['big_graphs']); }

// Load the settings for Multi-Tenancy.
if (isset($config['branding']) && is_array($config['branding']))
{
  if ($config['branding'][$_SERVER['SERVER_NAME']])
  {
    foreach ($config['branding'][$_SERVER['SERVER_NAME']] as $confitem => $confval)
    {
      eval("\$config['" . $confitem . "'] = \$confval;");
    }
  } else {
    foreach ($config['branding']['default'] as $confitem => $confval)
    {
      eval("\$config['" . $confitem . "'] = \$confval;");
    }
  }
}

// page_title_prefix is displayed, unless page_title is set
if ($config['page_title']) { $config['page_title_prefix'] = $config['page_title']; }

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title><?php echo($config['page_title_prefix'] . ($config['page_title_prefix'] != '' && $config['page_title_suffix'] != '' ? ' - ' : '') . $config['page_title_suffix']); ?></title>
  <base href="<?php echo($config['base_url']); ?>" />
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
  <meta http-equiv="content-language" content="en-us" />
<?php
if ($config['page_refresh']) { echo('  <meta http-equiv="refresh" content="'.$config['page_refresh'].'" />' . "\n"); }
?>
  <link rel="shortcut icon" href="<?php echo($config['favicon']);  ?>" />
  <link href="css/bootstrap.css" rel="stylesheet" type="text/css" />
  <link href="css/google-code-prettify.css" rel="stylesheet" type="text/css" />
  <link href="css/jquery.qtip.min.css" rel="stylesheet" type="text/css" />
  <link href="css/mktree.css" rel="stylesheet" type="text/css" />
  <link href="css/sprite.css" rel="stylesheet" type="text/css" />
  <link href="css/flags.css" rel="stylesheet" type="text/css" />

  <script type="text/javascript" src="js/jquery.min.js"></script>
  <script type="text/javascript" src="js/google-code-prettify.js"></script>

<?php
if ($_SESSION['widescreen']) { echo('<link rel="stylesheet" href="css/styles-wide.css" type="text/css" />'); }
?>
</head>

<body>
  <div class="container">

<?php

// To help debug the new URLs :)
if ($devel || $vars['devel'])
{
  echo("<pre>");
  print_r($_GET);
  print_r($vars);
  echo("</pre>");
}

if ($_SESSION['authenticated'])
{
  // Do various queries which we use in multiple places
  include("includes/cache-data.inc.php");

  // Include navbar
  if (!$vars['bare'] == "yes") {  include("includes/navbar.inc.php"); }

  // Authenticated. Print a page.
  if (isset($vars['page']) && !strstr("..", $vars['page']) &&  is_file("pages/" . $vars['page'] . ".inc.php"))
  {
    include("pages/" . $vars['page'] . ".inc.php");
  } else {
    if (isset($config['front_page']) && is_file($config['front_page']))
    {
      include($config['front_page']);
    } else {
      include("pages/front/default.php");
    }
  }

} else {
  // Not Authenticated. Print login.
  include("pages/logon.inc.php");

  exit;
}

?>
<?php

$runtime_end = utime(); $runtime = $runtime_end - $runtime_start;
$gentime = substr($runtime, 0, 5);
$fullsize = memory_get_usage();
unset($cache);
$cachesize = $fullsize - memory_get_usage();
if ($cachesize < 0) { $cachesize = 0; } // Silly PHP!

?>
</div>

<div class="navbar navbar-fixed-bottom">
  <div class="navbar-inner">
    <div class="container">
      <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="oicon-bar"></span>
        <span class="oicon-bar"></span>
        <span class="oicon-bar"></span>
      </a>
      <div class="nav-collapse">
        <ul class="nav">
          <li class="divider-vertical" style="margin:0;"></li>

          <li><a href="http://www.observium.org">Observium <?php echo $config['version']; ?></a></li>
          <li class="divider-vertical" style="margin:0;"></li>
        </ul>

        <ul class="nav pull-right">
          <li><a id="poller_status"></a></li>

          <li class="divider-vertical" style="margin:0;"></li>
          <li class="dropdown">
            <a href="<?php echo(generate_url(array('page'=>'overview'))); ?>" class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown">
              <i class="oicon-time"></i> Perf <b class="caret"></b></a>
            <div class="dropdown-menu" style="padding: 10px;">
              <table class="table table-bordered table-condensed-more table-rounded table-striped">
                <tr>
                  <th>Time</th><td><?php echo($gentime); ?>s</td>
                </tr>
              </table>
              <table class="table table-bordered table-condensed-more table-rounded table-striped">
                <tr>
                  <th colspan=2>MySQL</th>
                </tr>
                <tr>
                  <th>Cell</th><td><?php echo(($db_stats['fetchcell']+0).'/'.round($db_stats['fetchcell_sec']+0,3).'s'); ?></td>
                </tr>
                <tr>
                  <th>Row</th><td><?php echo(($db_stats['fetchrow']+0).'/'.round($db_stats['fetchrow_sec']+0,3).'s'); ?></td>
                </tr>
                <tr>
                  <th>Rows</th><td><?php echo(($db_stats['fetchrows']+0).'/'.round($db_stats['fetchrows_sec']+0,3).'s'); ?></td>
                </tr>
                <tr>
                  <th>Column</th><td><?php echo(($db_stats['fetchcol']+0).'/'.round($db_stats['fetchcol_sec']+0,3).'s'); ?></td>
                </tr>
              </table>
              <table class="table table-bordered table-condensed-more table-rounded table-striped">
                <tr>
                  <th colspan=2>Memory</th>
                </tr>
                <tr>
                  <th>Cached</th><td><?php echo formatStorage($cachesize); ?></td>
                </tr>
                <tr>
                  <th>Page</th><td><?php echo formatStorage($fullsize); ?></td>
                </tr>
                <tr>
                  <th>Peak</th><td><?php echo formatStorage(memory_get_peak_usage()); ?></td>
                </tr>
              </table>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

<?php
if (is_array($pagetitle))
{
  // if prefix is set, put it in front
  if ($config['page_title_prefix']) { array_unshift($pagetitle,$config['page_title_prefix']); }

  // if suffix is set, put it in the back
  if ($config['page_title_suffix']) { $pagetitle[] = $config['page_title_suffix']; }

  // create and set the title
  $title = join(" - ",$pagetitle);
  echo("<script type=\"text/javascript\">\ndocument.title = '$title';\n</script>");
}
?>

  <script type="text/javascript">
//  $(document).ready(function()
//  {
//    $('#poller_status').load('ajax_poller_status.php');
//  });
//
//  var auto_refresh = setInterval(
//    function ()
//    {
//      $('#poller_status').load('ajax_poller_status.php');
//    }, 10000); // refresh every 10000 milliseconds
  </script>

  <script type="text/javascript">
  <!-- Begin
  function popUp(URL)
  {
    day = new Date();
    id = day.getTime();
    eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,        menubar=0,resizable=1,width=550,height=600');");
  }
  // End -->
  </script>

  <script src="js/bootstrap.min.js"></script>
  <script src="js/twitter-bootstrap-hover-dropdown.min.js"></script>
  <script src="js/bootstrap-datetimepicker.min.js"></script>
  <script src="js/bootstrap-select.min.js"></script>
  <script type="text/javascript">$('.selectpicker').selectpicker();</script>
  <script type="text/javascript" src="js/mktree.js"></script>
  <script type="text/javascript" src="js/sorttable.js"></script>
  <script type="text/javascript" src="js/jquery.switch.js"></script>
  <script type="text/javascript" src="js/jquery-checkbox.js"></script>
  <script type="text/javascript" src="js/jquery.qtip.min.js"></script>
  <script type="text/javascript">
  jQuery(document).ready(function($) {
    $(".tooltip-from-element").each(function() {
      var selector = '#' + $(this).data('tooltip-id');
      $(this).qtip({
        content: $(selector),
        style: {
                classes: 'qtip-bootstrap',
        },
        position: {
                target: 'mouse',
                viewport: $(window),
                adjust: {
                        x: 2,
                        y: 2
                }
        }
      });
    });

    $('.tooltip-from-data').qtip({
      content: {
              attr: 'data-tooltip'
      },
      style: {
              classes: 'qtip-bootstrap',
      },
      position: {
              target: 'mouse',
              viewport: $(window),
              adjust: {
                      x: 2,
                      y: 2
              }
      }
    })
  });
  </script>

  <?php /* html5.js below from http://html5shim.googlecode.com/svn/trunk/html5.js */ ?>
  <!--[if IE]><script src="js/html5.js"></script><![endif]-->
  <!--  <script language="javascript" type="text/javascript" src="js/jqplot/jquery.jqplot.min.js"></script>
  <link rel="stylesheet" type="text/css" href="js/jqplot/jquery.jqplot.min.css" />
  <script type="text/javascript" src="js/jqplot/plugins/jqplot.pieRenderer.min.js"></script>
  <script type="text/javascript" src="js/jqplot/plugins/jqplot.donutRenderer.min.js"></script>
  -->


  </body>
</html>
