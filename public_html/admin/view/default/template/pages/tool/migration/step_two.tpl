<?php if ($error_warning) { ?>
	<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
	<div class="success alert alert-success"><?php echo $success; ?></div>
<?php }

foreach($counts as &$val){
	if($val>1000){
		$val = '<span class="required">'.$val.'</span>';
	}
} unset($val);

$form['migrate_products_text'] = nl2br(sprintf($form['migrate_products_text'], $counts['products'], $counts['categories'], $counts['manufacturers']));
$form['migrate_customers_text'] = sprintf($form['migrate_customers_text'], $counts['customers']);
?>
<div class="box">
	<div class="left"></div>
	<div class="right"></div>
	<div class="heading">
		<h1 class="icon_title_log"><?php echo $heading_title; ?></h1>
	</div>

	<div class="content">
		<?php echo $form['form_open']; ?>
		<?php if ($error_migrate_data) { ?>
			<span class="required"><?php echo $error_migrate_data; ?></span>
		<?php } ?>
		<table class="form">
			<tr>
				<td><?php echo $entry_migrate_data; ?></td>
				<td style="width: 1px;"><?php echo $form['migrate_products'] ?></td>
				<td><?php echo $form['migrate_products_text'];?></td>

			</tr>
			<tr>
				<td></td>
				<td><?php echo $form['migrate_customers'] ?></td>
				<td><?php echo $form['migrate_customers_text'] ?></td>
			</tr>
			<tr>
				<td><?php echo $entry_erase_existing_data; ?></td>
				<td><?php echo $form['erase_existing_data'] ?></td>
				<td></td>
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