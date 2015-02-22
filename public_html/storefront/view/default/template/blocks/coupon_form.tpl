    <p><?php echo $text_coupon; ?></p>
	<div class="registerbox">
		<?php echo $form_open; ?>
		<div class="form-inline">
			<label class="checkbox"><?php echo $entry_coupon; ?></label>
		    <?php echo $coupon; ?>
		    <button id="apply_coupon_btn" title="<?php echo $submit->name; ?>" class="btn btn-default mr10" value="<?php echo $submit->form ?>" type="submit">
			<i class="fa fa-check"></i>
			<?php echo $submit->name; ?>
			</button>
		</div>
		</form>
	</div>