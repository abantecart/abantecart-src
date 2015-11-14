<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<?php if(!$mp_connected) { ?>
			<div class="btn-group">
			    <a class="btn btn-orange mp-connect tooltips" title="<?php echo $text_marketplace_connect; ?>" data-toggle="modal" data-target="#amp_modal">
			    	<i class="fa fa-sign-in fa-fw"></i> <?php echo $text_connect ?>
			    </a>
			</div>
			<?php } else { ?>
			<div class="btn-group">
			    <a	class="btn btn-default tooltips" 
			    	title="<?php echo $text_connected; ?>"
			    	data-confirmation="delete"
			    	onclick="disconnect(); return false;" href="#"
			    	data-confirmation-text="<?php echo $text_disconnect_confirm; ?>"
			    >
			    	<i class="fa fa-unlink fa-fw"></i>
			    </a>
			</div>
			<?php }  ?>
			<div class="btn-group">
			    <a class="btn btn-primary" href="<?php echo $btn_extensions_store->href;?>">
			    	<i class="fa fa-cloud-download fa-fw"></i> <?php echo $btn_extensions_store->text ?>
			    </a>
			</div>

			<?php if($setting_url) { ?>
			<div class="btn-group">
			    <a class="btn btn-default tooltips" href="<?php echo $setting_url;?>" title="<?php echo $text_configuration_settings; ?>">
			    	<i class="fa fa-gears fa-fw"></i>
			    </a>
			</div>			
			<?php } ?>
		</div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>		
	</div>
	
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<?php echo $listing_grid; ?>
	</div>
	
	<div class="panel-footer col-xs-12">
		<div class="text-center">
			<a href="<?php echo $more_extensions_url; ?>" class="btn btn-orange lock-on-click">
			<i class="fa fa-puzzle-piece fa-fw"></i> <?php echo $text_more_extensions; ?>
			</a>
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

<?php
	if(!$mp_connected) { 
	echo $this->html->buildElement(
		array('type' => 'modal',
				'id' => 'amp_modal',
				'modal_type' => 'md',
				'title' => $text_marketplace_connect,
				'content' =>'<iframe id="amp_frame" width="100%" height="380px" frameBorder="0"></iframe>
								<div id="iframe_loading" class="center_div"><i class="fa fa-spinner fa-spin fa-2x"></i></div>
							',
				'footer' => ''
		));
	}	
?>

<script type="application/javascript">
	/* run after grid load */
	var extension_grid_ready = function(data){

		var userdata = data.userdata;
		$('.grid_action_edit, .grid_action_install, .grid_action_uninstall, .grid_action_delete' ).each(function(){
			var row_id = $(this).parents('tr').attr('id');
			var href = $(this).attr('href') + '&extension='+userdata.extension_id[ row_id ];
			$(this).attr('href', href);
			$(this).next('.confirm_popover').find('.btn-danger').attr('href',href);
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
				    		$('#license_modal .modal-title').html(<?php js_echo($text_license); ?>);
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
			
			    $('#license_modal').modal({show: false, remote: '<?php echo $dependants_url; ?>&extension='+ extension});
			    var data = $('#license_modal .modal-body').html();
			    if(data.length>0){
			    	$('#license_modal').modal('show');
			    	$('#license_modal').on('shown.bs.modal', function () {
			    		$('#modal_confirm').click(function () {
			    			$(that).parents('td').find('.quicksave .icon_save').click();
			    			$('#license_modal').modal('hide');
			    		});
			    	});
			    	$('#license_modal').on('hidden.bs.modal', function () {
			    		$(that).parents('td').find('.quicksave .icon_reset').click();
			    	});
			    }else{
			
			    }
			}
		});
	}

	$('#amp_modal').on('shown.bs.modal', function () {
		var d = new Date();
    	$('#amp_modal iframe').attr("src","<?php echo $amp_connect_url; ?>&time_stamp="+d.getTime());
    	$('#iframe_loading').show();
    	$('#amp_modal').modal('show');
  	});
  	
  	$('#amp_frame').on('load', function() {  
    	$('#iframe_loading').hide();
	});


	var disconnect = function(){
		$.ajax({
			url: '<?php echo $amp_disconnect_url; ?>',
			type: 'GET',
			success: function (data) {
				if(data == 'success'){
					success_alert(<?php js_echo($text_disconnect_success); ?>,true);
					location.reload();
				} else if(data == 'error')  {
					error_alert(<?php js_echo($error_mp_connection); ?>,true);
				} else {				
					location.reload();
				}
			},
			global: false,
			error: function (jqXHR, textStatus, errorThrown) {
				error_alert(errorThrown);
			}
		});
		return false;
	}

	var reload_page = function(){
		location.reload();
		//important to clean up the modal 
		$('#amp_modal').modal('hide');
		$("#amp_modal").find(".modal-body").empty(); 
	}
</script>