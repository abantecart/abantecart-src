<?php foreach ($options as $v => $text) {
    $radio_id = preg_replace('/[^a-zA-Z0-9\.-_]/', '', $id . $v); ?>
<div class="radio">
    <label for="<?php echo $radio_id ?>">
    	<input id="<?php echo $radio_id ?>" type="radio" value="<?php echo $v ?>" name="<?php echo $name ?>" <?php echo($v == $value ? ' checked="checked" ' : '') ?> data-orgvalue="<?php echo $value; ?>" <?php echo $attr ?>><?php echo $text ?>
    </label>
</div>
<?php } ?>

<?php if ( $required == 'Y' || !empty ($help_url) ) { ?>
	<span class="input-group-addon">
	<?php if ( $required == 'Y') { ?> 
		<span class="required">*</span>
	<?php } ?>	

	<?php if ( !empty ($help_url) ) { ?>
	<span class="help_element"><a href="<?php echo $help_url; ?>" target="new"><i class="fa fa-question-circle fa-lg"></i></a></span>
	<?php } ?>	
	</span>
<?php } ?>