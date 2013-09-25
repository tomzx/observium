<div class="row">
  <div class="col-md-8 col-md-offset-2">
    <div class="well" style="padding: 50px; background-image: url('/images/login-hamster-large.png');  background-position: left 10px top -65px; background-repeat: no-repeat;">
      <div class="row">
        <div class="col-md-3">
        </div>
        <div class="col-md-9">
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
<?php
if(function_exists('mcrypt_decrypt') && function_exists('mcrypt_encrypt'))
{
?>
                    <label class="checkbox">
                      <input type="checkbox" id="remember" name="remember">
                      Remember my login on this computer
                    </label>
<?php
}
?>
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
  echo('<tr><td colspan="3" style="font-weight: bold; color: #cc0000;">' . $auth_message . '</td></tr>');
}
?>
            </table>
            </fieldset>
            </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php


if (isset($config['login_message']))
{
  echo('<div class=row><div class="col-md-6 col-md-offset-3"><div style="margin-top: 10px;text-align: center; font-weight: bold; color: #cc0000;">'.$config['login_message'].'</div></div></div>');
}
?>
<script type="text/javascript">
<!--
document.logonform.username.focus();
// -->
</script>

    </div>
</div>
