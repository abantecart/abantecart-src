<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>
	<div id="content" class="panel panel-default">
		<div class="panel-heading col-xs-12">
			<div class="primary_content_actions pull-left"></div>
			<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
		</div>

		<div class="panel-body panel-body-nopadding tab-content col-xs-12">
			<div class="panel-body panel-body-nopadding">
				<?php
				if (!$mp_connected){ ?>
					<?php echo $text_please_connect; ?>
					<div class="btn-group">
						<a class="btn btn-orange mp-connect tooltips" title="<?php echo $text_marketplace_connect; ?>"
						   data-toggle="modal" data-target="#amp_modal">
							<i class="fa fa-sign-in fa-fw"></i> <?php echo $text_connect ?>
						</a>
					</div>
				<?php }else{ ?>
				<table class="table table-striped">
					<?php
					if ($extensions){
						?>
						<thead>
						<tr class="left">
							<td><?php echo $column_name; ?></td>
							<td><?php echo $column_type; ?></td>
							<td><?php echo $column_category; ?></td>
							<td><?php echo $column_status; ?></td>
							<td><?php echo $column_version; ?></td>
							<td><?php echo $column_new_version; ?></td>
							<td></td>
						</tr>
						</thead>
					<?php } ?>
					<tbody>
					<?php if ($extensions){
						foreach ($extensions as $extension){ ?>
							<tr class="left">
								<td><?php echo $extension['name']; ?></td>
								<td><?php echo $extension['type'] ?></td>
								<td><?php echo $extension['category'] ?></td>
								<td><?php echo $extension['status'] ?></td>
								<td><?php echo $extension['installed_version'] ?></td>
								<td><a href="<?php echo $extension['install_url']; ?>" target="_new"
								       title="<?php echo $text_view_details; ?>"><i
												class="fa fa-info-circle"></i> <?php echo $extension['new_version'] ?>
									</a>
								</td>
								<td><?php if ($extension['install_url']){ ?>
										<a href="<?php echo $extension['install_url']; ?>" target="_new"
										   title="<?php echo $text_upgrade_now; ?>"><i class="fa fa-cloud-download"></i></a>
									<?php } elseif (!$mp_connected){ ?>
										<a class="btn btn-orange mp-connect tooltips"
										   title="<?php echo $text_marketplace_connect; ?>"
										   data-toggle="modal"
										   data-target="#amp_modal">
											<i class="fa fa-cloud-download"></i>
										</a>
									<?php } ?>
								</td>
							</tr>
						<?php } ?>
					<?php } else{ ?>
						<tr>
							<td class="center"><?php echo $text_nothing_todo; ?></td>
						</tr>
					<?php } ?>
					</tbody>

				</table>
				<?php } ?>
			</div>

		</div>
	</div>

<?php
if (!$mp_connected){
	echo $this->html->buildElement(
			array ('type'       => 'modal',
			       'id'         => 'amp_modal',
			       'modal_type' => 'lg',
			       'title'      => $text_marketplace_connect,
			       'content'    => '<iframe id="amp_frame" width="100%" height="400px" frameBorder="0"></iframe>
							<div id="iframe_loading" class="center_div"><i class="fa fa-spinner fa-spin fa-2x"></i></div>
						',
			       'footer'     => ''
			));
} ?>
	<script type="text/javascript">
		var reload_page = function () {
			location.reload();
			//important to clean up the modal
			$('#amp_modal').modal('hide');
			$("#amp_modal").find(".modal-body").empty();
		}
	</script>

<?php
if ($extensions){ ?>
	<script type="text/javascript">

		$('td .btn_switch button').click(function () {
			var href = $(this).parents('div').attr('data-edit-url');
			if (href) {
				goTo(href);
			}
			return false;
		});

	</script>
<?php } ?>