<div class="modal-header">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
	<h4 class="modal-title"><?php echo $text_title ?></h4>
</div>

<div class="tab-content">
	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding">
		<?php
		foreach ($form['fields'] as $name => $field) {?>
		<div class="form-group" >
			<label class="control-label col-sm-4 col-xs-12" for="<?php echo $field->element_id; ?>"><?php echo ${'entry_im_'.$name}; ?></label>
			<div class="input-group afield col-sm-5 col-xs-12">
				<?php
				if($name=='email'){ ?>
				<div class="input-group-btn">
					<button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					    <span class="caret"></span>
					  </button>
					  <ul class="emails dropdown-menu" aria-labelledby="dLabel">
						  <?php
                        foreach($admin_emails as $e){
                            echo '<li>'.$e.'</li>';
                        }
                        ?>
					  </ul>
				</div>
				<?php }
				echo $field; ?>
			</div>
		</div>
	<?php } ?>
	</div>

	<div class="panel-footer">
		<div class="row">
			<div class="col-sm-6 col-sm-offset-3 center">
				<a class="btn btn-primary on_save_close lock-on-click">
				 <i class="fa fa-save"></i> <?php echo $button_save_and_close; ?>
				 </a>&nbsp;
				<button class="btn btn-primary lock-on-click">
					<i class="fa fa-save"></i> <?php echo $form['submit']->text; ?>
				</button>
				&nbsp;
				<a class="btn btn-default" data-dismiss="modal" href="<?php echo $cancel; ?>">
					<i class="fa fa-arrow-left"></i> <?php echo $form['cancel']->text; ?>
				</a>

			</div>
		</div>
	</div>

	</form>
</div>

<script type="text/javascript">
$('#imsetFrm').submit(function () {
	save_changes();
	return false;
});

var modal_changed = false;
//save an close mode
$('.on_save_close').on('click', function(){
	var $btn = $(this);
	save_changes();
	$btn.closest('.modal').modal('hide');
	return false;
});

function save_changes(){
	$.ajax({
		url: '<?php echo $form['form_open']->action; ?>',
	    type: 'POST',
	    data: $('#imsetFrm').serializeArray(),
	    dataType: 'json',
	    success: function (data) {
			success_alert(data.result_text, true, "#im_settings_modal");
		    modal_changed = true;
		    resetAForm();
	    },
		complete: function(){
			resetLockBtn();
		}
	});
}

function on_modal_close(){
	if(modal_changed){
		location.reload();
	}
}

	$('.emails.dropdown-menu li').on('click', function(){
		$('#imsetFrm_settingsemail').val($(this).html()).change();
	});

</script>

