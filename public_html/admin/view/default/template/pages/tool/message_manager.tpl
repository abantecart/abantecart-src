<div id="aPopup">
	<div class="message_head" >
		<div id="msg_title" class="message_title"></div>
		<table>
			<tr class="message_status">
				<td><?php echo  $status; ?></td>
				<td><?php echo  $status_field; ?></td>
			</tr>
			<tr class="message_date">
				<td><?php echo  $create_date; ?></td>				
				<td><?php echo  $create_date_field; ?></td>
			</tr>
			<tr id="message_repeat" class="message_repeat">
				<td><?php echo  $repeats; ?></td>
				<td><?php echo  $repeat_field; ?></td>
			</tr>
		</table>
	</div>	
	<div class="message_body" >
		<div class="aform">
			<div class="afield mask2">
				<div class="tl"><div class="tr"><div class="tc"></div></div></div>
				<div class="cl"><div class="cr"><div class="cc">
					<div class="message_text" id="msg_body"></div>
				</div></div></div>
				<div class="bl"><div class="br"><div class="bc"></div></div></div>
			</div>
		</div>
	</div>
</div>
<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_information"><?php echo $heading_title; ?></div>
	  <div class="toolbar">
		<?php if ( !empty ($help_url) ) : ?>
	        <div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
	    <?php endif; ?>
    </div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">
    <?php echo $listing_grid; ?>
  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>
<script type="text/javascript">
var $aPopup = $('#aPopup');
var msg_id;
function show_popup(id){
	$aPopup = $('#aPopup').dialog({
		autoOpen: false, 
		modal: true, 
		resizable: false,
		width: 550,
		minWidth: 550,
		buttons:{
				"<?php echo $delete; ?>": delete_msg,
				"<?php echo $close; ?>": function(event, ui) {
							$(this).dialog('destroy');
							$("#message_grid").trigger("reloadGrid");
						}
			},
		open: function() {
		            //$('.ui-dialog-buttonpane').find('button:contains("close")').css('background-color','red').addClass('cancelButtonClass');
		        },

		resize: function(event, ui){
		}, 
		close: function(event, ui) {
			$(this).dialog('destroy');			
			$("#message_grid").trigger("reloadGrid");
		}
	});
	
	$.ajax({
		url: '<?php echo $popup_action; ?>',
		type: 'GET',
		dataType: 'json',
		data: 'id='+id+'&oper=show',
		success: function(data) {

			$aPopup.dialog( "option" , "title" ,data.title);

			$('#msg_status').val(data.status);
			$('#msg_create_date').val(data.create_date);
			$('#msg_body').html(data.message);

			msg_id = data.msg_id;
			if(Number(data.repeated)>0){							
				$('#message_repeat').show();
				$('#msg_repeat').val(data.repeated);
				}
			$aPopup.dialog('open');			
		}
	});
}
function delete_msg(){
	if(confirm('<?php echo $confirm; ?>')){
		$.ajax({
			url: '<?php echo $popup_action; ?>',
			type: 'POST',
			dataType: 'json',
			data: 'oper=del&id='+msg_id,
			success: function(data) {
					$aPopup.dialog('destroy');
					$("#message_grid").trigger("reloadGrid");
					update_notify();
			}
		});		
	}
}
function update_notify(){
	$.ajax({
			url: '<?php echo $notifier; ?>',
			type: 'POST',
			dataType: 'json',
			data: {},
			success: function(data) {
				if(data.msg_cnt){
					Number(data.msg_cnt.N)>0 ? $('#notice_msg_cnt').html(data.msg_cnt.N) : $('#notice_msg_box').hide();
					Number(data.msg_cnt.E)>0 ? $('#error_msg_cnt').html(data.msg_cnt.E) : $('#error_msg_box').hide();
					Number(data.msg_cnt.W)>0 ? $('#warning_msg_cnt').html(data.msg_cnt.W) : $('#warning_msg_box').hide();
				}
			}
	});
}
</script>