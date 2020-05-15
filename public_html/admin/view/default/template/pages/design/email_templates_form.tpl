<style>
	.btn.btn-primary.tooltips.add_media {
		display: none;
	}
</style>
<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div id="content" class="panel panel-default">
	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<?php if (!empty ($list_url)) { ?>
			<div class="btn-group">
				<a class="btn btn-white tooltips" href="<?php echo $list_url; ?>" data-toggle="tooltip" data-original-title="<?php echo $text_back_to_list; ?>">
					<i class="fa fa-arrow-left fa-lg"></i>
				</a>
			</div>
			<?php } ?>

			<?php if ($insert){ ?>
				<div class="btn-group mr10 toolbar">
					<a class="actionitem btn btn-primary lock-on-click tooltips" href="<?php echo $insert; ?>"
					   title="<?php echo $button_add; ?>">
						<i class="fa fa-plus fa-fw"></i>
					</a>
				</div>
			<?php } ?>
		</div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
	</div>

	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<label class="h4 heading"><?php echo $form_title; ?></label>
		<?php foreach ($form['fields'] as $name => $field) {
		//Logic to calculate fields width
		$widthcasses = "col-sm-7";
		if (is_int(stripos($field->style, 'large-field'))){
			$widthcasses = "col-sm-7";
		} else if (is_int(stripos($field->style, 'medium-field')) || is_int(stripos($field->style, 'date'))){
			$widthcasses = "col-sm-5";
		} else if (is_int(stripos($field->style, 'small-field')) || is_int(stripos($field->style, 'btn_switch'))){
			$widthcasses = "col-sm-3";
		} else if (is_int(stripos($field->style, 'tiny-field'))){
			$widthcasses = "col-sm-2";
		}
		$widthcasses .= " col-xs-12";
		?>
		<div class="form-group row align-items-start <?php if (!empty($error[$name])){
			echo "has-error";
		} ?>">
			<label class="control-label offset-sm-1 col-sm-3 col-xs-12"
			       for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>

			<div id="field_<?php echo $name; ?>"
			     class="input-group afield <?php echo $widthcasses; ?> <?php echo($name == 'content' ? 'ml_ckeditor' : '') ?>">
				<?php if ($name == 'keyword'){ ?>
					<span class="input-group-prepend">
					<?php echo $keyword_button; ?>
				</span>
				<?php } ?>
				<?php
				if ($name == 'sort_order'){ ?>
					<ul class="list-unstyled">
						<?php
						foreach ($field as $s){ ?>
							<li class="col-sm-12 col-xs-12">
								<div class="row">
									<label class="col-sm-3 control-label"><?php echo $s['label']; ?>:</label>

									<div class="col-sm-3"><?php echo $s['field'] ?></div>
								</div>
							</li>
						<?php } ?>
					</ul>
					<?php
				} else{
					echo $field;
				}
				?>
			</div>
			<?php if (!empty($error[$name])){ ?>
				<span class="help-block field_err"><?php echo $error[$name]; ?></span>
			<?php } ?>
		</div>
		<?php } ?><!-- <div class="fieldset"> -->
	</div>

	<div class="panel-footer col-xs-12">
		<div class="text-center">
			<button class="btn btn-primary lock-on-click">
				<i class="fa fa-save fa-fw"></i> <?php echo $form['submit']->text; ?>
			</button>
			<a class="btn btn-default" href="<?php echo $cancel; ?>">
				<i class="fa fa-arrow-left fa-fw"></i> <?php echo $form['cancel']->text; ?>
			</a>
			<?php if($form['show_on_storefront']){ ?>
			<a class="btn btn-info" target="_blank"
			href="<?php echo $form['show_on_storefront']->href; ?>">
			<i class="fa fa-external-link-alt"></i> <?php echo $form['show_on_storefront']->text; ?>
			</a>
			<?php } ?>
		</div>
	</div>
	</form>

</div><!-- <div class="tab-content"> -->

