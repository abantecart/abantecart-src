<div class="btn-group btn-toggle <?php echo $style; ?>" <?php echo $attr ?>> 
	<?php if ($checked) { ?>
    <button class="btn btn-primary active">ON</button>
    <button class="btn btn-default">OFF</button>	
	<?php } else { ?>
    <button class="btn btn-default">ON</button>
    <button class="btn btn-primary btn-off active">OFF</button>		
	<?php } ?>
</div>
<input type="hidden"
           name="<?php echo $name ?>"
           id="<?php echo $id ?>"
           value="<?php echo $value ?>" 
           data-orgvalue="<?php echo $value ?>"
           class="aswitcher"
/>
<?php if ( !empty ($help_url) ) { ?>
<span class="input-group-addon aswitcher">
	<span class="help_element"><a href="<?php echo $help_url; ?>" target="new"><i class="fa fa-question-circle fa-lg fa-lg"></i></a></span>
</span>
<?php } ?>