<?php if ($error_warning) { ?>
	<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
	<div class="success"><?php echo $success; ?></div>
<?php } ?>
<div class="box">
	<div class="left"></div>
	<div class="right"></div>
	<div class="heading">
		<h1 class="icon_title_log"><?php echo $heading_title; ?></h1>
	</div>

	<div class="content">
		<?php echo $form['form_open']; ?>
		<table class="form">
			<tr>
				<td><?php echo $entry_migrate_data; ?></td>
				<td>
					<?php echo $form['migrate_products'] ?><br/>
					<?php echo $form['migrate_customers'] ?><br/>
					<br/>
					<?php if ($error_migrate_data) { ?>
						<span class="required"><?php echo $error_migrate_data; ?></span>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td><?php echo $entry_erase_existing_data; ?></td>
				<td><?php echo $form['erase_existing_data'] ?></td>
			</tr>
		</table>
		<div class="buttons align_center">
			<a onclick="location = '<?php echo $back; ?>';" class="btn_standard"
			   href="<?php echo $back; ?>"><?php echo $form['button_back']; ?></a>
			<button type="submit" class="btn_standard"><?php echo $form['button_continue']; ?></button>
			<a onclick="location = '<?php echo $cancel; ?>';" class="btn_standard"
			   href="<?php echo $cancel; ?>"><?php echo $form['button_cancel']; ?></a>
		</div>
		</form>
	</div>
</div>