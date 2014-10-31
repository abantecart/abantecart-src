<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php
echo $extension_summary;
echo $tabs; ?>
<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
	</div>

<?php foreach ($sections as $section) {
	echo $section['form']['form_open'];
?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<label class="h4 heading" id="<?php echo $section['id'];?>"><?php echo $section['name']; ?></label>

		<?php if ($section['section_id'] == 'enc_usage') { ?>
			<?php
			$unc_count = 0;
			foreach ($unencrypted_stats as $unc_stats) {
				$unc_count += $unc_stats['count'];
			} ?>
			<?php if ($unc_count > 0) { ?>
                <h4><?php echo $text_unencrepted_records ?></h4>
				<div class="alert alert-warning">
				<table class="list" width="80%">
                <tr>
                    <td width="300">
                    <ul>
                    <?php $unc_count = 0;
                        foreach ($unencrypted_stats as $unc_stats) { ?>
                        <li><?php echo $unc_stats['table']; ?> : <?php echo $unc_stats['count']; ?> <?php echo $text_usage_records; ?></li>
                    <?php $unc_count += $unc_stats['count'];
                         } ?>
                    </ul>
                    </td>
                    <td width="300">
	                <?php echo $warn_encrypt_open_data; ?>
					</td>
				</tr>
				</table>
				</div>
			<?php } ?>
			<?php if (count ($section['usage_details']) > 0) { ?>

				<h4><?php echo $text_encrepted_records ?></h4>
				<div class="alert alert-warning">
                <table class="list" width="80%">
                <tr>
                    <th><?php echo $text_usage_heading_key_id; ?></th>
                    <th><?php echo $text_usage_heading_key_name; ?></th>
                    <th><?php echo $text_usage_heading_key_tables; ?></th>
                    <th><?php echo $text_usage_heading_key_rotate; ?></th>
                </tr>
                <?php foreach ($section['usage_details'] as $usage) { ?>
                <tr>
                    <td width="60"><?php echo $usage['key_id']; ?></td>
                    <td width="120"><?php echo $usage['key_name']; ?></td>
                    <td width="400">
                    <ul>
                    <?php $enc_count = 0;
                        foreach ($usage['key_usage'] as $enc_stats) { ?>
                        <li><?php echo $enc_stats['table']; ?> : <?php echo $enc_stats['count']; ?> <?php echo $text_usage_records; ?></li>
                    <?php $enc_count += $enc_stats['count'];
                         } ?>
                    </ul>
					</td>
                    <td><?php if($enc_count > 0) { echo $usage['actons']; } ?></td>
                </tr>
                <?php } ?>
                </table></div>
                <?php } // endof enc_usage section ?>

		<?php
		}else{

		foreach ($section['form']['fields'] as $name => $field) {
		//Logic to calculate fields width
		$widthcasses = "col-sm-7";
		if (is_int(stripos($field->style, 'large-field'))) {
			$widthcasses = "col-sm-7";
		} else if (is_int(stripos($field->style, 'medium-field')) || is_int(stripos($field->style, 'date'))) {
			$widthcasses = "col-sm-5";
		} else if (is_int(stripos($field->style, 'small-field')) || is_int(stripos($field->style, 'btn_switch'))) {
			$widthcasses = "col-sm-3";
		} else if (is_int(stripos($field->style, 'tiny-field'))) {
			$widthcasses = "col-sm-2";
		}
		$widthcasses .= " col-xs-12";
		?>
		<div class="form-group <?php if (!empty($error[$name])) {
			echo "has-error";
		} ?>">
			<label class="control-label col-sm-3 col-xs-12"
			       for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>

			<div class="input-group afield <?php echo $widthcasses; ?> <?php echo($name == 'description' ? 'ml_ckeditor' : '') ?>">
				<?php echo $field; ?>
			</div>
			<?php if (!empty($error[$name])) { ?>
				<span class="help-block field_err"><?php echo $error[$name]; ?></span>
			<?php } ?>
		</div>
		<?php
			}
		}?><!-- <div class="fieldset"> -->


	</div>

	<?php if ($section['note']) { ?>
		<div class="alert alert-warning"><i class="fa fa-info-circle fa-fw"></i> <?php echo $section['note']; ?></div>
	<?php } ?>

	<div class="panel-footer col-xs-12">
		<div class="text-center">
			<button class="btn btn-primary lock-on-click">
			<i class="fa fa-save fa-fw"></i> <?php echo $section['form']['submit']->text; ?>
			</button>
			<button class="btn btn-default" type="reset">
			<i class="fa fa-refresh fa-fw"></i> <?php echo $section['form']['reset']->text; ?>
			</button>
		</div>
	</div>
	</form>

<?php }  ?>


</div><!-- <div class="tab-content"> -->
