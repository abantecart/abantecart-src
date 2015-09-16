<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php if ($tabs) { ?>
	<ul class="nav nav-tabs nav-justified nav-profile">
		<?php foreach($tabs as $tab){?>
		<li <?php echo ($tab['active'] ? 'class="active"' : '') ?>>
		<a href="<?php echo $tab['href'] ? $tab['href'] : 'Javascript:void(0);'; ?>"><span><?php echo $tab['text']; ?></span></a></li>
		<li>
		<?php } ?>
		<?php echo $this->getHookVar('extension_tabs'); ?>
	</ul>
<?php } ?>

<div id="content" class="panel panel-default">
	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group mr10 toolbar">
			<?php if($button_details){	?>
				<a class="btn btn-white tooltips"
					href="<?php echo $button_details->href; ?>"
					target="new" data-toggle="tooltip" title="<?php echo $button_details->text; ?>">
					<i class="fa fa-bar-chart-o fa-lg"></i>
				</a>
			<?php } ?>
			</div>
		</div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
	</div>

	<?php echo $form['form_open']; ?>
	<?php 
		foreach ($form['hidden_fields'] as $name => $field) {
			echo $field;
		}
	?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">

		<?php if($banner_type == 1) { ?>
		<div class="col-md-9 mb10">
		<?php } ?>
		<label class="h4 heading"><?php echo $form_title; ?></label>
		<div class="form-group">
			<label class="control-label col-sm-3 col-xs-12"></label>
			<div class="btn-group mr10 toolbar">
				<a class="btn btn-white disabled">
					<?php echo $banner_types['icon']; ?> <?php echo $banner_types['text']; ?>
				</a>
			</div>
		</div>
			<?php foreach ($form['fields'] as $name => $field) {
				if($name == 'new_banner_group'){ continue;}
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
		<div class="form-group <?php if (!empty($error[$name])) { echo "has-error"; } ?>">
			<label class="control-label col-sm-3 col-xs-12" for="<?php echo $field->element_id; ?>"><?php echo $form[ 'text' ][$name]; ?></label>
			<div class="input-group afield <?php echo $widthcasses; ?> <?php echo ($name == 'description' ? 'ml_ckeditor' : '')?>">
				<?php echo $field;
				if($name=='banner_group_name'){
					echo $form['fields']['new_banner_group'];
				}
				?>

			</div>
		    <?php if (!empty($error[$name])) { ?>
		    <span class="help-block field_err"><?php echo $error[$name]; ?></span>
		    <?php } ?>
		</div>
			<?php }  ?><!-- <div class="fieldset"> -->
		
		<?php if($banner_type == 1) { ?>
		</div>
		<div class="col-md-3 mb10">
			<div id="image">
			<?php echo $resources_html; ?>
			</div>
		</div>
		<?php } ?>
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
<?php echo $resources_scripts; ?>
<script type="text/javascript">
$(document).ready(function() {
	$('#BannerFrm_banner_group_name0').change();

	if($('#BannerFrm_description').length){
	    var cke = wrapCKEditor('BannerFrm_description', {height: '300px'} );
		addRL2CKE(cke);
	}

	$('#BannerFrm_banner_group_name0').on('change',function(){
	    if($(this).val()=='new'){
	    	$('#BannerFrm_banner_group_name1').fadeIn().focus();
	    }else{
	    	$('#BannerFrm_banner_group_name1').fadeOut();
	    }
	});

	$('#BannerFrm_banner_group_name\\\[0\\\]').change( function(){
		$(this).val() == 'new' ? $('#BannerFrm_banner_group_name\\\[1\\\]').show().parents('.aform').show() : $('#BannerFrm_banner_group_name\\\[1\\\]').hide().parents('.aform').hide();
		!$('#BannerFrm_banner_group_name\\\[1\\\]').is(':visible') ? $('#BannerFrm_banner_group_name\\\[1\\\]').val('<?php echo $new_group_hint; ?>') : null;
	});
	$('#BannerFrm_banner_group_name\\\[1\\\]').click( function(){
		$(this).val() == '<?php echo $new_group_hint; ?>' ? $(this).val('') : null;
	});
});




</script>