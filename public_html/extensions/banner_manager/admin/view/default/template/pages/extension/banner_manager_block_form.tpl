<?php if ($error_warning) { ?>
<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="contentBox">
	<div class="cbox_tl">
		<div class="cbox_tr">
			<div class="cbox_tc">
				<div class="heading icon_title_blocks"><?php echo $heading_title; ?></div>
				<div class="heading-tabs">
					<?php
					foreach($tabs as $tab){
						echo '<a '.($tab['active']? 'class="active"' : '').' href="'.$tab['href'].'"><span>'.$tab['text'].'</span></a>';
					}
					?>
				</div>
				<div class="toolbar">
					<?php if (!empty ($help_url)) : ?>
					<div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img
							src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
					<?php endif; ?>
					<?php echo $form_language_switch; ?></div>
			</div>
		</div>
	</div>
	<div class="cbox_cl">
		<div class="cbox_cr">
			<div class="cbox_cc">

				<?php echo $form[ 'form_open' ]; ?>
				<div class="fieldset">
					<div class="heading"><?php echo $form_title; ?></div>
					<div class="top_left">
						<div class="top_right">
							<div class="top_mid"></div>
						</div>
					</div>
					<div class="cont_left">
						<div class="cont_right">
							<div class="cont_mid">

								<table class="form">
									<?php foreach ($form[ 'fields' ] as $name => $field) { ?>
									<tr>
										<td><?php echo $form[ 'text' ][$name]; ?></td>
										<td class="<?php echo ($name=='block_status' ? '' : 'ml_field');?>">
											<?php echo $field; ?>
											<?php if (!empty($error[ $name ])) { ?>
											<div class="field_err"><?php echo $error[ $name ]; ?></div>
											<?php } ?>
										</td>
									</tr>
									<?php }   ?>
									<tr>
										<td><?php echo $entry_list_type; ?></td>
										<td class="ml_field"><?php echo $list_type; ?></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<div class="bottom_left">
						<div class="bottom_right">
							<div class="bottom_mid"></div>
						</div>
					</div>
					<div class="sep"></div>
				</div>
				<div class="buttons align_center">
					<button type="submit" class="btn_standard"><?php echo $form[ 'submit' ]; ?></button>
					<a class="btn_standard" href="<?php echo $cancel; ?>"><?php echo $form[ 'cancel' ]; ?></a>
				</div>
				</form>

			</div>
		</div>
	</div>
	<div class="cbox_bl">
		<div class="cbox_br">
			<div class="cbox_bc"></div>
		</div>
	</div>
</div>
<?php echo $autoload;?>
