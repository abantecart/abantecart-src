<?php if ($error_warning) { ?>
<div class="warning alert alert-error alert-danger"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="row">
	<div class="col-sm-12 col-lg-12">
		<ul class="content-nav">
			<li><?php
				if (!empty($search_form)) {
					?>
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
							<button type="submit"
									class="btn btn-xs btn-primary"><?php echo $search_form['submit']->text ?></button>
						</div>
					</form>
				<?php
				}
				?>
			</li>

			<?php if (!empty ($help_url)) { ?>
				<li>
					<div class="help_element">
						<a href="<?php echo $help_url; ?>" target="new">
							<i class="fa fa-question-circle"></i>
						</a></div>
				</li>
			<?php } ?>
		</ul>
	</div>
</div>

<?php

if ($search_categories) {?>

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
<?php
} else {
	?>
	<div class="flt_none clr_both heading"><?php echo $scat;?></div>
	<table class="table_list">
		<tr>
			<td class="left" id="no results"><?php echo $no_results_message; ?></td>
		</tr>
	</table>

	<?php } ?>




<script type="text/javascript">
	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		var target = $(e.target).attr("href");
		$(target+'_grid').trigger( 'resize' );
	});

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