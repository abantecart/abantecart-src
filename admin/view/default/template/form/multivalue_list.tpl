<b class="group_title"><?php echo $group['name'] ?></b>
<?php if ( !empty($group['description']) ) { ?>
<p class="group_description"><?php echo $group['description'] ?></p>
<?php } ?>
<div class="group_fields">
    <div class="gtop">
        <div class="left"></div>
        <div class="right"></div>
        <div class="center"></div>
    </div>
    <div class="gmiddle" style="width: 400px;">
	    <table id="<?php echo $id; ?>" class="multivalue list">
<?php
if($values){
		    echo '<tr><td></td><td class="center">Action</td></tr>';
	foreach ( $values as $value ) {
	    ?>
		<tr id="<?php echo $value['id']; ?>"><td nowrap><?php echo $value['name']; ?></td>
			<td>
				<a class="remove"></a>
			</td>
		</tr>
<?php
    }
	    }else{
		    echo '<tr style="display:none;"><td></td><td></td></tr>';
	    }
?>
	</table>
    </div>
    <div class="gbottom">
        <div class="left"></div>
        <div class="right"></div>
        <div class="center"></div>
    </div>
</div>

<script type="text/javascript">

function <?php echo $id; ?>_buildList() {
	var tmp = jQuery.parseJSON( $('#<?php echo  $form_name.'_'.$multivalue_hidden_id; ?>_selected').html());
	var id = new Array();
	var i=0;
	for (var e in tmp) {
		if (tmp[e].hasOwnProperty('status')) {
			if (tmp[e]['status']) {
				id[i] = e;
				i++;
			}
		}
	}

	$('#<?php echo $id; ?>').html('<tr style="display:none;"><td></td><td></td></tr>');

	$.ajax({
		url: '<?php echo $content_url;?>',
		dataType: 'json',
		type: 'POST',
		data: {'id': id},
		success: function(data) {
			$('#<?php echo $id; ?> > tbody').html('');
			if(data.length>0){
				$('#<?php echo $id; ?> > tbody').append('<tr><td></td><td class="center">Action</td></tr>');
			}
			for (i = 0; i < data.length; i++) {
	 			$('#<?php echo $id; ?> > tbody').append('<tr id="'+data[i]['id']+'"><td>' + data[i]['name'] + (data[i]['model'] ? ' (' + data[i]['model'] + ')' : '') + '</td>'+
						 '<td><a class="remove"></a></td></tr>');
			}
		}
	});
}

$("#<?php echo $id; ?> a.remove").live('click', function(){
     <?php echo $id; ?>_delete_item($(this).parents('tr').attr('id'));
     return false;
});

function <?php echo $id; ?>_delete_item( id ) {
	var prefix = '<?php echo $form_name.'_'.$multivalue_hidden_id; ?>';
	var tmp = jQuery.parseJSON( $('#'+prefix+'_selected').html());
	if(!tmp){
		return;
	}
	if(confirm('<?php echo $text['delete_confirm'];?>')){
		tmp[id]['status'] = false;
		var newvalue = JSON.stringify(tmp);
		$('#'+prefix+'_selected').html( newvalue ) ;
		$('#'+prefix+'_buffer').html( newvalue ) ;
		$('#<?php echo $return_to; ?>').html( <?php echo $form_name.'_'.$multivalue_hidden_id; ?>_count_selected( prefix+'_selected'));

		if (ovalue != $('#'+prefix+'_selected').html()) {
						$('#'+prefix+'_save_reset').show();
						$('#<?php echo $return_to ?>').parent().addClass('multivalue changed');
		}
		<?php echo $id; ?>_buildList();
	}
}
</script>
