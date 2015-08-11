<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group">
				<a class="btn btn-primary actionitem tooltips" title="<?php echo $manage_extensions->title; ?>" href="<?php echo $manage_extensions->href; ?>">
				<i class="fa fa-puzzle-piece"></i>
				</a>
			</div>

			<div class="btn-group toolbar">
				<a class="actionitem btn btn-default lock-on-click tooltips" href="<?php echo $current_url; ?>" title="<?php echo $button_reload; ?>">
					<i class="fa fa-refresh fa-fw"></i>
				</a>
			</div>
			
		</div>

		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>

	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<label class="h4 heading"><?php echo $text_storefront_templates; ?></label>
		
		<div class="row type_blocks mt10">
<?php
	foreach ($templates as $template) {
?>		
			<div class="col-md-4 col-sm-6 template_block">
				<div class="text-center template_thumbnail <?php if(!$template['set_defailt_url']) echo 'default';?>">   
					<div class="h5"><?php echo $template['name']; ?></div>
				             
					<a class="text-center" href="<?php echo $template['edit_url']; ?>">
					<img class="text-center img-responsive" src="<?php echo $template['preview']; ?>" />
					</a>

					<div class="caption center">
					<?php 
						if($template['set_defailt_url']) {
					?>
						<a href="<?php echo $template['set_defailt_url']; ?>" class="btn tooltips" data-original-title="<?php echo $text_set_template; ?>">
							<i class="fa fa-check fa-lg"></i>
						</a>
					<?php 
						}
					?>
						<a href="<?php echo $template['edit_url']; ?>" target="_template" class="btn tooltips" data-original-title="<?php echo $text_edit; ?>">
							<i class="fa fa-edit fa-lg"></i>
						</a>
					<?php 
						if($template['extn_url']) {
					?>
						<a href="<?php echo $template['extn_url']; ?>" target="_template" class="btn tooltips" data-original-title="<?php echo $text_edit_extension; ?>">
							<i class="fa fa-puzzle-piece fa-lg"></i>
						</a>
					<?php 
						}
					?>
						<a href="<?php echo $template['clone_url']; ?>" target="_template" class="btn tooltips" data-original-title="<?php echo $text_clone_template; ?>">
							<i class="fa fa-copy fa-lg"></i>
						</a>
					</div>

				</div>
			</div>		
<?php 
	} 
?> 			
		</div>
		
	</div>

</div>

<script type="text/javascript"><!--

$('.delete_page_layout').click(function(e) {
  e.stopPropagation();
  e.preventDefault();
  
  if (confirm('<?php echo $text_delete_confirm; ?>' )) {
    var url = $(this).attr('href');
    window.location = url + '&confirmed_delete=yes';  
  }
});

--></script>