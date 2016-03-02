<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>
<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group mr10 toolbar">
                <a class="btn btn-white tooltips"
                   href="<?php echo $edit_profile_url; ?>"
                   data-toggle="tooltip"
                   title="<?php echo $text_edit_details; ?>" data-original-title="<?php echo $text_edit_details; ?>"
		        ><i class="fa fa-user"></i></a>
		    </div>
		</div>

		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>


	<div class="panel-body panel-body-nopadding tab-content col-xs-12 form-horizontal">

		<label class="h4 heading"><?php echo $text_sendpoints; ?></label>


			<?php foreach ($sendpoints as $point) {

				//Logic to calculate fields width
				$widthcasses = "col-sm-7";
				$widthcasses .= " col-xs-12";
			?>
			<div class="form-group">
				<label class="control-label col-sm-4 col-xs-12" ><?php echo $point['text']; ?></label>
				<div class="input-group afield <?php echo $widthcasses; ?> ">
					<div class="col-sm-3 text-center"><p class="form-control-static"><?php echo implode(', ', $point['values']); ?></p></div>
					<div class="col-sm-3">
						<a href="<?php echo $im_settings_url . '&section=' . $section . '&sendpoint=' . $point['id']; ?>"
						   data-toggle="modal"
						   data-target="#im_settings_modal"
						   title="<?php echo $text_change_im_addresses; ?>"
						   class="btn btn-default tooltips"><i class="fa fa-gears"></i></a>
					</div>
				</div>
			    <?php if (!empty($error[$name])) { ?>
			    <span class="help-block field_err"><?php echo $error[$name]; ?></span>
			    <?php } ?>
			</div>
			<?php }  ?><!-- <div class="fieldset"> -->
	</div>


</div><!-- <div class="tab-content"> -->
<?php
echo $this->html->buildElement(
		array (
				'type'        => 'modal',
				'id'          => 'im_settings_modal',
				'modal_type'  => 'lg',
				'data_source' => 'ajax',
				'js_onclose'  => 'on_modal_close();'));
?>