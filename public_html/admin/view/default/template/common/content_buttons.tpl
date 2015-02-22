	<div class="common_content_actions pull-right">
		<?php
		if($common_content_buttons){
		$common_content_buttons = !is_array($common_content_buttons) ? array($common_content_buttons): $common_content_buttons;
			foreach($common_content_buttons as $cbb){ ?>
				<div class="btn-group"><?php echo  $cbb; ?></div>
			<?php } ?>
		<?php } ?>
		<?php echo $this->getHookVar('common_content_buttons'); ?>

		<?php if(!empty($form_store_switch)) { ?>
		<div class="btn-group">
			<?php echo $form_store_switch; ?>
		</div>
    	<?php } ?>

		<?php if (!empty($form_language_switch)) { ?>
		<div class="btn-group">
			<?php echo $form_language_switch; ?>
		</div>
		<?php } ?>
	    <?php if (!empty ($help_url)) { ?>
		<div class="btn-group">
		    	<a class="btn btn-white tooltips" href="<?php echo $help_url; ?>" target="_ext_help" data-toggle="tooltip" data-original-title="<?php echo $text_external_help; ?>">
		    		<i class="fa fa-question-circle fa-lg"></i>
		    	</a>
		</div>
	    <?php } ?>
	</div>	    