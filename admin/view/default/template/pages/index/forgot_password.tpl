<div class="contentBox loginBox">
	<div class="cbox_tl">
		<div class="cbox_tr">
			<div class="cbox_tc">
				<div class="heading icon_lockscreen"><?php echo $text_heading; ?></div>
			</div>
		</div>
	</div>
	<div class="cbox_cl">
		<div class="cbox_cr">
			<div class="cbox_cc">

				<?php if (!empty($error['warning'])) { ?>
				<div class="warning" style="padding: 3px;"><?php echo $error['warning']; ?></div>
				<?php } ?>

				<?php if ($show_instructions) {
					echo $text_instructions;
					echo '<br/><br/><a href="'. $login .'">'. $text_login .'</a>';
				} else { ?>
				<?php echo $form['form_open']; ?>
					<table cellspacing="0" cellpadding="8" border="0" width="100%">
						<?php foreach ($form['fields'] as $name => $field) { ?>
						<tr>
							<td><?php echo ${'entry_'.$name}; ?></td>
							<td>
								<?php echo $field; ?>
								<?php if (!empty($error[$name])) { ?>
									<div class="field_err"><?php echo $error[$name]; ?></div>
								<?php } ?>
							</td>
						</tr>
						<?php } //foreach ($form['fields'] as $name => $field) ?>
						<tr>
							<td><a href="<?php echo $login ?>"><?php echo $text_login ?></a></td>
							<td align="right">
								<button type="submit" class="btn_standard"><?php echo $form['submit']; ?></button>
							</td>
						</tr>
					</table>
				</form>
				<?php } ?>
				
			</div>
		</div>
	</div>
	<div class="cbox_bl">
		<div class="cbox_br">
			<div class="cbox_bc"></div>
		</div>
	</div>
</div>