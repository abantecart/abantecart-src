<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>
<div id="content" class="panel panel-default">
	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left"></div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
	</div>

	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<div class="panel-heading">
			<h4 class="panel-title"><?php echo $heading_title; ?></h4>
		</div>

		<div class="panel-body panel-body-nopadding">
			<table class="table table-striped">
				<?php if($extensions){?>
				<thead>
			        <tr >
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
			    <?php if($extensions){
			        foreach ($extensions as $extension) { ?>
			        <tr class="left">
			          <td><?php echo $extension['name']; ?></td>
			          <td><?php echo $extension['type'] ?></td>
			          <td><?php echo $extension['category'] ?></td>
			          <td><?php echo $extension['status'] ?></td>
			          <td><?php echo $extension['installed_version'] ?></td>
			          <td><a href="<?php echo $extension['mp_url']; ?>" target="_blank" title="<?php echo $text_view_details;?>"><i class="fa fa-info-circle"></i> <?php echo $extension['new_version'] ?></a></td>
			          <td><?php if($extension['install_url']){?>
				          <a href="<?php echo $extension['install_url']; ?>" title="<?php echo $text_upgrade_now;?>"><i class="fa fa-play"></i></a>
					<?php } ?>
					  </td>
			        </tr>
			        <?php } ?>
			        <?php } else { ?>
			        <tr>
			          <td class="center" ><?php echo $text_nothing_todo; ?></td>
			        </tr>
			        <?php } ?>
				</tbody>
			</table>
		</div>

	</div>
</div>

<?php
if ($extensions) { ?>
<script type="text/javascript">

$('td .btn_switch button').click(function(){
	var href = $(this).parents('div').attr('data-edit-url');
	if(href){
		goTo(href);
	}
	return false;
});

</script>
<?php } ?>