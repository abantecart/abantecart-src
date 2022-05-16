<section id="newslettersignup" class="mt-3 d-flex flex-wrap">
<h2><?php echo $heading_title; ?></h2>
	<label class="newsletter mb-3"><?php echo $text_signup; ?></label>
	<div class="pull-right">
		<?php echo $form_open;?>
			<div class="input-group">
				<?php foreach($form_fields as $field_name=>$field_value){?>
				<input type="hidden" name="<?php echo $field_name?>" value="<?php echo $field_value; ?>">
				<?php } ?>
				<input type="email" placeholder="<?php echo $text_subscribe; ?>" name="email" id="appendedInputButton" class="form-control">
				<button class="btn btn-primary" type="submit"><?php echo $button_subscribe;?></button>
			</div>
		</form>
	</div>
</section>
