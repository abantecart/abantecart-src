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
		<tr style="display:none;"><td></td><td></td><td></td></tr>
	</table>
    </div>
    <div class="gbottom">
        <div class="left"></div>
        <div class="right"></div>
        <div class="center"></div>
    </div>
</div>

<script type="text/javascript">
var <?php echo $id; ?>_buildList = function () {
	var tmp = jQuery.parseJSON( $('#<?php echo  $form_name.'_'.$multivalue_hidden_id; ?>_selected').html());
	var id = [];
	var i=0;
	for (var e in tmp) {
		if (tmp[e].hasOwnProperty('status')) {
			if (tmp[e]['status']) {
				id[i] = e;
				i++;
			}
		}
	}

	$('#<?php echo $id; ?>').html('<tr style="display:none;"><td></td><td></td><td></td></tr>');

	$.ajax({
		url: '<?php echo $content_url;?>',
		dataType: 'json',
		type: 'POST',
		data: {'id': id},
		success: function(rows) {
			$('#<?php echo $id; ?> > tbody').html('');
			var row = '';
			var sort_order = '';
			if(rows.length>0){
				row = '<tr><td></td>';

				<?php if($with_sorting){?>
				row +='<td class="center"><?php echo $text['column_sort_order']; ?></td>';
				//also reorder rows by sort_order
				for(i in rows){
					rows[i]['sort_order'] = tmp[rows[i]['id']]['sort_order']>0 && rows[i]['sort_order']==0 ? tmp[rows[i]['id']]['sort_order'] : rows[i]['sort_order'];
				}
				rows.sort(function (a, b) {
				    var firstSortOrder = a['sort_order'];
				    var secondSortOrder = b['sort_order'];
				    return (firstSortOrder === secondSortOrder) ? a['name'] > b['name'] : firstSortOrder > secondSortOrder;
				});
				<?php } ?>

				row +='<td class="center"><?php echo $text['column_action']; ?></td></tr>';
				$('#<?php echo $id; ?> > tbody').append(row);
			}

			for ( i in rows) {
	 			row = '<tr id="'+rows[i]['id']+'"><td>' + rows[i]['name'] + (rows[i]['model'] ? ' (' + rows[i]['model'] + ')' : '') + '</td>';
				<?php if($with_sorting){?>
					row += '<td><span class="text_element"><div class="aform"><div class="afield mask1"><div class="cl"><div class="cr"><div class="cc">'+
						    	'<input type="text" value="' + rows[i]['sort_order'] +'" class="atext small-field" name="sort_order['+rows[i]['id']+']">'+
								'</div></div></div></div></div></td>';
				<?php } ?>
				row += '<td><a class="remove"></a></td></tr>';
				$('#<?php echo $id; ?> > tbody').append(row);
			}
		}
	});
}
$('#<?php echo $id; ?> input[name^="sort_order"]').on('change', function(){
	var tmp = jQuery.parseJSON( $('#<?php echo  $form_name.'_'.$multivalue_hidden_id; ?>_selected').html());
	for (var e in tmp) {
		if (tmp[e].hasOwnProperty('status')) {
			if (tmp[e.toString()]['status'] && e == $(this).parents('tr').attr('id') ) {
				tmp[e.toString()]['sort_order'] = $(this).val();
				break;
			}
		}
	}
	$('#<?php echo  $form_name.'_'.$multivalue_hidden_id; ?>_selected').html( JSON.stringify(tmp) );
});

$("#<?php echo $id; ?> a.remove").on('click', function(){
     <?php echo $id; ?>_delete_item($(this).parents('tr').attr('id'));
     return false;
});

var <?php echo $id; ?>_delete_item = function( id ) {
	var prefix = '<?php echo $form_name.'_'.$multivalue_hidden_id; ?>';
	var tmp = jQuery.parseJSON( $('#'+prefix+'_selected').html());
	if(!tmp){
		return;
	}
	if(confirm(<?php js_echo($text['delete_confirm']); ?>)){
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
//call function on page load event
$(document).ready(function(){
	<?php echo $id; ?>_buildList();
});
</script>
