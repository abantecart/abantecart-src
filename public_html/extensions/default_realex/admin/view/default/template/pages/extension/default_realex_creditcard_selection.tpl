<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover">
	  <thead>
	    <tr>
	      <td class="text-left"><?php echo $text_card_type; ?></td>
	      <td class="text-center"><?php echo $text_enabled; ?></td>
	      <td class="text-center"><?php echo $text_default; ?></td>
	      <td class="text-left"><?php echo $text_subaccount; ?></td>
	    </tr>
	  </thead>
	  <tbody>
	  <?php foreach($options as $type => $text_key) {
	  	$enabled = '';
	  	if ($value[$type]['enabled']) {
	  		$enabled = 'true';
	  	}
	  	$default = '';
	  	if ($value[$type]['default']) {
	  		$default = 'true';
	  	}
	  ?>
	  <tr>
	      <td class="text-left"><?php echo $text_key; ?></td>
	      <td class="text-center"><input type="checkbox" data-orgvalue="<?php echo $enabled; ?>" value="<?php echo $value[$type]['enabled']; ?>" name="default_realex_creditcard_selection[<?php echo $type; ?>][enabled]" <?php if($enabled) { ?>checked<?php } ?>></td>
	      <td class="text-center"><input type="checkbox" data-orgvalue="<?php echo $default; ?>" value="<?php echo $value[$type]['default']; ?>" name="default_realex_creditcard_selection[<?php echo $type; ?>][default]" <?php if($default) { ?>checked<?php } ?>></td>
	      <td class="text-right"><input type="text" class="form-control" placeholder="<?php echo $text_subaccount; ?>" data-orgvalue="<?php echo $value[$type]['']; ?>" value="<?php echo $value[$type]['subaccount']; ?>" name="default_realex_creditcard_selection[<?php echo $type; ?>][subaccount]"></td>
	  </tr>
	  <?php } ?>
	    </tr>
	  </tbody>
	</table>
</div>