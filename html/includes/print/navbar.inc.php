<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    observium
 * @subpackage web
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */

/**
 * Generate Bootstrap-format Navbar
 *
 *   A little messy, but it works and lets us move to having no navbar markup on pages :)
 *   Examples:
 *   print_navbar(array('brand' => "Apps", 'class' => "navbar-narrow", 'options' => array('mysql' => array('text' => "MySQL", 'url' => generate_url($vars, 'app' => "mysql")))))
 *
 * @param array $vars
 * @return none
 *
 */
function print_navbar($navbar)
{
  global $config;

  $id = strgen();

  ?>

  <div class="navbar <?php echo $navbar['class']; ?>">
    <div class="navbar-inner">
      <div class="container">
        <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target="#nav-<?php echo $id; ?>">
          <span class="oicon-bar"></span>
        </button>

  <?php

  if (isset($navbar['brand'])) { echo ' <a class="brand">'.$navbar['brand'].'</a>'; }
  echo('<div class="nav-collapse" id="nav-'.$id.'">');

  foreach (array('options', 'options_right') as $array_name)
  {
    if ($array_name == "options_right") {
      if (!$navbar[$array_name]) { break; }
      echo('<ul class="nav pull-right">');
    } else {
      echo('<ul class="nav">');
    }
    foreach ($navbar[$array_name] as $option => $array)
    {
      if ($array[''] == "pull-right") {
        $navbar['options_right'][$option] = $array;
      } else {
        if (!is_array($array['suboptions']))
        {
          echo('<li class="'.$array['class'].'">');
          echo('<a href="'.$array['url'].'">');
          if (isset($array['icon'])) { echo('<i class="'.$array['icon'].'"></i> '); }
          echo($array['text'].'</a>');
          echo('</li>');
        } else {
          echo('  <li class="dropdown">');
          echo('    <a class="dropdown-toggle" data-toggle="dropdown"  href="'.$array['url'].'">');
          if (isset($array['icon'])) { echo('<i class="'.$array['icon'].'"></i> '); }
          echo($array['text'].'
            <b class="caret"></b>
          </a>
        <ul class="dropdown-menu">');
          foreach ($array['suboptions'] as $suboption => $subarray)
          {
            echo('<li class="'.$subarray['class'].'">');
            echo('<a href="'.$subarray['url'].'">');
            if (isset($subarray['icon'])) { echo('<i class="'.$subarray['icon'].'"></i> '); }
            echo($subarray['text'].'</a>');
            echo('</li>');
          }
          echo('    </ul>
      </li>');
        }
      }
    }
    echo('</ul>');
  }

  ?>
        </div>
      </div>
    </div>
  </div>

 <?php

}

?>
