<h2><?php echo $text_form_title; ?></h2>

<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

	<?php echo $form['form_open']; ?>

		<table class="form">
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
		<?php } //foreach ($form['fields'] as $name => $field)  ?>
		</table>

	<div class="buttons align_center">
	  <button type="submit" class="btn_standard"><?php echo $form['submit']; ?></button>
	  <a class="btn_standard" href="<?php echo $cancel; ?>" ><?php echo $form['cancel']; ?></a>
    </div>
	</form>
