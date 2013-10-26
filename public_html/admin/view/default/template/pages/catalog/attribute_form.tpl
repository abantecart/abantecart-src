<?php if (!empty($error['warning'])) { ?>
	<div class="warning alert alert-error"><?php echo $error['warning']; ?></div>
<?php } ?>
<?php if ($success) { ?>
	<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>


<div class="contentBox">
	<div class="cbox_tl">
		<div class="cbox_tr">
			<div class="cbox_tc">
				<div class="heading icon_title_length"><?php echo $heading_title; ?></div>
				<div class="heading-tabs">
					<?php
					foreach ($tabs as $tab) {
						$css_class = $tab['active'] ? 'active' : '';
						if ($attribute_id && !$tab['active']) {
							$css_class = 'inactive';
						}
						echo '<a ' . ($tab['href'] ? 'href="' . $tab['href'] . '" ' : '') . ' class="' . $css_class . '" ' . '><span>' . $tab['text'] . '</span></a>';
					} ?>
				</div>
				<div class="toolbar">
					<?php if (!empty ($help_url)) { ?>
						<div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img
										src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
					<?php }
					echo $form_language_switch; ?>
					<div class="buttons">
						<a class="btn_toolbar" title="<?php echo $button_insert; ?>" href="<?php echo $insert; ?>">
							<span class="icon_add">&nbsp;</span>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="cbox_cl">
		<div class="cbox_cr">
			<div class="cbox_cc">

				<?php echo $form['form_open']; ?>
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
									<?php foreach ($form['fields'] as $name => $field) { ?>
									<tr>
										<td><?php echo ${'entry_' . $name}; ?></td>
										<td>
											<?php if (!is_array($field)) echo $field; ?>
											<?php if (!empty($error[$name])) { ?>
												<div class="field_err"><?php echo $error[$name]; ?></div>
											<?php } ?>
											<?php if ($name == 'element_type') { ?>

											<div id="values">
												<?php if ($child_count == 0): ?>
													<div style="padding-left: 40px;">
														<span style="padding-right: 35px;"><b><?php echo $entry_element_values; ?></b></span>
														<span><b><?php echo $column_sort_order; ?></b></span>
													</div>
													<?php foreach ($form['fields']['attribute_values'] as $atr_val_id => $atr_field): ?>
														<div class="value">
															<?php echo $atr_field['attribute_value_ids']; ?>
															<?php echo $atr_field['values']; ?>&nbsp;
															<?php echo $atr_field['sort_order']; ?>
															<a class="remove"></a>
														</div>
													<?php endforeach; ?>
													<a class="add"></a>
												<?php else: ?>
													<div style="padding-left: 10px;">

														<span><b><?php echo $entry_children_attributes; ?></b></span>

														<?php foreach ($children as $child): ?>
															<div class="value">
																<a href="<?php echo $child['link']; ?>"><?php echo $child['name']; ?></a>
															</div>
														<?php endforeach; ?>

													</div>
												<?php endif; ?>
											</div>

											<div id="file_settings">
												<div class="value">
													<span style="padding-right: 20px;"><?php echo $entry_allowed_extensions; ?></span>
													<?php echo $form['settings_fields']['extensions']; ?>
												</div>
												<div class="value">
													<span style="padding-right: 20px;"><?php echo $entry_min_size; ?></span>
													<?php echo $form['settings_fields']['min_size']; ?>
												</div>
												<div class="value">
													<span style="padding-right: 20px;"><?php echo $entry_max_size; ?></span>
													<?php echo $form['settings_fields']['max_size']; ?>
												</div>
												<div class="value">
													<span style="padding-right: 20px;"><?php echo $entry_upload_dir; ?></span>
													<?php echo $form['settings_fields']['directory']; ?>
												</div>
											</div>
							</div>

							<?php } ?>
							</td>
							</tr>
							<?php if ($name == 'attribute_parent' && $text_parent_note) { ?>
								<tr>
									<td colspan="2"><?php echo $text_parent_note; ?></td>
								</tr>
							<?php } ?>

							<?php } //foreach ?>


							</table>
							<?php echo $subform; ?>
						</div>
					</div>
				</div>
				<div class="bottom_left">
					<div class="bottom_right">
						<div class="bottom_mid"></div>
					</div>
				</div>
			</div>
			<!-- <div class="fieldset"> -->
			<div class="buttons align_center">
				<a class="btn_standard" href="<?php echo $cancel; ?>"><?php echo $form['cancel']; ?></a>
				<button type="submit" class="btn_standard"><?php echo $form['submit']; ?></button>
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
