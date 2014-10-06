<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group mr10 toolbar">
				<a id="insert_btn" class="actionitem btn btn-primary tooltips" href="<?php echo $insert; ?>" title="<?php echo $button_add; ?>">
				<i class="fa fa-plus fa-fw"></i>
				</a>
			</div>

			<div class="btn-group mr10 toolbar">
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
						        <button type="submit" class="btn btn-xs btn-primary tooltips" title="<?php echo $button_filter; ?>">
						                        <?php echo $search_form['submit']->text ?>
						        </button>
						        <button type="reset" class="btn btn-xs btn-default tooltips" title="<?php echo $button_reset; ?>">
						                <i class="fa fa-refresh"></i>
						        </button>
						</div>						
					</form>
				<?php
				}
				?>
			</div>
		</div>

		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>

	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<?php echo $listing_grid; ?>
	</div>

</div>

<?php
echo $this->html->buildElement(
		array('type' => 'modal',
				'id' => 'ld_modal',
				'modal_type' => 'lg',
				'content' => '',
				'title' => $text_please_confirm,
		));
?>

<script type="text/javascript">
	var doModal = function(obj){
		var href = $(obj).attr('href');

		$.ajax({
			url:href,
			type:'GET',
			dataType:'json',
			success:function (data) {
				if (data == '' || data == null) {
					return null;
				} else {
					if (data.html) {
						$('#ld_modal .modal-body').html(data.html);
						$('#ld_modal .modal-title').html(data.title);
					}
					$('#ld_modal').modal('show');
				}
			}
		});
	}

	var grid_ready = function(){
		$('.grid_action_edit').click(
		function () {
			doModal(this);
			return false;
		});
	}

	$('#insert_btn').click(
		function () {
			doModal(this);
			return false;
		});

</script>