<?php
$img_good = '<img src="' . $template_dir . 'image/good.png" alt="Good" />';
$img_bad = '<img src="' . $template_dir . 'image/bad.png" alt="Bad" />';
echo $header; ?>
	<div class="row">
		<div class="col-md-9">
			<div class="panel panel-default">
				<ul class="nav nav-tabs" role="tablist">
					<li><a href="<?php echo $back; ?>">1: License</a></li>
					<li class="active"><a href="#" onclick="return false;">2: Compatibility Validation</a></li>
					<li class="disabled"><a href="#" onclick="return false;">3: Configuration</a></li>
					<li class="disabled"><a href="#" onclick="return false;">4: Data Load</a></li>
					<li class="disabled"><a href="#" onclick="return false;">5: Finished</a></li>
				</ul>
				<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
					<div class="panel-heading">
						<h2>
							<i class="fa fa-cogs fa-fw"></i> Compatibility Validation
							<small class="pull-right"><a onclick="document.getElementById('form').submit()"
							                             class="btn btn-primary">Continue <i
											class="fa fa-arrow-right"></i></a></small>
						</h2>
					</div>

					<div class="panel-body">

						<?php if ($error_warning){ ?>
							<div class="warning alert alert-error alert-danger"><?php echo $error_warning; ?></div>
						<?php } ?>

						<p>1. Please see if your PHP settings configured to match requirements listed below.</p>

						<div class="section">
							<table class="settings_table">
								<thead>
								<tr>
									<th>PHP Settings</th>
									<th>Current Settings</th>
									<th>Required Settings</th>
									<th>Status</th>
								</tr>
								</thead>
								<tbody>
								<tr>
									<td>PHP Version:</td>
									<td>><?php echo phpversion(); ?></td>
									<td><?php echo MIN_PHP_VERSION; ?>+</td>
									<td align="center"><?php echo !$errors['php_version'] ? $img_good : $img_bad; ?></td>
								</tr>
								<tr>
									<td>Magic Quotes GPC:</td>
									<td><?php echo (ini_get('magic_quotes_gpc')) ? 'On' : 'Off'; ?></td>
									<td>Off</td>
									<td align="center"><?php echo (!ini_get('magic_quotes_gpc')) ? $img_good : $img_bad; ?></td>
								</tr>
								<tr>
									<td>File Uploads:</td>
									<td><?php echo !$errors['file_uploads'] ? 'On' : 'Off'; ?></td>
									<td>On</td>
									<td align="center"><?php echo !$errors['file_uploads'] ? $img_good : $img_bad; ?></td>
								</tr>
								<tr>
									<td>Session Auto Start:</td>
									<td><?php echo ini_get('session.auto_start') ? 'On' : 'Off'; ?></td>
									<td>Off</td>
									<td align="center"><?php echo ini_get('session.auto_start') ? $img_bad : $img_good; ?></td>
								</tr>
								<tr>
									<td>Output Buffering</td>
									<td><?php echo (ini_get('output_buffering')) ? 'On' : 'Off'; ?></td>
									<td>On</td>
									<td align="center"><?php echo (ini_get('output_buffering')) ? $img_good : $img_bad; ?></td>
								</tr>
								</tbody>
							</table>
						</div>
						<p>2. Please make sure the extensions listed below are installed.</p>

						<div class="section">
							<table class="settings_table">
								<thead>
								<tr>
									<th>Extension</th>
									<th>Current Settings</th>
									<th>Required Settings</th>
									<th>Status</th>
								</tr>
								</thead>
								<tbody>
								<tr>
									<td>MySQL:</td>
									<td><?php echo !$errors['mysql'] ? 'On' : 'Off'; ?></td>
									<td>On</td>
									<td align="center"><?php echo !$errors['mysql'] ? $img_good : $img_bad; ?></td>
								</tr>
								<tr>
									<td>GD:</td>
									<td><?php echo !$errors['gd'] ? 'On' : 'Off'; ?></td>
									<td>On</td>
									<td align="center"><?php echo !$errors['gd'] ? $img_good : $img_bad; ?></td>
								</tr>
								<tr>
									<td>CURL:</td>
									<td><?php echo !$errors['curl'] ? 'On' : 'Off'; ?></td>
									<td>On</td>
									<td align="center"><?php echo !$errors['curl'] ? $img_good : $img_bad; ?></td>
								</tr>
								<tr>
									<td>ZIP:</td>
									<td><?php echo !$errors['zlib'] ? 'On' : 'Off'; ?></td>
									<td>On</td>
									<td align="center"><?php echo !$errors['zlib'] ? $img_good : $img_bad; ?></td>
								</tr>
								<tr>
									<td>FileInfo:</td>
									<td><?php echo !$errors['fileinfo'] ? 'On' : 'Off'; ?></td>
									<td>On</td>
									<td align="center"><?php echo !$errors['fileinfo'] ? $img_good : $img_bad; ?></td>
								</tr>
								<tr>
									<td>MultiByte String:</td>
									<td><?php echo !$errors['mbstring'] ? 'On' : 'Off'; ?></td>
									<td>On</td>
									<td align="center"><?php echo !$errors['mbstring'] ? $img_good : $img_bad; ?></td>
								</tr>
								<tr>
									<td>OpenSSL:</td>
									<td><?php echo !$errors['openssl'] ? 'On' : 'Off'; ?></td>
									<td>On</td>
									<td align="center"><?php echo !$errors['openssl'] ? $img_good : $img_bad; ?></td>
								</tr>
								<tr>
									<td>PHAR:</td>
									<td><?php echo !$errors['phar'] ? 'On' : 'Off'; ?></td>
									<td>On</td>
									<td align="center"><?php echo !$errors['phar'] ? $img_good : $img_bad; ?></td>
								</tr>
								</tbody>
							</table>
						</div>
						<p>3. Please make sure you have set the correct permissions on the files list below.</p>

						<div class="section">
							<table class="settings_table">
								<thead>
								<tr>
									<th style="width: 85%">Files</th>
									<th style="width: 15%">Status</th>
								</tr>
								</thead>
								<tbody>
								<tr>
									<td><?php echo $config_catalog; ?></td>
									<td><?php echo is_writable($config_catalog) ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
								</tr>
								<?php if (!is_writable($config_catalog)){ ?>
									<tr>
										<td colspan="2"><span
													class="bad">Change file permission to 777 or rwx-rwx-rwx:<br/> chmod 777 <?php echo $config_catalog; ?></span>
										</td>
									</tr>
								<?php } ?>
								</tbody>
							</table>
						</div>
						<p>4. Please make sure you have set the correct permissions on the directories list below.</p>

						<div class="section">
							<table class="settings_table">
								<thead>
								<tr>
									<th style="width: 85%">Directories</th>
									<th style="width: 15%">Status</th>
								</tr>
								</thead>
								<tbody>
								<tr>
									<td><?php echo $system; ?></td>
									<?php $_writable = is_writable($system) ?>
									<td><?php echo $_writable ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
								</tr>
								<?php if (!$_writable){ ?>
									<tr>
										<td colspan="2"><span class="bad">Change directory and all directories children permissions to 777 or rwx-rwx-rwx:<br/> chmod -R 777 <?php echo $system; ?></span>
										</td>
									</tr>
								<?php } else{ ?>
									<?php if (!is_writable($cache)){ ?>
										<tr>
											<td colspan="2"><span class="bad">Change directory permission to 777 or rwx-rwx-rwx:<br/> chmod 777 <?php echo $cache . '/'; ?></span>
											</td>
										</tr>
									<?php } ?>
									<?php if (!is_writable($logs)){ ?>
										<tr>
											<td colspan="2"><span class="bad">Change directory permission to 777 or rwx-rwx-rwx:<br/> chmod 777 <?php echo $logs . '/'; ?></span>
											</td>
										</tr>
									<?php }
								} ?>
								<tr>
									<td><?php echo $image . '/'; ?></td>
									<?php $_writable = is_writable($image) ?>
									<td><?php echo $_writable ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
								</tr>
								<?php if (!$_writable){ ?>
									<tr>
										<td colspan="2">
											<span class="bad">Change directory and all children directories permissions to 777 or rwx-rwx-rwx:<br/> chmod -R 777 <?php echo $image . '/'; ?></span>
										</td>
									</tr>
								<?php } else{ ?>
									<?php if (!is_writable($image_thumbnails)){ ?>
										<tr>
											<td colspan="2">
												<span class="bad">Change directory permission to 777 or rwx-rwx-rwx:<br/> chmod -R 777 <?php echo $image_thumbnails . '/'; ?></span>
											</td>
										</tr>
									<?php }
								} ?>
								<tr>
									<td><?php echo $download . '/'; ?></td>
									<td><?php echo is_writable($download) ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
								</tr>
								<?php if (!is_writable($download)){ ?>
									<tr>
										<td colspan="2">
											<span class="bad">Change directory permission to 777 or rwx-rwx-rwx:<br/> chmod 777 <?php echo $download . '/'; ?></span>
										</td>
									</tr>
								<?php } ?>
								<tr>
									<td><?php echo $extensions . '/'; ?></td>
									<td><?php echo is_writable($extensions) ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
								</tr>
								<?php if (!is_writable($extensions)){ ?>
									<tr>
										<td colspan="2">
											<span class="bad">Change directory and all children directories permission to 777 or rwx-rwx-rwx:<br/> chmod -R 777 <?php echo $extensions . '/'; ?></span>
										</td>
									</tr>
								<?php } ?>
								<tr>
									<td><?php echo $resources . '/'; ?></td>
									<td><?php echo is_writable($resources) ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
								</tr>
								<?php if (!is_writable($resources)){ ?>
									<tr>
										<td colspan="2">
											<span class="bad">Change directory and all children directories permission to 777 or rwx-rwx-rwx:<br/> chmod -R 777 <?php echo $resources . '/'; ?></span>
										</td>
									</tr>
								<?php } ?>
								<tr>
									<td><?php echo $admin_system . '/'; ?></td>
									<td><?php echo is_writable($admin_system) ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
								</tr>
								<?php if (!is_writable($admin_system)){ ?>
									<tr>
										<td colspan="2">
											<span class="bad">Change directory and all children directories permission to 777 or rwx-rwx-rwx:<br/> chmod -R 777 <?php echo $admin_system . '/'; ?></span>
										</td>
									</tr>
								<?php } ?>
								</tbody>
							</table>
						</div>
					</div>

					<div class="panel-footer">
						<a class="btn btn-default" href="<?php echo $back; ?>"><i class="fa fa-arrow-left"></i> Back</a>
						<a class="btn btn-primary pull-right" onclick="document.getElementById('form').submit()">Continue
							<i class="fa fa-arrow-right"></i></a>
					</div>

			</div>
			</form>
		</div>
		<div class="col-md-3">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4><i class="fa fa-info-circle fa-fw"></i> AbanteCart Tips</h4>
				</div>
				<div class="panel-body">
					<h5 class="tip_heading">Editing text is made easy</h5>

					<p>Edit any text in the admin with quick search and text edit feature.</p>
					<h5 class="tip_heading">Multilingual and Auto-translation</h5>

					<p>AbanteCart is multilingual and powered with automatic missing text population or translation</p>
					<h5 class="tip_heading">Quick Save</h5>

					<p>Editing is made easy with quick save feature. When change a filed quick save button will show</p>
					<h5 class="tip_heading">Smart search</h5>

					<p>Navigate administration faster with smart search locating data in all areas of application</p>
					<h5 class="tip_heading">Media Manager</h5>

					<p>Convenient interface to manage media files with resource library</p>
					<h5 class="tip_heading">Flexible Layout</h5>

					<p>Flexible and quick to edit multi-template layout manager</p>
					<h5 class="tip_heading">Advanced Import/Export</h5>

					<p>Fully featured Import/Export in CSV and XML formats</p>
				</div>
			</div>
		</div>
	</div>
<?php echo $footer; ?>