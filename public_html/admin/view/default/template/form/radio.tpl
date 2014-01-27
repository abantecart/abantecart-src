<div class="radio_element">
	<div class="aform">
		<div class="afield aradio">
			<div class="cl">
				<div class="cr">
					<div class="cc">
						<?php foreach ($options as $v => $text) {
							$radio_id = preg_replace('/[^a-zA-Z0-9\.-_]/', '', $id . $v); ?>
							<label for="<?php echo $radio_id ?>"><input id="<?php echo $radio_id ?>" <?php echo $attr ?>
																		type="radio" value="<?php echo $v ?>"
																		name="<?php echo $name ?>" <?php echo($v == $value ? ' checked="checked" ' : '') ?>><?php echo $text ?>
							</label>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php if ($required == 'Y') : ?>
	<span class="required">*</span>
<?php endif; ?>