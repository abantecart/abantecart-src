<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php if ($tabs) { ?>
	<ul class="nav nav-tabs nav-justified nav-profile">
	<?php foreach ( $tabs as $tab){ ?>
		<li class="<?php echo $tab['class']; ?>"><a href="<?php echo $tab['href']; ?>"><span><?php echo $tab['text']; ?></span></a></li>
	<?php }?>
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
		<label class="h4 heading"><?php echo $form_title; ?></label>
			<?php
			$fields = $form['fields'];
			unset($fields['link_category'],$fields['link_page']);
			foreach ($fields as $name => $field) {
				//Logic to calculate fields width
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
				$widthcasses .= " col-xs-12";	?>

			<div class="form-group <?php if (!empty($error[$name])) { echo "has-error"; } ?>">
				<label class="control-label col-sm-3 col-xs-12" for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
				<div class="input-group afield <?php echo $widthcasses; ?> <?php echo ($name == 'description' ? 'ml_ckeditor' : '')?>">
					<?php echo $field; ?>
				</div>
				<?php if (!empty($error[$name])) { ?>
				<span class="help-block field_err"><?php echo $error[$name]; ?></span>
				<?php } ?>
			</div>
			<?php
			if($name=='item_url'){ ?>
			<div class="form-group <?php if (!empty($error[$name])) { echo "has-error"; } ?>">
				<label class="control-label col-sm-3 col-xs-12" ></label>
				<div class="input-group afield col-sm-7 col-xs-12">
				<?php foreach(array('link_category', 'link_page') as $subfld_name){?>
						<label class="control-label col-sm-3 mt10" for="<?php echo $form['fields'][$subfld_name]->element_id; ?>"><?php echo ${'entry_' . $subfld_name}; ?></label>
						<div class="input-group afield col-sm-9 mt10">
						<?php echo $form['fields'][$subfld_name];  ?>
						</div>
					<?php } ?>
				</div>
			</div>
			<?php } ?>

		<?php }  ?><!-- <div class="fieldset"> -->


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
jQuery(function($){

	$('#menu_categories').change(function(){
        var c_id = $(this).val();
		if(c_id.length>0) {
            $('input[name="item_url"]').val('product/category&path=' + c_id).change();
            $("#menu_information").val($("#menu_information option:first").val());
        }
		return false;
    });

	$('#menu_information').change(function(){
        var c_id = $(this).val();
		if(c_id.length>0){
        	$('input[name="item_url"]').val('content/content&content_id='+c_id).change();
			$("#menu_categories").val($("#menu_categories option:first").val());
		}
		return false;
    });

	function preselect(){
		var val = $.trim( $('input[name="item_url"]').val());
		var id;

		if(val.search("product/category&path=")>-1){
			id = val.replace('product/category&path=', '');
			$('#menu_categories').val(id).change();
		}else if(val.search("content/content&content_id=")>-1){
			id = val.replace('content/content&content_id=', '');
			$('#menu_information').val(id).change();
		}
	}

	$(document).ready(function(){
		preselect();
	});
	$('input[name="item_url"]').on('keyup',preselect);
});

</script>