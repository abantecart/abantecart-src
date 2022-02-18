    <p><?php echo $text_coupon; ?></p>
	<div class="registerbox">
		<?php echo $form_open; ?>
		<div class="form-inline">
			<label class="checkbox"><?php echo $entry_coupon; ?></label>
		    <?php echo $coupon; ?>
			<?php if($coupon_code) { ?>
				<a href="#" id="remove_coupon_btn" title="<?php echo $remove; ?>" class="btn btn-primary" value="" type="submit">
					<i class="fa fa-remove"></i>
				</a>
			<?php } ?>
		    <button id="apply_coupon_btn" title="<?php echo $submit->name; ?>" class="btn btn-default mr10" value="<?php echo $submit->form ?>" type="submit">
			<i class="fa fa-check"></i>
			<?php echo $submit->name; ?>
			</button>
		</div>
		</form>
	</div>
	<script type="text/javascript">

		jQuery(function ($) {
			//reset coupon
			$('.registerbox').on('click', '#remove_coupon_btn', function () {
				var $form = $("#coupon_coupon").closest('form');
				$("#coupon_coupon").val('');
				$form.append('<input type="hidden" name="reset_coupon" value="true" />');
				$form.submit();
				return false;
			});
		});

	</script>