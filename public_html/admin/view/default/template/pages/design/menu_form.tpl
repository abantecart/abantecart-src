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

			<?php
			if($name=='item_url'){ ?>
			<div class="form-group <?php if (!empty($error[$name])) { echo "has-error"; } ?>">
				<label class="control-label col-sm-3 col-xs-12" ></label>
				<div class="input-group afield col-sm-9 col-xs-12">
					<div class="pull-left col-sm-6 col-xs-12">
						<label class="control-label col-sm-5 mt10" for="<?php echo $link_type->element_id; ?>">
							<?php echo $entry_link_type; ?></label>
						<div class="input-group afield col-sm-7 mt10">
						<?php echo $link_type;  ?>
						</div>
					</div>
				<?php foreach(array('link_category', 'link_content') as $subfld_name){?>
						<div id="<?php echo $subfld_name.'_wrapper';?>" class="link_types pull-left col-sm-6 col-xs-12 <?php echo ($subfld_name == 'link_type' ? '' : 'hide');?>">
							<div class="input-group afield col-sm-7 mt10">
							<?php echo $$subfld_name;  ?>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
			<?php } ?>
			<div class="form-group <?php if (!empty($error[$name])) { echo "has-error"; } ?>">
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

	$('#link_type').change(function(){
        var type_name = $(this).val();
		$('div.link_types').each(function(){
		    if($(this).attr('id') == 'link_'+type_name+'_wrapper'){
		        $(this).show().removeClass('hide');
			}else{
				$(this).hide().addClass('hide');
			}
		});
		if(type_name == 'custom'){
			$('input[name="item_url"]').removeAttr('readonly');
		}else{
			$('input[name="item_url"]').attr('readonly','readonly');
		}
		return false;
    });

	$('#menu_categories').change(function(){
        var c_id = $(this).val();
		if(c_id.length>0) {
            $('input[name="item_url"]').val('product/category&path=' + c_id)
				.removeAttr('readonly')
            	.change()
            	.attr('readonly','readonly');
            $("#menu_information").val($("#menu_information option:first").val());
        }
		return false;
    });

	$('#menu_information').change(function(){
        var c_id = $(this).val();
		if(c_id.length>0){
        	$('input[name="item_url"]').val('content/content&content_id='+c_id)
				.removeAttr('readonly')
				.change()
				.attr('readonly','readonly');
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
			$('#link_category_wrapper').removeClass('hide');
			$('#link_type').val('category');
			$('input[name="item_url"]').attr('readonly','readonly');

		}else if(val.search("content/content&content_id=")>-1){
			id = val.replace('content/content&content_id=', '');
			$('#menu_information').val(id).change();
			$('#link_content_wrapper').removeClass('hide');
			$('#link_type').val('content');
			$('input[name="item_url"]').attr('readonly','readonly');
		}else{
			$('#link_type').val('custom');
			$('input[name="item_url"]').removeAttr('readonly');
		}
	}

	$(document).ready(function(){
		preselect();
	});
});

</script>