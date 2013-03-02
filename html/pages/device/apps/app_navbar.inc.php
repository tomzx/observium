<?php

$navbar['brand'] = nicecase($app['app_type']);
$navbar['class'] = "navbar-narrow";
foreach ($app_sections as $app_section => $app_section_text)
{
  if (!$vars['app_section']) { $vars['app_section'] = $app_section; }
  $navbar['brand'] = nicecase($app['app_type']);
  $navbar['options'][$app_section]['text'] = $app_section_text;
  if ($vars['app_section'] == $app_section) { $navbar['options'][$app_section]['class'] = "active"; }
  $navbar['options'][$app_section]['url'] = generate_url($vars,array('app_section'=>$app_section));
}
print_navbar($navbar);

?>
