<div class="aform form-control acheckboxgroup">
					<?php if ($scrollbox){ ?>
					<div class="scrollbox">
						<?php } ?>
						<span class="checkbox_element">
<?php foreach ($options as $v => $text) {
$check_id = preg_replace('/[^a-zA-Z0-9_]/', '', $id . $v);
?><label for="<?php echo $check_id ?>">
<div class="afield acheckbox <?php echo(in_array($v, $value) ? 'checked' : '') ?>"><span>
                <input id="<?php echo $check_id ?>" type="checkbox"
					   value="<?php echo $v ?>" <?php echo($style ? 'class="' . $style . '"' : ''); ?>
					   name="<?php echo $name ?>" <?php echo(in_array($v, $value) ? ' checked="checked" ' : '') ?> <?php echo $attr; ?>
					   data-orgvalue="<?php echo(in_array($v, $value) ? 'true' : 'false') ?>" style="opacity: 100;"/>
            </span></div><?php echo $text ?></label>
							<?php } ?>
</span>
						<?php if ($scrollbox) : ?>
					</div>
				<?php endif; ?>
</div>
<?php if ($required == 'Y') { ?>
<span class="input-group-addon">
	<span class="required">*</span>
</span>
<?php } ?>

