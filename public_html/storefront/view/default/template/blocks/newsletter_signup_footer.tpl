<div class="footer_block">
<h2><?php echo $heading_title; ?></h2>
<section id="newslettersignup">
	<div class="pull-left newsletter"><?php echo $text_signup; ?></div>
	<div class="pull-right">
		<?php echo $form_open;?>
			<div class="input-group">
				<?php foreach($form_fields as $field_name=>$field_value){?>
				<input type="hidden" name="<?php echo $field_name?>" value="<?php echo $field_value; ?>">
				<?php } ?>
				<input type="text" placeholder="<?php echo $text_subscribe; ?>" name="email" id="appendedInputButton" class="form-control">
				<span class="input-group-btn">
					<button class="btn btn-orange" type="submit"><?php echo $button_subscribe;?></button>
				</span>
			</div>
		</form>
	</div>
</section>
</div>