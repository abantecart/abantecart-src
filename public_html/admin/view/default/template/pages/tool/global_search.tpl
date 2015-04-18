<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
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
			    	</div>
			    </form>
			<?php } ?>
			</div>
		</div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>

<?php if ($search_categories) {?>
	<div class="panel-body-nopadding tab-content col-xs-12">

	<ul class="nav nav-tabs nav-justified nav-profile" role="tablist">
	<?php
	$i=0;
	foreach ($search_categories as $scat) {	?>
	<!-- Nav tabs -->
	  <li <?php echo $i==0 ? 'class="active"' : ''; ?>><a href="#<?php echo $scat;?>" role="tab" data-toggle="tab"><?php echo $search_categories_names[ $scat ];?></a></li>
	<?php
		$i++;
	} ?>
	</ul>

	<div class="tab-content">
		<?php
		$i=0;
		foreach ($search_categories as $scat) {	?>
			<div class="tab-pane <?php echo $i==0 ? 'active' : ''; ?>" id="<?php echo $scat; ?>">
				<div class="row">
					<div class="col-sm-12 col-lg-12">
						<div class="panel panel-default">
							<div class="panel-body">
						<?php echo ${"listing_grid_" . $scat}; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php $i++; } ?>
	</div>

	</div>
<?php } else { ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<div class="flt_none clr_both heading"><?php echo $scat;?></div>
		<table class="table_list">
		<tr>
			<td class="left" id="no results"><?php echo $no_results_message; ?></td>
		</tr>
		</table>
	</div>
<?php } ?>

</div>

<?php
echo $this->html->buildElement(
		array('type' => 'modal',
				'id' => 'gs_modal',
				'modal_type' => 'lg',
				'data_source' => 'ajax'
		));
?>

<script type="text/javascript">
	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		var target = $(e.target).attr("href");
		$(target+'_grid').trigger( 'resize' );

	});

	// Javascript to enable link to tab
	var url = document.location.toString();
	if (url.match('#')) {
	    $('.nav-tabs a[href=#'+url.split('#')[1]+']').tab('show') ;
	}

	// Change hash for page-reload
	$('.nav-tabs a').on('shown', function (e) {
	    window.location.hash = e.target.hash;
	})

	var grid_ready = function (grid_id, data){

		if( grid_id == 'languages_grid' ){
			$('#'+grid_id).find('td[aria-describedby$="_grid_search_result"]>a').each(
					function () {
						$(this).attr('data-toggle', 'modal').attr('data-target', '#gs_modal');
					});
		}
	}

	$('span.icon_search').click(function(){
		$('#search_form').submit();
	});

	$(document).ready(function(){
		<?php
		$time = 0;
		foreach($grid_inits as $func_name){
			echo 'setTimeout("'.$func_name.'($)",'.$time.');'."\n";
			$time+=500;
		}
	?>
	});
</script>