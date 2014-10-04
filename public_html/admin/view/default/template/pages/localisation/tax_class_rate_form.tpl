<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php if ($location_id) { ?>
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


	<div class="tab-content">
		<div class="panel-heading">
			<div class="pull-right">
				<div class="btn-group mr10 toolbar">
					<?php if (!empty ($help_url)) : ?>
						<a class="btn btn-white tooltips" href="<?php echo $help_url; ?>" target="new"
						   data-toggle="tooltip" title="" data-original-title="Help">
							<i class="fa fa-question-circle fa-lg"></i>
						</a>
					<?php endif; ?>
				</div>
				<?php echo $form_language_switch; ?>
			</div>
		</div>

		<?php echo $form['form_open']; ?>
		<div class="panel-body panel-body-nopadding">
			<?php foreach ($form['fields'] as $name => $field) {
			if ($name == 'all_zones') {
				continue;
			}
			$fld = is_array($field) ? $field[0] : $field;
			//Logic to cululate fileds width
			$widthcasses = "col-sm-7";
			if (is_int(stripos($fld->style, 'large-field'))) {
				$widthcasses = "col-sm-7";
			} else if (is_int(stripos($fld->style, 'medium-field')) || is_int(stripos($fld->style, 'date'))) {
				$widthcasses = "col-sm-5";
			} else if (is_int(stripos($fld->style, 'small-field')) || is_int(stripos($fld->style, 'btn_switch'))) {
				$widthcasses = "col-sm-3";
			} else if (is_int(stripos($fld->style, 'tiny-field'))) {
				$widthcasses = "col-sm-2";
			}
			$widthcasses .= "";
			if (in_array($name, array('rate', 'threshold_condition', 'threshold'))) {
				$widthcasses = "col-sm-3 col-xs-12";
			}
			?>
			<div id="container_<?php echo $name; ?>" class="form-group <? if (!empty($error[$name])) {
				echo "has-error";
			} ?>">
				<label class="control-label col-sm-3 col-xs-12"
					   for="<?php echo $fld->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>

				<div class="input-group afield <?php echo $widthcasses; ?> <?php echo($name == 'description' ? 'ml_ckeditor' : '') ?>">
					<?php if ($name == 'zone') { ?>
						<div class="dl-horizontal mb10">
							<?php echo $form['fields']['all_zones'] ?>
							<label for="cgFrm_all_zones"><?php echo $text_tax_all_zones; ?></label>
						</div>
					<?php
					}
					echo is_array($field) ? implode('', $field) : $field;    ?>
				</div>
				<?php if (!empty($error[$name])) { ?>
					<span class="help-block field_err"><?php echo $error[$name]; ?></span>
				<?php } ?>
			</div>
			<?php } ?><!-- <div class="fieldset"> -->
		</div>
		<div class="panel-footer">
			<div class="row">
				<div class="col-sm-6 col-sm-offset-3">
					<button class="btn btn-primary">
						<i class="fa fa-save"></i> <?php echo $form['submit']->text; ?>
					</button>
					&nbsp;
					<a class="btn btn-default" href="<?php echo $cancel; ?>">
						<i class="fa fa-refresh"></i> <?php echo $form['cancel']->text; ?>
					</a>
				</div>
			</div>
		</div>
		</form>
	</div><!-- <div class="tab-content"> -->


<?php if (isset($zone_id)) { ?>
	<script type="text/javascript"><!--

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
		//--></script>
<?php } ?>