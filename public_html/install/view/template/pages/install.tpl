<?php echo $header; ?>
<div class="row">
	<div class="col-md-9">
	<div class="panel panel-default">

	<ul class="nav nav-tabs" role="tablist">
	  <li class="disabled"><a href="#" onclick="return false;">1: License</a></li>
	  <li class="disabled"><a href="<?php echo $back; ?>">2: Compatibility Validation</a></li>
	  <li class="active"><a href="#" onclick="return false;">3: Configuration</a></li>
	  <li class="disabled"><a href="#" onclick="return false;">4: Data Load</a></li>
	  <li class="disabled"><a href="#" onclick="return false;">5: Finished</a></li>
	</ul>

	<div class="panel-heading">
	<h2><i class="fa fa-gear fa-fw"></i> Configuration <small>Provide setting below</small></h2>
	</div>
	
	<div class="panel-body panel-body-nopadding">
		<form class="form-horizontal" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">

		<?php if ($error['warning']) { ?>
		<div class="warning alert alert-error alert-danger"><?php echo $error['warning']; ?></div>
		<?php } ?>

		<label class="h5 heading">1 . Please enter your database connection details.</label>

		<div class="form-group <?php if (!empty($error['db_driver'])) { echo "has-error"; } ?>">
			<label class="control-label col-sm-4 col-xs-12">Database Driver:</label>
			<div class="input-group col-sm-6 col-xs-12 afield">
				<?php echo $form['db_driver']; ?>
			</div>
			<?php if (!empty($error['db_driver'])) { ?>
				<span class="help-block field_err"><?php echo $error['db_driver']; ?></span>
			<?php } ?>
		</div>

		<div class="form-group <?php if (!empty($error['db_host'])) { echo "has-error"; } ?>">
			<label class="control-label col-sm-4 col-xs-12">Database Hostname:</label>
			<div class="input-group col-sm-6 col-xs-12 afield">
				<?php echo $form['db_host']; ?>
			</div>
			<?php if (!empty($error['db_host'])) { ?>
				<span class="help-block field_err"><?php echo $error['db_host']; ?></span>
			<?php } ?>
		</div>

		<div class="form-group <?php if (!empty($error['db_user'])) { echo "has-error"; } ?>">
			<label class="control-label col-sm-4 col-xs-12">Database Username:</label>
			<div class="input-group col-sm-6 col-xs-12 afield">
				<?php echo $form['db_user']; ?>
			</div>
			<?php if (!empty($error['db_user'])) { ?>
				<span class="help-block field_err"><?php echo $error['db_user']; ?></span>
			<?php } ?>
		</div>

		<div class="form-group <?php if (!empty($error['db_password'])) { echo "has-error"; } ?>">
			<label class="control-label col-sm-4 col-xs-12">Database Password:</label>
			<div class="input-group col-sm-6 col-xs-12 afield">
				<?php echo $form['db_password']; ?>
			</div>
			<?php if (!empty($error['db_password'])) { ?>
				<span class="help-block field_err"><?php echo $error['db_password']; ?></span>
			<?php } ?>
		</div>

		<div class="form-group <?php if (!empty($error['db_name'])) { echo "has-error"; } ?>">
			<label class="control-label col-sm-4 col-xs-12">Database Name:</label>
			<div class="input-group col-sm-6 col-xs-12 afield">
				<?php echo $form['db_name']; ?>
			</div>
			<?php if (!empty($error['db_name'])) { ?>
				<span class="help-block field_err"><?php echo $error['db_name']; ?></span>
			<?php } ?>
		</div>

		<div class="form-group <?php if (!empty($error['db_prefix'])) { echo "has-error"; } ?>">
			<label class="control-label col-sm-4 col-xs-12">Table Names Prefix:</label>
			<div class="input-group col-sm-6 col-xs-12 afield">
				<?php echo $form['db_prefix']; ?>
			</div>
			<?php if (!empty($error['db_prefix'])) { ?>
				<span class="help-block field_err"><?php echo $error['db_prefix']; ?></span>
			<?php } ?>
		</div>


		<label class="h5 heading">2. Please enter a name for administrator's section. It needs to be unique alphanumeric name. Only administrators needs to know this to access control panel of the shopping cart application. Example: admin_section_2010</label>
		
		<div class="form-group <?php if (!empty($error['admin_path'])) { echo "has-error"; } ?>">
			<label class="control-label col-sm-4 col-xs-12">Admin section unique key:</label>
			<div class="input-group col-sm-6 col-xs-12 afield">
				<?php echo $form['admin_path']; ?>
			</div>
			<?php if (!empty($error['admin_path'])) { ?>
				<span class="help-block field_err"><?php echo $error['admin_path']; ?></span>
			<?php } ?>
		</div>
					
		<label class="h5 heading">3. Please enter a username and password for the administration.</label>

		<div class="form-group <?php if (!empty($error['username'])) { echo "has-error"; } ?>">
			<label class="control-label col-sm-4 col-xs-12">Admin username:</label>
			<div class="input-group col-sm-6 col-xs-12 afield">
				<?php echo $form['username']; ?>
			</div>
			<?php if (!empty($error['username'])) { ?>
				<span class="help-block field_err"><?php echo $error['username']; ?></span>
			<?php } ?>
		</div>

		<div class="form-group <?php if (!empty($error['password'])) { echo "has-error"; } ?>">
			<label class="control-label col-sm-4 col-xs-12">Admin Password:</label>
			<div class="input-group col-sm-6 col-xs-12 afield">
				<?php echo $form['password']; ?>
			</div>
			<?php if (!empty($error['password'])) { ?>
				<span class="help-block field_err"><?php echo $error['password']; ?></span>
			<?php } ?>
		</div>

		<div class="form-group <?php if (!empty($error['password_confirm'])) { echo "has-error"; } ?>">
			<label class="control-label col-sm-4 col-xs-12">Confirm Password:</label>
			<div class="input-group col-sm-6 col-xs-12 afield">
				<?php echo $form['password_confirm']; ?>
			</div>
			<?php if (!empty($error['password_confirm'])) { ?>
				<span class="help-block field_err"><?php echo $error['password_confirm']; ?></span>
			<?php } ?>
		</div>

		<div class="form-group <?php if (!empty($error['email'])) { echo "has-error"; } ?>">
			<label class="control-label col-sm-4 col-xs-12">Admin Email:</label>
			<div class="input-group col-sm-6 col-xs-12 afield">
				<?php echo $form['email']; ?>
			</div>
			<?php if (!empty($error['email'])) { ?>
				<span class="help-block field_err"><?php echo $error['email']; ?></span>
			<?php } ?>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-4 col-xs-12">Load Demo Data (recommended):</label>
			<div class="input-group col-sm-6 col-xs-12 afield">
				<input type="checkbox" id="load_demo_data" name="load_demo_data" checked="checked">
			</div>
		</div>

		</form>
				
	</div>
	
	<div class="panel-footer">
		<a class="btn btn-default" href="<?php echo $back; ?>"><i class="fa fa-arrow-left"></i> Back</a>
		<a class="btn btn-primary pull-right" onclick="document.getElementById('form').submit()">Continue <i class="fa fa-arrow-right"></i></a>
	</div>
	
	</div>
	
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">
			<h4><i class="fa fa-info-circle fa-fw"></i> AbanteCart Tips</h4>
			</div>
			<div class="panel-body">
				<h5 class="tip_heading">Completely Mobile</h5>
				<p>AbanteCart storefront and administration area support most mobile devices, tablets and computer screen resolutions</p>
				<h5 class="tip_heading">IOS and Android Integration Ready</h5>
				<p>Powered with Restfull storefront and admin API, AbanteCart is ready to be connected.</p>
				<h5 class="tip_heading">Click Updates</h5>
				<p>Our team provides AbanteCart users with update notifications and click installation of upgrades</p>
				<h5 class="tip_heading">Additional Features</h5>
				<p>Expand your ecommerce site and service with large selection of extensions available on AbanteCart marketplace. Extensions are click installed right in the administration section.</p>
				<h5 class="tip_heading">Have it customized</h5>
				<p>Our teem as well as our partners, are ready to expand your ecommerce even further.</p>
			</div>
					
		</div>
	</div>
	
</div>
<?php echo $footer; ?>