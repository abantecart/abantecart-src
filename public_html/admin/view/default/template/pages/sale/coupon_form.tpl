<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php if ($tabs) { ?>
	<ul class="nav nav-tabs nav-justified nav-profile">
	<?php foreach ( $tabs as $tab){ ?>
		<li class="<?php echo $tab['class']; ?>"><a href="<?php echo $tab['href']; ?>"><span><?php echo $tab['text']; ?></span></a></li>
	<?php }?>
		<?php echo $this->getHookVar('extension_tabs'); ?>
	</ul>
<?php } ?>

<div class="tab-content">
	<div class="panel-heading">
			<div class="pull-right">
			    <div class="btn-group mr10 toolbar">
                    <?php if (!empty ($help_url)){ ?>
                    <a class="btn btn-white tooltips" href="<?php echo $help_url; ?>" target="new" data-toggle="tooltip" title="" data-original-title="Help">
                    <i class="fa fa-question-circle fa-lg"></i>
                    </a>
                    <?php } ?>
			    </div>
                <?php echo $form_language_switch; ?>
			</div>
	</div>
	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding">
		<label class="h4 heading"><?php echo $form_title; ?></label>
			<?php foreach ($form['fields'] as $name => $field) {
				//Logic to cululate fileds width
				$widthcasses = "col-sm-7";
				if ( is_int(stripos($field->style, 'large-field')) ) {
					$widthcasses = "col-sm-7";
				} else if ( is_int(stripos($field->style, 'medium-field')) || is_int(stripos($field->style, 'date')) ) {
					$widthcasses = "col-sm-5";
				} else if ( is_int(stripos($field->style, 'small-field')) || is_int(stripos($field->style, 'btn_switch')) ) {
					$widthcasses = "col-sm-3";
				} else if ( is_int(stripos($field->style, 'tiny-field')) ) {
					$widthcasses = "col-sm-2";
				}
				$widthcasses .= " col-xs-12";
			?>
		<div class="form-group <? if (!empty($error[$name])) { echo "has-error"; } ?>">
			<label class="control-label col-sm-3 col-xs-12" for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
			<div class="input-group afield <?php echo $widthcasses; ?> <?php echo ($name == 'description' ? 'ml_ckeditor' : '')?>">
				<?php echo $field; ?>
			</div>
		    <?php if (!empty($error[$name])) { ?>
		    <span class="help-block field_err"><?php echo $error[$name]; ?></span>
		    <?php } ?>
		</div>
			<?php }  ?><!-- <div class="fieldset"> -->
			
						
	</div>
	<div class="panel-footer">
		<div class="row">
		   <div class="col-sm-6 col-sm-offset-5">
			<button class="btn btn-primary">
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
	</div>
	</form>

</div><!-- <div class="tab-content"> -->
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jquery/ajax-chosen.js"></script>
<script type="text/javascript">
$(document).ready(function () {
	$("#couponFrm_coupon_product").ajaxChosen({
	    type: 'POST',
	    url: '<?php echo $products_list_url; ?>',
	    dataType: 'json'
	}, function (data) {
	    var results = [];
	    $.each(data, function (i, val) {
	    	var html = val.image + '&nbsp;' + val.name;
	    	if (val.model) {
	    		html += '&nbsp;(' + val.model + ')';
	    	}
	        results.push({ value: val.product_id, text: html });
	    });
	    return results;
	});
});
</script>
