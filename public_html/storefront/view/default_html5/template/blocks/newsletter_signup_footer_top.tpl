<section id="newslettersignup">
<div class="container-fluid">
	<h2><?php echo $heading_title; ?></h2>
<div class="newsletter"><?php echo $text_signup; ?></div>
<div>
	<form id="subscribeFrm" class="form-horizontal" method="" action="<?php echo $form_action; ?>">
		<div class="input-prepend">
			<?php foreach($form_fields as $field_name=>$field_value){?>
			<input type="hidden" name="<?php echo $field_name?>" value="<?php echo $field_value; ?>">
			<?php } ?>
			<input type="text" placeholder="<?php echo $text_subscribe; ?>" name="email" id="inputIcon" class="input-medium">
			<input type="submit" value="Subscribe" class="btn btn-orange"><?php echo $text_sign_in;?>
		</div>
	</form>
</div>
</div>
</section>