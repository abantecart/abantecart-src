<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group mr10 toolbar">
				<a class="btn btn-primary tooltips" href="<?php echo $insert; ?>" title="<?php echo $button_add; ?>">
				<i class="fa fa-plus"></i>
				</a>
			</div>
			<div class="btn-group mr10 toolbar">
			<?php if (!empty($search_form)) { ?>
			    <form id="<?php echo $search_form['form_open']->name; ?>"
			    	  method="<?php echo $search_form['form_open']->method; ?>"
			    	  name="<?php echo $search_form['form_open']->name; ?>" class="form-inline" role="form">

			    	<?php
			    	foreach ($search_form['fields'] as $f) {
			    		?>
			    		<div class="form-group">
			    			<div class="input-group input-group-sm">
			    				<?php echo $f; ?>
			    			</div>
			    		</div>
			    	<?php
			    	}
			    	?>
			    	<div class="form-group">
			    		<button type="submit" class="btn btn-xs btn-primary tooltips" title="<?php echo $button_filter; ?>">
			    			<?php echo $search_form['submit']->text ?>
			    		</button>
			    		<button type="reset" class="btn btn-xs btn-default tooltips" title="<?php echo $button_reset; ?>">
			    			<i class="fa fa-refresh"></i>
			    		</button>
			    	</div>
			    </form>
			<?php } ?>
			</div>
		</div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>

	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<?php echo $listing_grid; ?>
	</div>
</div>
<?php echo $this->html->buildElement(
		array(
				'type' => 'modal',
				'id' => 'viewport_modal',
				'modal_type' => 'lg',
                'data_source' =>'ajax',
				'title' => 'Category Preview',
		));
?>
<script type="text/javascript">
    $('#category_grid_wrapper a.grid_action_expand').click(function(){
        var new_url = '<?php echo $grid_url; ?>&'+$(this).attr('rel');
        $('#category_grid')
            .jqGridHistory('setGridParam',{url:new_url})
            .trigger("reloadGrid");
        return false;
    });

	var grid_ready = function(data) {
		var url = '<?php echo $embed_url?>';
		$('#category_grid tr[role="row"]').each(function () {
			if ($(this).attr('id')) {
				url += '&category_id[]=' + $(this).attr('id');
			}
		});
		$('a[data-target="#embed_modal"]').attr('href', url);

		//do modal edit for action button in grid
		$("td[aria-describedby=category_grid_action] ul.grid-dropdown>li>a").each(function(){
			var viewport_url = $(this).attr('data-viewport-href');
			if(!viewport_url){ return null; }
			$(this).attr('data-fullmode-href', $(this).attr('href'));
			$(this).removeAttr('data-viewport-href');
			$(this).attr('href', viewport_url)
					.attr('data-toggle','modal')
					.attr('data-target','#viewport_modal');
		})
	}
	$('#viewport_modal').on('shown.bs.modal', function(e){
		var target = $(e.relatedTarget);

		$(this).find('.modal-footer a.btn.expand').attr('href',target.attr('data-fullmode-href'));
		var category_name = target.parents('tr').find('td[aria-describedby="category_grid_name"] label').text();
		var title = '<?php echo $update_title;?> - '+ category_name;
		$(this).find('.modal-title').html(title);
	})
</script>
