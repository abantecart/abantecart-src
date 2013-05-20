<section id="newslettersignup">
	<div class="container-fluid">

	<div class="pull-left newsletter"><?php echo $text_signup; ?></div>
	<div class="pull-right">
		<form id="subscribeFrm" class="form-horizontal" method="" action="<?php echo $form_action; ?>">
			<div class="input-append">
				<?php foreach($form_fields as $field_name=>$field_value){?>
				<input type="hidden" name="<?php echo $field_name?>" value="<?php echo $field_value; ?>">
				<?php } ?>
				<input type="text" placeholder="<?php echo $text_subscribe; ?>" name="email" id="appendedInputButton" class="input-medium">
				<input type="submit" value="<?php echo $button_subscribe;?>" class="btn btn-orange">
			</div>
		</form>
	</div>
	</div>
</section>