<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php if ($tax_class_id) { ?>
	<ul class="nav nav-tabs nav-justified nav-profile">
		<?php
		foreach ($tabs as $tab) {
			if ($tab['active']) {
				$classname = 'active';
			} else {
				$classname = '';
			}
			?>
			<li class="<?php echo $classname; ?>"><a <?php echo($tab['href'] ? 'href="' . $tab['href'] . '" ' : ''); ?>><strong><?php echo $tab['text']; ?></strong></a>
			</li>
		<?php } ?>

		<?php echo $this->getHookVar('extension_tabs'); ?>
	</ul>
<?php } ?>

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
		</div>

		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>

	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<?php 
			$name = 'location';
			$fld = $form['fields'][$name];
		?>
		<div id="container_<?php echo $name; ?>" class="form-group <?php if (!empty($error[$name])) {
			echo "has-error";
		} ?>">
			<label class="control-label col-sm-3 col-xs-12" for="<?php echo $fld->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
			<div class="input-group afield col-sm-5">
				<?php echo $fld; ?>
			</div>
			<?php if (!empty($error[$name])) { ?>
			<span class="help-block field_err"><?php echo $error[$name]; ?></span>
			<?php } ?>
		</div>

		<?php 
			$name = 'zone';
			$fld = $form['fields'][$name];
		?>
		<div id="container_<?php echo $name; ?>" class="form-group <?php if (!empty($error[$name])) {
			echo "has-error";
		} ?>">
			<label class="control-label col-sm-3 col-xs-12" for="<?php echo $fld->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
			<div class="input-group afield col-sm-5">
				<div class="dl-horizontal checkbox">
					<label for="cgFrm_all_zones"><?php echo $form['fields']['all_zones'] ?> <?php echo $text_tax_all_zones; ?></label>							</div>
				<?php echo $fld; ?>
			</div>
			<?php if (!empty($error[$name])) { ?>
			<span class="help-block field_err"><?php echo $error[$name]; ?></span>
			<?php } ?>
		</div>
	
		<?php 
			$name = 'description';
			$fld = $form['fields'][$name];
		?>
		<div id="container_<?php echo $name; ?>" class="form-group <?php if (!empty($error[$name])) {
			echo "has-error";
		} ?>">
			<label class="control-label col-sm-3 col-xs-12" for="<?php echo $fld->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
			<div class="input-group afield col-sm-7">
				<?php echo $fld; ?>
			</div>
			<?php if (!empty($error[$name])) { ?>
			<span class="help-block field_err"><?php echo $error[$name]; ?></span>
			<?php } ?>
		</div>
	
		<?php 
			$name = 'rate';
			$fld = $form['fields'][$name];
		?>
		<div id="container_<?php echo $name; ?>" class="form-group <?php if (!empty($error[$name])) {
			echo "has-error";
		} ?>">
			<label class="control-label col-sm-3 col-xs-12" for="<?php echo $fld->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
			<div class="input-group afield col-sm-7">
				<div class="row">
				    <div class="col-sm-3">
				    	<div class="input-group afield"><?php echo $fld[0]; ?></div>
				    </div>
				    <div class="col-sm-5">
				    	<div class="input-group afield"><?php echo $fld[1]; ?></div>
				    </div>
				</div>
			</div>
			<?php if (!empty($error[$name])) { ?>
			<span class="help-block field_err"><?php echo $error[$name]; ?></span>
			<?php } ?>
		</div>

		<?php 
			$name = 'tax_rate_threshold';
			$fld = $form['fields'][$name];
		?>
		<div id="container_<?php echo $name; ?>" class="form-group <?php if (!empty($error[$name])) {
			echo "has-error";
		} ?>">
			<label class="control-label col-sm-3 col-xs-12" for="<?php echo $fld->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
			<div class="input-group afield col-sm-7">
				<div class="row">
				    <div class="col-sm-2">
				    	<div class="input-group afield"><?php echo $fld[0]; ?></div>
				    </div>
				    <div class="col-sm-5">
				    	<div class="input-group afield"><?php echo $fld[1]; ?></div>
				    </div>
				</div>
			</div>
			<?php if (!empty($error[$name])) { ?>
			<span class="help-block field_err"><?php echo $error[$name]; ?></span>
			<?php } ?>
		</div>

		<?php 
			$name = 'tax_exempt_groups';
			$fld = $form['fields'][$name];
		?>
		<div id="container_<?php echo $name; ?>" class="form-group <?php if (!empty($error[$name])) {
			echo "has-error";
		} ?>">
			<label class="control-label col-sm-3 col-xs-12" for="<?php echo $fld->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
			<div class="input-group afield col-sm-5">
				<?php echo $fld; ?>
			</div>
			<?php if (!empty($error[$name])) { ?>
			<span class="help-block field_err"><?php echo $error[$name]; ?></span>
			<?php } ?>
		</div>
	
		<?php 
			$name = 'priority';
			$fld = $form['fields'][$name];
		?>
		<div id="container_<?php echo $name; ?>" class="form-group <?php if (!empty($error[$name])) {
			echo "has-error";
		} ?>">
			<label class="control-label col-sm-3 col-xs-12" for="<?php echo $fld->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
			<div class="input-group afield col-sm-2">
				<?php echo $fld; ?>
			</div>
			<?php if (!empty($error[$name])) { ?>
			<span class="help-block field_err"><?php echo $error[$name]; ?></span>
			<?php } ?>
		</div>
	
		<?php echo $this->getHookVar('tax_class_rate_form_fields'); ?>
	
	</div>

	<div class="panel-footer col-xs-12">
		<div class="text-center">
			<button class="btn btn-primary lock-on-click">
			<i class="fa fa-save fa-fw"></i> <?php echo $form['submit']->text; ?>
			</button>
			<button class="btn btn-default" type="reset">
			<i class="fa fa-refresh fa-fw"></i> <?php echo $button_reset; ?>
			</button>
			<a class="btn btn-default" href="<?php echo $cancel; ?>">
			<i class="fa fa-arrow-left fa-fw"></i> <?php echo $form['cancel']->text; ?>
			</a>
		</div>
	</div>
	</form>
</div><!-- <div class="tab-content"> -->


<?php if (isset($zone_id)) { ?>
	<script type="text/javascript">

		jQuery(function ($) {
			var zone_id = '<?php echo $zone_id; ?>';
			var toggleZonesSelectbox = function () {
				if (!$('#cgFrm_all_zones').is(':checked')) {
					$('#cgFrm_zone_id_chosen').show();
				} else {
					$('#cgFrm_zone_id_chosen').hide();
				}
			}
			toggleZonesSelectbox();


			var getZones = function (id, location_id) {

				$.ajax(
						{
							url: '<?php echo $common_zone; ?>&location_id=' + location_id + '&zone_id=' + zone_id,
							type: 'GET',
							dataType: 'json',
							success: function (data) {
								result = data;
								showZones(id, data);
							}
						});
			}

			var showZones = function (id, data) {
				var options = '';
				$.each(data['options'], function (i, opt) {
					options += '<option value="' + i + '"';
					if (opt.selected) {
						options += 'selected="selected"';
					}
					options += '>' + opt.value + '</option>'
				});

				$('#' + id).html(options).trigger("chosen:updated");
			}

			getZones('cgFrm_zone_id', $('#cgFrm_location_id').val());


			$('#cgFrm_location_id').change(function () {
				getZones('cgFrm_zone_id', $(this).val());
				$('#cgFrm_zone_id').val('').change();

			});

			$('#cgFrm_all_zones').click(function () {
				toggleZonesSelectbox();
			});

		});
		</script>
<?php } ?>