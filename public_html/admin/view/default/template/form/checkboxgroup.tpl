<div class="aform form-control acheckboxgroup">
	<?php if ($scrollbox){ ?>
	<div class="scrollbox">
		<?php } ?>
		<div class="checkbox_element">
<?php foreach($options as $v => $text){
			$check_id = preg_replace('/[^a-zA-Z0-9_]/', '', $id . $v);?>
			<label for="<?php echo $check_id ?>" class="col-sm-12">
				<div class="afield acheckbox <?php echo(in_array($v, $value) ? 'checked' : '') ?> pull-left">
		                <input id="<?php echo $check_id ?>" type="checkbox"
		                       value="<?php echo $v ?>" class="scrollbox <?php echo($style ? $style : ''); ?>"
		                       name="<?php echo $name ?>" <?php echo(in_array($v, $value) ? ' checked="checked" ' : '') ?> <?php echo $attr; ?>
		                       data-orgvalue="<?php echo(in_array($v, $value) ? 'true' : 'false') ?>" />
				</div><div class="form-inline">&nbsp;<?php echo $text ?></div>
			</label>
<?php } ?>
		</div>
<?php if ($scrollbox) { ?>
	</div>
<?php } ?>
</div>
<?php if($required == 'Y'){ ?>
	<span class="input-group-addon">
	<span class="required">*</span>
</span>
<?php } ?>

