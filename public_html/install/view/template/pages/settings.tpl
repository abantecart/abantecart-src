<?php echo $header; ?>
<div id="stepbar">
	<div class="tl">
		<div class="tr">
			<div class="tc"></div>
		</div>
	</div>
	<div class="cl">
		<div class="cr">
			<div class="cc">
				<div class="heading">Installation Steps:</div>
				<div class="step">1: License</div>
				<div class="step_current">2: Compatibility Check</div>
				<div class="step">3: Configuration</div>
				<div class="step">4: Data Load</div>
				<div class="step">5: Finished</div>
			</div>
		</div>
	</div>
	<div class="bl">
		<div class="br">
			<div class="bc"></div>
		</div>
	</div>
</div>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div class="main_content">
	<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
		<div class="contentBox">
			<div class="cbox_tl">
				<div class="cbox_tr">
					<div class="cbox_tc">
						<div class="heading">
							Compatibility Validation
							<div class="buttons"><a onclick="document.getElementById('form').submit()"
													class="btn_standard"><?php echo $button_continue; ?></a></div>
						</div>
					</div>
				</div>
			</div>
			<div class="cbox_cl">
				<div class="cbox_cr">
					<div class="cbox_cc">

						<p>1. Please see if your PHP settings configured to match requirements listed below.</p>

						<div class="section">
							<table width="100%">
								<tr>
									<th width="35%" align="left"><b>PHP Settings</b></th>
									<th width="25%" align="left"><b>Current Settings</b></th>
									<th width="25%" align="left"><b>Required Settings</b></th>
									<th width="15%" align="center"><b>Status</b></th>
								</tr>
								<tr>
									<td>PHP Version:</td>
									<td>><?php echo phpversion(); ?></td>
									<td>5.2+</td>
									<td align="center"><?php  echo (phpversion() >= '5.0') ? '<img src="' . $template_dir . 'image/good.png" alt="Good" />' : '<img src="' . $template_dir . '"image/bad.png" alt="Bad" />'; ?></td>
								</tr>
								<tr>
									<td>Register Globals:</td>
									<td><?php echo (ini_get('register_globals')) ? 'On' : 'Off'; ?></td>
									<td>Off</td>
									<td align="center"><?php echo (!ini_get('register_globals')) ? '<img src="' . $template_dir . 'image/good.png" alt="Good" />' : '<img src="' . $template_dir . 'image/bad.png" alt="Bad" />'; ?></td>
								</tr>
								<tr>
									<td>Magic Quotes GPC:</td>
									<td><?php echo (ini_get('magic_quotes_gpc')) ? 'On' : 'Off'; ?></td>
									<td>Off</td>
									<td align="center"><?php echo (!ini_get('magic_quotes_gpc')) ? '<img src="' . $template_dir . 'image/good.png" alt="Good" />' : '<img src="' . $template_dir . 'image/bad.png" alt="Bad" />'; ?></td>
								</tr>
								<tr>
									<td>File Uploads:</td>
									<td><?php echo (ini_get('file_uploads')) ? 'On' : 'Off'; ?></td>
									<td>On</td>
									<td align="center"><?php echo (ini_get('file_uploads')) ? '<img src="' . $template_dir . 'image/good.png" alt="Good" />' : '<img src="' . $template_dir . 'image/bad.png" alt="Bad" />'; ?></td>
								</tr>
								<tr>
									<td>Session Auto Start:</td>
									<td><?php echo (ini_get('session_auto_start')) ? 'On' : 'Off'; ?></td>
									<td>Off</td>
									<td align="center"><?php echo (!ini_get('session_auto_start')) ? '<img src="' . $template_dir . 'image/good.png" alt="Good" />' : '<img src="' . $template_dir . 'image/bad.png" alt="Bad" />'; ?></td>
								</tr>
							</table>
						</div>
						<p>2. Please make sure the extensions listed below are installed.</p>

						<div class="section">
							<table width="100%">
								<tr>
									<th width="35%" align="left"><b>Extension</b></th>
									<th width="25%" align="left"><b>Current Settings</b></th>
									<th width="25%" align="left"><b>Required Settings</b></th>
									<th width="15%" align="center"><b>Status</b></th>
								</tr>
								<tr>
									<td>MySQL:</td>
									<td><?php echo extension_loaded('mysql') ? 'On' : 'Off'; ?></td>
									<td>On</td>
									<td align="center"><?php echo extension_loaded('mysql') ? '<img src="' . $template_dir . 'image/good.png" alt="Good" />' : '<img src="' . $template_dir . 'image/bad.png" alt="Bad" />'; ?></td>
								</tr>
								<tr>
									<td>GD:</td>
									<td><?php echo extension_loaded('gd') ? 'On' : 'Off'; ?></td>
									<td>On</td>
									<td align="center"><?php echo extension_loaded('gd') ? '<img src="' . $template_dir . 'image/good.png" alt="Good" />' : '<img src="' . $template_dir . 'image/bad.png" alt="Bad" />'; ?></td>
								</tr>
								<tr>
									<td>CURL:</td>
									<td><?php echo extension_loaded('curl') ? 'On' : 'Off'; ?></td>
									<td>On</td>
									<td align="center"><?php echo extension_loaded('curl') ? '<img src="' . $template_dir . 'image/good.png" alt="Good" />' : '<img src="' . $template_dir . 'image/bad.png" alt="Bad" />'; ?></td>
								</tr>
								<tr>
									<td>ZIP:</td>
									<td><?php echo extension_loaded('zlib') ? 'On' : 'Off'; ?></td>
									<td>On</td>
									<td align="center"><?php echo extension_loaded('zlib') ? '<img src="' . $template_dir . 'image/good.png" alt="Good" />' : '<img src="' . $template_dir . 'image/bad.png" alt="Bad" />'; ?></td>
								</tr>
								<tr>
									<td>MultiByte String:</td>
									<td><?php echo extension_loaded('mbstring') ? 'On' : 'Off'; ?></td>
									<td>On</td>
									<td align="center"><?php echo extension_loaded('mbstring') ? '<img src="' . $template_dir . 'image/good.png" alt="Good" />' : '<img src="' . $template_dir . 'image/bad.png" alt="Bad" />'; ?></td>
								</tr>
							</table>
						</div>
						<p>3. Please make sure you have set the correct permissions on the files list below.</p>

						<div class="section">
							<table width="100%">
								<tr>
									<th align="left"><b>Files</b></th>
									<th width="15%" align="left"><b>Status</b></th>
								</tr>
								<tr>
									<td><?php echo $config_catalog; ?></td>
									<td><?php echo is_writable($config_catalog) ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
								</tr>
								<?php if (!is_writable($config_catalog)) { ?>
								<tr>
									<td colspan="2"><span class="bad">Change file permission to 777 or rwx-rwx-rwx:<br/> chmod 777 <?php echo $config_catalog; ?></span>
									</td>
								</tr>
								<?php } ?>
							</table>
						</div>
						<p>4. Please make sure you have set the correct permissions on the directories list below.</p>

						<div class="section">
							<table width="100%">
								<tr>
									<th align="left"><b>Directories</b></th>
									<th width="15%" align="left"><b>Status</b></th>
								</tr>
								<tr>
									<td><?php echo $system; ?></td>
									<?php $_writable = is_writable($system) ?>
									<td><?php echo $_writable ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
								</tr>
								<?php if (!$_writable) { ?>
								<tr>
									<td colspan="2"><span class="bad">Change directory and all directories children permissions to 777 or rwx-rwx-rwx:<br/> chmod -R 777 <?php echo $system; ?></span>
									</td>
								</tr>
								<?php } else { ?>
								<?php if (!is_writable($cache)) { ?>
									<tr>
										<td colspan="2"><span class="bad">Change directory permission to 777 or rwx-rwx-rwx:<br/> chmod 777 <?php echo $cache . '/'; ?></span>
										</td>
									</tr>
									<?php } ?>
								<?php if (!is_writable($logs)) { ?>
									<tr>
										<td colspan="2"><span class="bad">Change directory permission to 777 or rwx-rwx-rwx:<br/> chmod 777 <?php echo $logs . '/'; ?></span>
										</td>
									</tr>
									<?php }
							}?>
								<tr>
									<td><?php echo $image . '/'; ?></td>
									<?php $_writable = is_writable($image) ?>
									<td><?php echo $_writable ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
								</tr>
								<?php if (!$_writable) { ?>
								<tr>
									<td colspan="2"><span class="bad">Change directory and all children directories permissions to 777 or rwx-rwx-rwx:<br/> chmod -R 777 <?php echo $image . '/'; ?></span>
									</td>
								</tr>
								<?php } else { ?>
								<?php if (!is_writable($image_thumbnails)) { ?>
									<tr>
										<td colspan="2"><span class="bad">Change directory permission to 777 or rwx-rwx-rwx:<br/> chmod -R 777 <?php echo $image_thumbnails . '/'; ?></span>
										</td>
									</tr>
									<?php }
							}?>
								<tr>
									<td><?php echo $download . '/'; ?></td>
									<td><?php echo is_writable($download) ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
								</tr>
								<?php if (!is_writable($download)) { ?>
								<tr>
									<td colspan="2"><span class="bad">Change directory permission to 777 or rwx-rwx-rwx:<br/> chmod 777 <?php echo $download . '/'; ?></span>
									</td>
								</tr>
								<?php } ?>
								<tr>
									<td><?php echo $extensions . '/'; ?></td>
									<td><?php echo is_writable($extensions) ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
								</tr>
								<?php if (!is_writable($extensions)) { ?>
								<tr>
									<td colspan="2"><span class="bad">Change directory and all children directories permission to 777 or rwx-rwx-rwx:<br/> chmod -R 777 <?php echo $extensions . '/'; ?></span>
									</td>
								</tr>
								<?php } ?>
								<tr>
									<td><?php echo $resources . '/'; ?></td>
									<td><?php echo is_writable($resources) ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
								</tr>
								<?php if (!is_writable($resources)) { ?>
								<tr>
									<td colspan="2"><span class="bad">Change directory and all children directories permission to 777 or rwx-rwx-rwx:<br/> chmod -R 777 <?php echo $resources . '/'; ?></span>
									</td>
								</tr>
								<?php } ?>
								<tr>
									<td><?php echo $backup . '/'; ?></td>
									<td><?php echo is_writable($backup) ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
								</tr>
								<?php if (!is_writable($backup)) { ?>
								<tr>
									<td colspan="2"><span class="bad">Change directory and all children directories permission to 777 or rwx-rwx-rwx:<br/> chmod -R 777 <?php echo $backup . '/'; ?></span>
									</td>
								</tr>
								<?php } ?>
							</table>
						</div>

						<div class="align_right"><a onclick="document.getElementById('form').submit()"
													class="btn_standard"><?php echo $button_continue; ?></a></div>

					</div>
				</div>
			</div>
			<div class="cbox_bl">
				<div class="cbox_br">
					<div class="cbox_bc"></div>
				</div>
			</div>
		</div>
	</form>
</div>
<?php echo $footer; ?>