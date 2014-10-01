<?php if ($error_warning) { ?>
	<div class="warning alert alert-error alert-danger"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
	<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="row">
	<div class="col-sm-12 col-lg-12">
		<ul class="content-nav">
			<li>
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
							<div class="input-group input-group-sm">
								<?php echo $text_select_store; ?>
							</div>
						</div>
						<?php } ?>
						<div class="form-group">
							<div class="input-group input-group-sm">
								<?php echo $f; ?>
							</div>
						</div>
					<?php } ?>
					<div class="form-group">
						<a class="btn btn-xs btn-primary" href="<?php echo $btn_extensions_store->href;?>"><i class="fa fa-arrows-alt"></i><?php echo $btn_extensions_store->text ?></a>
						<a class="btn btn-xs btn-primary" href="<?php echo $btn_add_new->href;?>"><i class="fa fa-step-forward"></i><?php echo $btn_add_new->text ?></a>
					</div>
					<?php
					}
					?>
				</form>
			</li>

			<?php if (!empty ($form_language_switch)) { ?>
				<li>
					<?php echo $form_language_switch; ?>
				</li>
			<?php } ?>
			<?php if (!empty ($help_url)) { ?>
				<li>
					<div class="help_element">
						<a href="<?php echo $help_url; ?>" target="new">
							<i class="fa fa-question-circle fa-lg"></i>
						</a></div>
				</li>
			<?php } ?>
		</ul>
	</div>
</div>

<div class="row">
	<div class="col-sm-12 col-lg-12">
		<div class="panel panel-default">
			<div class="panel-body">
				<?php echo $listing_grid; ?>
			</div>
		</div>
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
	$('#extension_grid_search_store_selector').change(function () {
		location = '<?php echo $this->html->getSecureURL('extension/extensions/extensions'); ?>' + '&store_id=' + $(this).val();
		return false;
	});
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
							if(responseData.error_text){
								$('#license_modal .modal-title').html('&nbsp;');
								$('#license_modal .modal-body').html('<div class="alert alert-danger" role="alert">' + responseData.error_text + '</div>');
								$('#license_agree').hide();
								$('#license_modal').modal('show');
								return false;
							}else if (responseData.license_text) {
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