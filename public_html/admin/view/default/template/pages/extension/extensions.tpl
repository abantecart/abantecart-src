<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group">
				<?php
				if (!empty($search_form)) {
				?>
				<form id="<?php echo $search_form['form_open']->name; ?>"
				      method="<?php echo $search_form['form_open']->method; ?>"
				      name="<?php echo $search_form['form_open']->name; ?>" class="form-inline" role="form">
	
				    <?php
				    foreach ($search_form['fields'] as $n=>$f) {
				    	if($n=='store_selector'){ ?>
				    	<div class="form-group">
				    		<div class="input-group">
				    			<?php echo $text_select_store; ?>
				    		</div>
				    	</div>
				    	<?php } ?>
				    	<div class="form-group">
				    		<div class="input-group">
				    			<?php echo $f; ?>
				    		</div>
				    	</div>
				    <?php } ?>
				    <div class="form-group">
				    	<a class="btn btn-primary" href="<?php echo $btn_extensions_store->href;?>">
				    		<i class="fa fa-arrows-alt fa-fw"></i> <?php echo $btn_extensions_store->text ?>
				    	</a>
				    	<a class="btn btn-primary" href="<?php echo $btn_add_new->href;?>">
				    		<i class="fa fa-step-forward fa-fw"></i> <?php echo $btn_add_new->text ?>
				    	</a>
				    </div>
				    <?php
				    }
				    ?>
				</form>
			</div>
		</div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>		
	</div>
	
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<?php echo $listing_grid; ?>
	</div>
</div>

<?php
echo $this->html->buildElement(
		array('type' => 'modal',
				'id' => 'dep_modal',
				'modal_type' => 'sm',
				'title' => $text_please_confirm,
				'content' => '',
				'footer' => '<button type="button" class="btn btn-default" id="confirm_cancel">'.$button_cancel.'</button>
							<button type="button" class="btn btn-primary" id="confirm_disable">'.$button_confirm.'</button>'
		));
?>

<?php
echo $this->html->buildElement(
		array('type' => 'modal',
				'id' => 'license_modal',
				'name' => 'license_modal',
				'modal_type' => 'lg',
				'title' => $text_license,
				'content' => '',
				'footer' => '<button type="button" class="btn btn-default" data-dismiss="modal" id="license_cancel">'.$button_cancel.'</button>
											<button type="button" class="btn btn-primary" id="license_agree">'.$button_agree.'</button>'
		));
?>


<script type="application/javascript">
	/* run after grid load */
	var extension_grid_ready = function(data){

		var userdata = data.userdata;
		$('.grid_action_edit, .grid_action_install, .grid_action_uninstall, .grid_action_delete' ).each(function(){
			var row_id = $(this).parents('tr').attr('id');
			var href = $(this).attr('href');
			$(this).attr('href', href+'&extension='+userdata.extension_id[ row_id ]);
		});

		$('.grid_action_install' ).click(function(){
			var row_id = $(this).parents('tr').attr('id');
			var href = $(this).attr('href');

			$('#license_agree').click(function(){
				location = href;
				return false;
			});

			$.ajax({
					url: '<?php echo $license_url; ?>',
					type: 'GET',
					data: 'extension=' + userdata.extension_id[ row_id ],
					dataType: 'json',
					success: function (responseData) {
						if (responseData) {
							if(responseData.error_text.length>0){
								$('#license_modal .modal-title').html('&nbsp;');
								$('#license_modal .modal-body').html('<div class="alert alert-danger" role="alert">' + responseData.error_text + '</div>');
								$('#license_agree').hide();
								$('#license_modal').modal('show');
								return false;
							}else if (responseData.license_text.length>0) {
								$('#license_modal .modal-title').html('<?php echo $text_license?>');
								$('#license_modal .modal-body').html(responseData.license_text);
								$('#license_modal').modal('show');
								return false;
							}

						}
					}
			});
		});

		$('.grid_action_remote_install' ).each(function(){
			if(userdata.hasOwnProperty('installation_key')){
				var row_id = $(this).parents('tr').attr('id');
				var href = $(this).attr('href');
				$(this).attr('href', href+'&extension_key='+userdata.installation_key[ row_id ]);
			}
		});
		//check dependancies before disabling of extension
		$('td[aria-describedby="extension_grid_status"] button').click(function () {
					var switcher = $('td[aria-describedby="extension_grid_status"]').find("input[type='hidden']");
					var value = switcher.val();
					var that = this;
					if (value == 1 && switcher.attr('data-orgvalue')!=0) {
						var row_id = $(this).parents('tr').attr('id');
						var extension = userdata.extension_id[ row_id ];

						$('#license_modal').modal({remote: '<?php echo $dependants_url; ?>&extension='+ extension}).modal('show');
						$('#license_modal').on('shown.bs.modal', function () {
							$('#modal_confirm').click(function () {
								$(that).parents('td').find('.quicksave .icon_save').click();
								$('#license_modal').modal('hide');
							});
						});
						$('#license_modal').on('hidden.bs.modal', function () {
							$(that).parents('td').find('.quicksave .icon_reset').click();
						});
					}
				});
	}

</script>