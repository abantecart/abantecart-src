<div id="<?php echo $id ?>_layer" class="btn-group btn-toggle <?php echo $style; ?>" <?php echo $attr ?>>
<?php if ($checked) { ?>
<button type="button" class="btn btn-primary btn-on"><?php echo $text_on?></button>
<button type="button" class="btn btn-default"><i class="fa fa-circle-o"></i></button>
<button type="button" class="btn btn-primary btn-off hidden"><?php echo $text_off?></button>
<?php } else { ?>
<button type="button" class="btn btn-primary btn-on hidden"><?php echo $text_on?></button>
<button type="button" class="btn btn-default"><i class="fa fa-circle-o"></i></button>
<button type="button" class="btn btn-primary btn-off"><?php echo $text_off?></button>
<?php } ?>
</div>
<input type="hidden"
       name="<?php echo $name ?>"
       id="<?php echo $id ?>"
       value="<?php echo $value ?>"
       data-orgvalue="<?php echo $value ?>"
       class="aswitcher <?php echo $style; ?>"
<?php echo $attr; ?>
/>
<?php if ( !empty ($help_url) ) { ?>
<span class="input-group-addon aswitcher">
	<span class="help_element"><a href="<?php echo $help_url; ?>" target="new"><i class="fa fa-question-circle fa-lg"></i></a></span>
</span>
<?php } ?>