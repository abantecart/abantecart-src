<?php echo $header; ?>
<div id="stepbar">
    <div class="tl"><div class="tr"><div class="tc"></div></div></div>
    <div class="cl"><div class="cr"><div class="cc">
      <div class="heading">Installation Steps: </div>
      <div class="step">1: License</div>
      <div class="step">2: Compatibility Check</div>
      <div class="step_current">3: Configuration</div>
      <div class="step">4: Data Load</div>
      <div class="step">5: Finished</div>
    </div></div></div>
    <div class="bl"><div class="br"><div class="bc"></div></div></div>
</div>
<?php if ( !empty($error['warning']) ) { ?>
<div class="warning"><?php echo $error['warning']; ?></div>
<?php } ?>

<div class="main_content">    
  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
  <div class="contentBox">
    <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
      <div class="heading">
        Configuration
        <div class="buttons"><button type="submit" class="btn_standard"><?php echo $form['submit']; ?></button></div>
      </div>
    </div></div></div>
    <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">
    
      <p>1 . Please enter your database connection details.</p>
      <div class="section">
        <table>
          <tr>
            <td width="185">Database Host:</td>
            <td><?php echo $form['db_host']; ?>
              <br />
              <?php if ( !empty($error['db_host']) ) { ?>
              <span class="required"><?php echo $error['db_host']; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td>User:</td>
            <td><?php echo $form['db_user']; ?>
              <br />
              <?php if ( !empty($error['db_user']) ) { ?>
              <span class="required"><?php echo $error['db_user']; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td>Password:</td>
            <td><?php echo $form['db_password']; ?></td>
          </tr>
          <tr>
            <td>Database Name:</td>
            <td><?php echo $form['db_name']; ?>
              <br />
              <?php if ( !empty($error['db_name']) ) { ?>
              <span class="required"><?php echo $error['db_name']; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td>Database Prefix:</td>
            <td><?php echo $form['db_prefix']; ?>
                <br />
                <?php if ( !empty($error['db_prefix']) ) { ?>
                <span class="required"><?php echo $error['db_prefix']; ?></span>
                <?php } ?></td>
          </tr>
        </table>
      </div>
      <p>2. Please enter a name for administrator's section. It needs to be unique alphanumeric name. Only administrators needs to know this to access control panel of the shopping cart application. Example: admin_section_2010</p>
      <div class="section">
        <table>
          <tr>
            <td width="185">Admin section unique name:</td>
            <td><?php echo $form['admin_path']; ?>
              <br />
              <?php if ( !empty($error['admin_path']) ) { ?>
              <span class="required"><?php echo $error['admin_path']; ?></span>
              <?php } ?></td>
          </tr>
        </table>
      </div>
      <p>3. Please enter a username and password for the administration.</p>
      <div class="section">
        <table>
          <tr>
            <td width="185">Username:</td>
            <td><?php echo $form['username']; ?>
              <br />
              <?php if ( !empty($error['username']) ) { ?>
              <span class="required"><?php echo $error['username']; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td>Password:</td>
	        <td><?php echo $form['password']; ?>
              <br />
              <?php if ( !empty($error['password']) ) { ?>
              <span class="required"><?php echo $error['password']; ?></span>
              <?php } ?></td>
          </tr>
	      <tr>
            <td>Confirm Password:</td>
	        <td><?php echo $form['password_confirm']; ?>
              <br />
              <?php if ( !empty($error['password_confirm']) ) { ?>
              <span class="required"><?php echo $error['password_confirm']; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td>E-Mail:</td>
	        <td><?php echo $form['email']; ?>
              <br />
              <?php if ( !empty($error['email']) ) { ?>
              <span class="required"><?php echo $error['email']; ?></span>
              <?php } ?></td>
          </tr>
        </table>
      </div>
      
      <div class="buttons align_right"><button type="submit" class="btn_standard"><?php echo $form['submit']; ?></button></div>
    
    </div></div></div>
    <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
  </div>
  </form>
</div>
<?php echo $footer; ?>