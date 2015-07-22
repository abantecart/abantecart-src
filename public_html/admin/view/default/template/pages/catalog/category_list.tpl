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
	}
</script>
