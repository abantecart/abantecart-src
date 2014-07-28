<?php if ($error_warning) { ?>
<div class="warning alert alert-error alert-danger"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="row">
	<div class="col-sm-12 col-lg-12">
		<ul class="content-nav">
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
				'id' => 'message_info_modal',
				'name' => 'message_info_modal',
				'modal_type' => 'lg',
				'data_source' => 'remote'));
?>

<script type="text/javascript">

	var grid_ready = function(){
		$('.grid_action_view[data-toggle!="modal"]').each(function(){
			$(this).attr('data-toggle','modal'). attr('data-target','#message_info_modal');
		});
	};

	$(document).on("hidden.bs.modal", function (e) {
		$('#message_grid').trigger("reloadGrid");
		notifier_updater();
	});

</script>
