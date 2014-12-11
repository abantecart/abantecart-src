<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left"></div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
	</div>

	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">

		<label class="h4 heading"><?php echo $heading_title; ?></label>
		<?php foreach ($form['fields'] as $name => $field) { ?>
		<?php
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

		//special case for payment specific fee
		if("payment_fee" != substr($name,0,11)) {
		?>		
		<div class="form-group <?php if (!empty($error[$name])) { echo "has-error";} ?>">
			<label class="control-label col-sm-3 col-xs-12" for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
			<div id="<?php echo $name?>_wrp" class="input-group afield <?php echo $widthcasses; ?> <?php echo($name == 'description' ? 'ml_ckeditor' : '') .' '.(is_array($field) ? "form-inline" : "") ?>">
				<?php if(!is_array($field)){
					echo $field;
				}else{
					foreach($field as $i=>$f){
					?>
						<div class="input-group afield col-sm-2"><?php echo $f;?></div>
				<?php }
				} ?>
			</div>
			<?php if (!empty($error[$name])) { ?>
				<span class="help-block field_err"><?php echo $error[$name]; ?></span>
		</div>
		<?php }
		} else {
		//special case for payment specific fee
		?>
		<div class="form-group form-group-sm <?php if (!empty($error[$name])) { echo "has-error";} ?>">
			<label class="control-label col-sm-3 col-xs-12"><?php echo $field[0]; ?></label> 
			<div id="<?php echo $name?>_wrp" class="input-group afield form-inline col-sm-9">
				<div class="input-group input-group-sm afield col-sm-2"><?php echo $field[1]; ?></div>	
				<div class="input-group input-group-sm col-sm-3 text-right"><?php echo $field[2]; ?></div>			
				<div class="input-group input-group-sm afield col-sm-1"><?php echo $field[3]; ?></div>			
				<div class="input-group input-group-sm col-sm-1 text-right"><?php echo $field[4]; ?></div>			
				<div class="input-group input-group-sm afield col-sm-1"><?php echo $field[5]; ?></div>			
				<div class="input-group input-group-sm afield col-sm-1"><?php echo $field[6]; ?></div>			
			</div>
		<?php } ?>	
		</div>
		<?php } ?><!-- <div class="fieldset"> -->

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
</div>

<script language="javascript" type="text/javascript">
	$(document).ready(function(){
		var btn = '<a class="add btn btn-success" title="<?php echo $text_add?>" href="#"><i class="fa fa-plus-circle fa-lg"></i></a>';
		$('div[id*="payment_fee"]').last().parents('.form-group').after('<div class="form-group"><label class="control-label col-sm-3 col-xs-12"></label><div class="input-group afield">'+btn+'</div></div>');

		$('a.add').click(function(){
			var new_row = $('div[id*="payment_fee"]').last().parents('.form-group').clone();
			$('div[id*="payment_fee"]').last().parents('.form-group').after(new_row);
			return false;
		});
	});
</script>