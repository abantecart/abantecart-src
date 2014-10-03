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
							<button type="reset" class="btn btn-xs btn-default"><i class="fa fa-refresh"></i></button>
						</div>
					</form>
				<?php
				}
				?>
			</li>
			<li>
				<div class="dropdown dropdown-toggle">
					<a data-toggle="dropdown"
					   href="#"
					   class="btn btn-primary dropdown-toggle tooltips"
					   title="<?php echo $button_insert; ?>" > <i class="fa fa-plus-circle fa-lg"></i>  <span class="caret"></span></a>
					<ul class="dropdown-menu " role="menu" aria-labelledby="dLabel">
						<?php foreach($inserts as $in){ ?>
							<li><a href="<?php echo $in['href'] ?>" ><?php echo $in['text']; ?></a></li>
						<?php } ?>
					</ul>
				</div>
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

<?php echo $this->html->buildElement(
		array('type' => 'modal',
				'id' => 'block_info_modal',
				'modal_type' => 'lg',
				'data_source' => 'ajax'));
?>


<script type="text/javascript">

	var grid_ready = function(){
		$('.grid_action_view[data-toggle!="modal"]').each(function(){
			$(this).attr('data-toggle','modal'). attr('data-target','#block_info_modal');
		});

		$('.grid_action_edit').each(function(){
			var tr = $(this).parents('tr');
			if(!tr.hasClass('disable-edit')){
				var rowid = tr.attr('id').split('_');
				if(rowid[1]){
					$(this).attr('href', $(this).attr('href')+'&custom_block_id='+rowid[1]);
				}
			}
		});
	};

</script>