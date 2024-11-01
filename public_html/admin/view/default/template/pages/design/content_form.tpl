<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php if ($tabs){ ?>
	<ul class="nav nav-tabs nav-justified nav-profile">
		<?php
		foreach ($tabs as $tab){
			?>
			<li <?php echo($tab['active'] ? 'class="active"' : '') ?>>
				<a href="<?php echo $tab['href'] ? $tab['href'] : 'Javascript:void(0);'; ?>"><span><?php echo $tab['text']; ?></span></a>
			</li>
		<?php } ?>
		<?php echo $this->getHookVar('extension_tabs'); ?>
	</ul>
<?php } ?>

<div id="content" class="panel panel-default">
	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<?php if ($insert){ ?>
				<div class="btn-group mr5 toolbar">
					<a class="actionitem btn btn-primary lock-on-click tooltips" href="<?php echo $insert; ?>"
					   title="<?php echo $button_add; ?>">
						<i class="fa fa-plus fa-fw"></i>
					</a>
				</div>
			<?php } ?>
            <?php if ($content_id) { ?>
                <div class="btn-group">
                    <a class="btn btn-white lock-on-click tooltips mr5" href="<?php echo $clone_url; ?>"
                       data-toggle="tooltip" title="<?php echo $text_clone; ?>"
                       data-original-title="<?php echo $text_clone; ?>">
                        <i class="fa fa-clone fa-fw"></i>
                    </a>
                </div>
            <?php } ?>
            <?php if ($preview) { ?>
                <div class="btn-group">
                    <a class="btn btn-white lock-on-click tooltips" target="_blank" href="<?php echo $preview; ?>"
                       data-toggle="tooltip" title="<?php echo $text_view; ?>"
                       data-original-title="<?php echo $text_view; ?>">
                        <i class="fa fa-external-link"></i>
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
		<div class="form-group <?php if (!empty($error[$name])){
			echo "has-error";
		} ?>">
            <?php
                if ($field->type == 'hidden') {
                    echo $field;
                } else {
            ?>
                <label class="control-label col-sm-3 col-xs-12"
                       for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>

                <div id="field_<?php echo $name; ?>"
                     class="input-group afield <?php echo $widthcasses; ?> <?php echo($name == 'content' ? 'ml_ckeditor' : '') ?>">
                    <?php if ($name == 'keyword'){ ?>
                        <span class="input-group-btn">
                        <?php echo $keyword_button; ?>
                    </span>
                    <?php } ?>
                    <?php
                        echo $field;
                    ?>
                </div>
                <?php if (!empty($error[$name])){ ?>
                    <span class="help-block field_err"><?php echo $error[$name]; ?></span>
                <?php } ?>
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
		</div>
	</div>
	</form>

</div><!-- <div class="tab-content"> -->

<?php echo $resources_scripts; ?>
<script type="text/javascript">
	$(document).ready(function () {

		$('#contentFrm_generate_seo_keyword').click(function () {
			var seo_name = $('#contentFrm_title').val().replace('%', '');
			$.get('<?php echo $generate_seo_url;?>&seo_name=' + seo_name, function (data) {
				$('#contentFrm_keyword').val(data).change();
			});
			return false;
		});

	});
</script>
