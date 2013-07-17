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
 * Generate search form
 *
 * generates a search form.
 * types allowed: select, multiselect, text (or input), datetime, newline
 * 
 * Example of use:
 *  - array for 'select' item type
 *  $search[] = array('type'    => 'select',          // Type
 *                    'name'    => 'Search By',       // Displayed title for item
 *                    'id'      => 'searchby',        // Item id and name
 *                    'width'   => '120px',           // (Optional) Item width
 *                    'size'    => '15',              // (Optional) Maximum number of items to show in the menu (default 15)
 *                    'value'   => $vars['searchby'], // (Optional) Current value(-s) for item
 *                    'values'  => array('mac' => 'MAC Address',
 *                                       'ip'  => 'IP Address'));  // Array with option items
 *  - array for 'multiselect' item type (array keys same as above)
 *  $search[] = array('type'    => 'multiselect',
 *                    'name'    => 'Priorities',
 *                    'id'      => 'priority',
 *                    'width'   => '150px',
 *                    'subtext' => TRUE,              // (Optional) Display items value right of the item name
 *                    'value'   => $vars['priority'],
 *                    'values'  => $priorities);
 *  - array for 'text' or 'input' item type
 *  $search[] = array('type'  => 'text',
 *                    'name'  => 'Address',
 *                    'id'    => 'address',
 *                    'width' => '120px',
 *                    'value' => $vars['address']);
 *  - array for 'datetime' item type
 *  $search[] = array('type'  => 'datetime',
 *                    'id'    => 'timestamp',
 *                    'presets' => TRUE,                  // (optional) Show select field with timerange presets
 *                    'min'   => dbFetchCell('SELECT MIN(`timestamp`) FROM `syslog`'), // (optional) Minimum allowed date/time
 *                    'max'   => dbFetchCell('SELECT MAX(`timestamp`) FROM `syslog`'), // (optional) Maximum allowed date/time
 *                    'from'  => $vars['timestamp_from'], // (optional) Current 'from' value
 *                    'to'    => $vars['timestamp_to']);  // (optional) Current 'to' value
 *  - array for 'newline' item pseudo type
 *  $search[] = array('type' => 'newline')
 *  print_search_simple($search, 'Title here');
 *
 * @param array $data, string $title
 * @return none
 *
 */

function print_search_simple($data, $title = '', $button = 'search')
{
  // Form header
  $string = PHP_EOL . '<!-- START search form -->' . PHP_EOL;
  $string .= '<form method="POST" action="" class="form form-inline">' . PHP_EOL;
  $string .= '<div class="navbar">' . PHP_EOL;
  $string .= '<div class="navbar-inner">';
  $string .= '<div class="container">';
  if ($title) { $string .= '  <a class="brand">' . $title . '</a>' . PHP_EOL; }

  $string .= '<div class="nav" style="margin: 5px 0 5px 0;">';

  // Main
  foreach ($data as $item)
  {
    if (!isset($item['value'])) { $item['value'] = ''; }
    switch($item['type'])
    {
      case 'text':
      case 'input':
        $string .= '  <div class="input-prepend">' . PHP_EOL;
        if (!$item['name']) { $item['name'] = '<i class="icon-list"></i>'; }
        $string .= '    <span class="add-on">'.$item['name'].'</span>' . PHP_EOL;
        $string .= '    <input type="'.$item['type'].'" ';
        $string .= (isset($item['width'])) ? 'style="width:'.$item['width'].'" ' : '';
        $string .= 'name="'.$item['id'].'" id="'.$item['id'].'" class="input" value="'.$item['value'].'"/>' . PHP_EOL;
        $string .= '  </div>' . PHP_EOL;
        // End 'text' & 'input'
        break;
      case 'newline':
        $string .= '<div class="clearfix" id="'.$item['id'].'"><hr /></div>' . PHP_EOL;
        // End 'newline'
        break;
      case 'datetime':
        $id_from = $item['id'].'_from';
        $id_to = $item['id'].'_to';
        // Presets
        if ($item['presets'])
        {
          $presets = array('sixhours'  => 'Last 6 hours',
                           'today'     => 'Today',
                           'yesterday' => 'Yesterday',
                           'tweek'     => 'This week',
                           'lweek'     => 'Last week',
                           'tmonth'    => 'This month',
                           'lmonth'    => 'Last month',
                           'tyear'     => 'This year',
                           'lyear'     => 'Last year');
          $string .= '    <select id="'.$item['id'].'" class="selectpicker show-tick" data-size="false" data-width="auto">' . PHP_EOL . '      ';
          $string .= '<option value="" selected>Date/Time presets</option>';
          foreach ($presets as $k => $v)
          {
            $string .= '<option value="'.$k.'">'.$v.'</option> ';
          }
          $string .= PHP_EOL . '    </select>' . PHP_EOL;
        }
        // Date/Time input fields
        $string .= '  <div class="input-prepend" id="'.$id_from.'">' . PHP_EOL;
        $string .= '    <span class="add-on btn"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> From</span>' . PHP_EOL;
        $string .= '    <input type="text" class="input-medium" data-format="yyyy-MM-dd hh:mm:ss" ';
        $string .= 'name="'.$id_from.'" id="'.$id_from.'" value="'.$item['from'].'"/>' . PHP_EOL;
        $string .= '  </div>' . PHP_EOL;
        $string .= '  <div class="input-prepend" id="'.$id_to.'">' . PHP_EOL;
        $string .= '    <span class="add-on btn"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> To</span>' . PHP_EOL;
        $string .= '    <input type="text" class="input-medium" data-format="yyyy-MM-dd hh:mm:ss" ';
        $string .= 'name="'.$id_to.'" id="'.$id_to.'" value="'.$item['to'].'"/>' . PHP_EOL;
        $string .= '  </div>' . PHP_EOL;
        // JS
        $min = '-Infinity';
        $max = 'Infinity';
        $pattern = '/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/';
        if (!empty($item['min']))
        {
          if (preg_match($pattern, $item['min'], $matches))
          {
            $matches[2] = $matches[2] - 1;
            array_shift($matches);
            $min = 'new Date(' . implode(',', $matches) . ')';
          }
        } 
        if (!empty($item['max']))
        {
          if (preg_match($pattern, $item['max'], $matches))
          {
            $matches[2] = $matches[2] - 1;
            array_shift($matches);
            $max = 'new Date(' . implode(',', $matches) . ')';
          }
        } 
        $string .= '
    <script type="text/javascript">
      var startDate = '.$min.';
      var endDate   = '.$max.';
      $(document).ready(function() {
	$(\'#'.$id_from.'\').datetimepicker({
          //pickSeconds: false,
          weekStart: 1,
          startDate: startDate,
          endDate: endDate
        });
	$(\'#'.$id_to.'\').datetimepicker({
          //pickSeconds: false,
          weekStart: 1,
          startDate: startDate,
          endDate: endDate
        });
      });' . PHP_EOL;
        if ($item['presets'])
        {
          $string .= '
      $(\'select#'.$item['id'].'\').change(function() {
        var input_from = $(\'input#'.$id_from.'\');
        var input_to   = $(\'input#'.$id_to.'\');
        switch ($(this).val()) {' . PHP_EOL;
          foreach ($presets as $k => $v)
          {
            $preset = datetime_preset($k);
            $string .= "          case '$k':\n";
            $string .= "            input_from.val('".$preset['from']."');\n";
            $string .= "            input_to.val('".$preset['to']."');\n";
            $string .= "            break;\n";
          }
          $string .= '
          default:
            input_from.val("");
            input_to.val("");
            break;
        }
      });' . PHP_EOL;
        }
        $string .= '</script>' . PHP_EOL;
        // End 'datetime'
        break;
      case 'select':
      case 'multiselect':
        if ($item['type'] == 'multiselect')
        {
          $title = (isset($item['name'])) ? 'title="'.$item['name'].'" ' : '';
          $string .= '    <select multiple name="'.$item['id'].'[]" ' . $title;
        } else {
          $string .= '    <select name="'.$item['id'].'" ';
          if ($item['name'] && !isset($item['values']['']))
          {
            $item['values'] = array('' => $item['name']) + $item['values'];
          }
        }
        $string .= 'id="'.$item['id'].'" ';
        $data_width = ($item['width']) ? ' data-width="'.$item['width'].'"' : ' data-width="auto"';
        $data_size = (is_numeric($item['size'])) ? ' data-size="'.$item['size'].'"' : ' data-size="15"';
        $string .= 'class="selectpicker show-tick" data-selected-text-format="count>1"';
        $string .= $data_width . $data_size . '>' . PHP_EOL . '      ';
        if (!is_array($item['value'])) { $item['value'] = array($item['value']); }
        foreach ($item['values'] as $k => $v)
        {
          $k = (string)$k;
          $data_subtext = ($item['subtext']) ? ' data-subtext="('.$k.')"' : '';
          $string .= '<option value="'.$k.'"' . $data_subtext;
          $string .= (in_array($k, $item['value'])) ? ' selected>' : '>';
          $string .= $v.'</option> ';
        }
        $string .= PHP_EOL . '    </select>' . PHP_EOL;
        // End 'select' & 'multiselect'
        break;
    }
  }

  $string .= '</div>';

  // Form footer
  $string .= '    <ul class="nav pull-right"><li>' . PHP_EOL;
  $string .= '      <input type="hidden" name="pageno" value="1">' . PHP_EOL;
  switch($button)
  {
    case 'update':
      $string .= '      <button type="submit" class="btn"><i class="icon-refresh"></i> Update</button>' . PHP_EOL;
      break;
    default:
      $string .= '      <button type="submit" class="btn"><i class="icon-search"></i> Search</button>' . PHP_EOL;
  }
  $string .= '    </li></ul>' . PHP_EOL;
  $string .= '</div></div></div></form>' . PHP_EOL;
  $string .= '<!-- END search form -->' . PHP_EOL . PHP_EOL;

  // Print search form
  echo($string);
}

?>
