<?php

/**
 * Observium Network Management and Monitoring System
 * Copyright (C) 2006-2013, Adam Armstrong - http://www.observium.org
 *
 * @package    observium
 * @subpackage webui
 * @author     Adam Armstrong <adama@memetic.org>
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 *
 */

 // Hardcode exit if user doesn't have global write permissions. 
  if ($_SESSION['userlevel'] < 10)
  {
    include("includes/error-no-perm.inc.php");
    exit;
  } 

  if(isset($_POST['submit']) && $_POST['submit'] == "add_alert_check")
	{
	  
		echo '<div class="alert alert-info">
		<button type="button" class="close" data-dismiss="alert">×</button>
		<div class="pull-left" style="padding:0 5px 0 0"><i class="oicon-information"></i></div>
		<h4>Adding alert checker</h4>';
		
		foreach( array('entity_type', 'alert_name', 'alert_severity', 'check_conditions', 'assoc_device_conditions', 'assoc_entity_conditions') as $var)
		{
		  if(!isset($_POST[$var]) || strlen($_POST[$var]) == '0') { echo("Missing required data.</div>"); break 2; }
		}
		
		$check_array = array();
		
		$conds = array();
    foreach(explode("\n", $vars['check_conditions']) AS $cond)
    {
      list($this['metric'], $this['condition'], $this['value']) = explode(" ", $cond);
      $conds[] = $this;
    }
    $check_array['conditions'] = json_encode($conds);
		
		$check_array['entity_type'] = $_POST['entity_type'];
		$check_array['alert_name'] = $_POST['alert_name'];
		$check_array['alert_message'] = $_POST['alert_message'];
		$check_array['severity'] = $_POST['alert_severity'];
		$check_array['alerter'] = NULL;
		$check_array['and'] = '1';   
    $check_array['delay'] = $_POST['alert_delay'];		
		$check_array['enable'] = '1';

		$check_id = dbInsert('alert_tests', $check_array);
			
		if(is_numeric($check_id))
		{
		  
		  echo('<p>Alert inserted as <a href="'.generate_url(array('page' => 'alert_check', 'alert_test_id' => $check_id)).'">'.$check_id.'</a></p>');
		
		  $assoc_array = array();
			$assoc_array['alert_test_id'] = $check_id;
	  	$assoc_array['entity_type'] = $_POST['entity_type'];
			$assoc_array['enable'] = '1';
			
			$dev_conds = array();
      foreach(explode("\n", $vars['assoc_device_conditions']) AS $cond)
      {
        list($this['attrib'], $this['condition'], $this['value']) = explode(" ", $cond);
        $dev_conds[] = $this;
      }
      $assoc_array['device_attributes'] = json_encode($dev_conds);
			if($vars['assoc_device_conditions'] == "*") { $vars['assoc_device_conditions'] = json_encode(array()); }
			
			$ent_conds = array();
      foreach(explode("\n", $vars['assoc_entity_conditions']) AS $cond)
      {
        list($this['attrib'], $this['condition'], $this['value']) = explode(" ", $cond);
        $ent_conds[] = $this;
      }
      $assoc_array['attributes'] = json_encode($ent_conds);
			if($vars['assoc_entity_conditions'] == "*") { $vars['assoc_entity_conditions'] = json_encode(array()); }

			$assoc_id = dbInsert('alert_assoc', $assoc_array);
			if(is_numeric($assoc_id))
  		{
	  	  echo("<p>Association inserted as ".$assoc_id."</p>");
      } else {
        echo('<p class="red">Association creation failed.</p>');
			}
		} else {
        echo('<p class="red">Alert creation failed. Please note that the alert name <b>must</b> be unique.</p>');
		}
		echo '</div>';
  }	
	
?>

<form name="form1" method="post" action="" class="form-horizontal">

<legend>New Alert Checker</legend>

<div class="row">
  <div class="col-md-6">
    <div class="well info_box">
      <div class="title"><i class="oicon-bell"></i> Checker Details</div>
      <div class="content">
        <fieldset>
        <div class="control-group">
          <label class="control-label" for="entity_type">Entity Type</label>
          <div class="controls">
            <select name="entity_type" class="selectpicker" data-show-icon="true">
              <?php

		          foreach($config['entities'] as $entity_type => $entity_type_array)
		          {
		            echo '<option value="'.$entity_type.'" ';
								if(!isset($entity_type_array['icon'])) { $entity_type_array['icon'] = $config['entity_default']['icon']; }
                echo($vars['entity_type'] == $entity_type  || ($vars['entity_type'] == '')  ? 'selected' : ''); 
		            echo ' data-icon="'.$entity_type_array['icon'].'"> '.nicecase($entity_type).'</option>';
              }		  
		          ?>
            </select>
          </div>
	      </div>
				
        <div class="control-group">
          <label class="control-label" for="alert_name">Alert Name</label>
          <div class="controls">
            <input type=text name="alert_name" size="32" placeholder="Alert name"/>
          </div>
        </div>
	      <div class="control-group">
          <label class="control-label" for="alert_message">Message</label>
          <div class="controls">
            <textarea class="form-control col-md-11" name="alert_message" rows="3" placeholder="Alert message."/></textarea>
	        </div>
	      </div>
	      <div class="control-group">
          <label class="control-label" for="alert_delay">Alert Delay</label>
          <div class="controls">
            <input type=text name="alert_delay" size="40" placeholder="&#8470; of checks to delay alert."/>
          </div>
        </div>
	      <div class="control-group">
          <label class="control-label" for="alert_severity">Severity</label>
          <div class="controls">
            <select name="alert_severity" class="selectpicker">
              <!-- This option is unimplemented, so anything other than critical is hidden for now -->
		          <!-- <option value="info">Informational</option> -->
  		        <!-- <option value="warn">Warning</option> -->
              <option value="crit" data-icon="oicon-exclamation-red">Critical</option>
		        </select>
	        </div>
	      </div>  
        </fieldset>
      </div> <!-- content -->
    </div> <!--  info_box-->
  </div> <!-- col -->
 
 <div class="col-md-6">
    <div class="well info_box">
      <div class="title"><i class="oicon-traffic-light"></i> Checker Conditions</div>
      <div class="title" style="float: right; margin-bottom: -10px;"><a href="#" class="tooltip-from-element" data-tooltip-id="tooltip-help-conditions"><i class="oicon-question"></i></a></div>
      <div class="content">
        <textarea class="col-md-12" rows="3" name="check_conditions"></textarea>
      </div> <!-- content -->
    </div> <!-- infobox -->
		
		<div class="well info_box">
      <div class="title"><i class="oicon-sql-join-left"></i> Assocations</div>
      <div class="title" style="float: right; margin-bottom: -10px;"><a href="#" class="tooltip-from-element" data-tooltip-id="tooltip-help-associations"><i class="oicon-question"></i></a></div>
      <div class="content">
			  <div class="control-group">
	  		  <label>Device Association</label>
	  		  <textarea class="col-md-12" rows="3" name="assoc_device_conditions" placeholder=""></textarea>
					</div>
	  		<div class="control-group">
				  <label>Entity Association</label>
          <textarea class="col-md-12" rows="3" name="assoc_entity_conditions"></textarea>
        </div>
		  </div> <!-- content -->
    </div> <!-- infobox -->
		
		
  </div> <!-- col -->
</div> <!-- row -->

<div class="form-actions">
  <button type="submit" class="btn btn-success" name="submit" value="add_alert_check"><i class="icon-plus oicon-white"></i> Add Check</button>
</div>

</form>

<div id="tooltip-help-conditions" style="display: none;">

      Conditions should be entered in this format
			<pre>metric_1 condition value_1
metric_2 condition value_2
metric_3 condition value_3</pre>

      For example to alert when an enabled port is down
			<pre>ifAdminStatus equals up 
ifOperStatus equals down</pre>
			
</div>

<div id="tooltip-help-associations" style="display: none;">

      Associations should be entered in this format
			<pre>attribute_1 condition value_1
attribute_2 condition value_2
attribute_3 condition value_3</pre>

Device attributes include: <span class=label>hostname</span>, <span class="label">sysName</span>, <span class="label">sysDescr</span>, <span class="label">sysContact</span>, <span class="label">version</span>, <span class="label">distro</span>, <span class="label">distro_ver</span>, <span class="label">kernel</span>, <span class="label">arch</span>, <span class="label">features</span>, <span class="label">location</span>, <span class="label">location_[city|county|state|country]</span>, <span class="label">os</span>, <span class="label">type</span> and <span class="label">serial</span>.<br />

      For example, to match a network device with core in its hostname
			<pre>type equals network 
hostname match *cisco*</pre>
			
			For example, to match an ethernet port which is connected at 10 gigabit 
			<pre>ifType equals ethernetCsmacd
ifSpeed ge 100000000</pre>
			
			If you put * in either the device or entity fields, it will match all devices or all entities respectively.
</div>


<?php
 
 
// EOF
