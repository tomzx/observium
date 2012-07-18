<form class="well form-search" method="post" action="" style="padding-bottom: 10px;">
  <fieldset>
    <strong>Search:</strong>
    <input class="span4" type="text" name="hostname" id="hostname" value="<?php echo($_POST['hostname']); ?>" />
    <select class="span2" name="os" id="os">
      <option value="">All Types</option>
      <option value="">CDR 95th</option>
      <option value="">Quota</option>
      <!-- <option value="">Average</option> //-->
    </select>
    <select class="span2" name="hardware" id="hardware">
      <option value=''>All States</option>
      <option value=''>Under Quota</option>
      <option value=''>Over Quota</option>
    </select>
    <select class="span2" name="location" id="location">
      <option value=''>All Customers</option>
    </select>
    <input type="submit" class="btn btn-info" value="Search">
  </fieldset>
</form>
