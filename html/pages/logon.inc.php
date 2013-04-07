<div style="width: 700px; margin: auto; margin-top: 30px;">
    <div style="margin: auto; width:700px; padding:5px;">
      <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td><img src="images/login-hamster.png" alt="Login required" /></td>
          <td>
            <form action="" method="post" name="logonform" class="form-horizontal">
              <fieldset>
                <div class="control-group">
                  <div class="controls">
                    <h3>Please log in:</h3>
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="username">Username</label>
                  <div class="controls">
                    <input type="text" class="input-xlarge" id="username" name="username">
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="password">Password</label>
                  <div class="controls">
                    <input type="password" class="input-xlarge" id="password" name="password">
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="optionsCheckbox2"></label>
                  <div class="controls">
                    <label class="checkbox">
                      <input type="checkbox" id="remember" name="remember">
                      Remember my login on this computer
                    </label>
                  </div>
                </div>
                <div class="controls">
                  <button type="submit" class="btn-large btn">
                    <i class="icon-lock"></i>
                    Log in
                  </button>
                </div>
<?php
if (isset($auth_message))
{
  echo('<tr><td colspan="2" style="font-weight: bold; color: #cc0000;">' . $auth_message . '</td></tr>');
}
?>
            </table>
            </fieldset>
            </form>
          </td>
        </tr>
      </table>
<?php
if (isset($config['login_message']))
{
  echo('<div style="margin-top: 10px;text-align: center; font-weight: bold; color: #cc0000; width: 470px;">'.$config['login_message'].'</div>');
}
?>
<script type="text/javascript">
<!--
document.logonform.username.focus();
// -->
</script>

    </div>
</div>
