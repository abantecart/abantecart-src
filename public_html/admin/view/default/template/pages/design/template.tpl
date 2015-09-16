<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group">
				<a class="btn btn-primary actionitem tooltips" title="<?php echo $manage_extensions->title; ?>" href="<?php echo $manage_extensions->href; ?>">
				<i class="fa fa-puzzle-piece"></i>
				</a>
			</div>

			<div class="btn-group toolbar mr10">
				<a class="actionitem btn btn-default lock-on-click tooltips" href="<?php echo $current_url; ?>" title="<?php echo $button_reload; ?>">
					<i class="fa fa-refresh fa-fw"></i>
				</a>
				<?php echo $this->getHookVar('template_toolbar_buttons'); ?>
			</div>			
			<?php echo $this->getHookVar('template_panel_buttons'); ?>
		</div>

		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>	
	</div>

	<div class="panel-body panel-body-nopadding tab-content col-xs-12 <?php echo $status_off; ?>">
		<label class="h4 heading"><?php echo $text_storefront_templates; ?></label>
		
		<div class="row type_blocks mt10">
<?php
	foreach ($templates as $template) {
?>		
			<div class="col-md-4 col-sm-6 template_block">
				<div class="text-center template_thumbnail <?php if(!$template['set_default_url']) echo 'default';?>">   
					<div class="h5"><?php echo $template['name']; ?></div>
				             
					<a class="text-center" href="<?php echo $template['edit_url']; ?>">
					<img class="text-center img-responsive" src="<?php echo $template['preview']; ?>" />
					</a>

					<div class="caption center">
					<?php 
						if($template['set_default_url']) {
					?>
						<a href="<?php echo $template['set_default_url']; ?>" class="btn tooltips" data-original-title="<?php echo $text_set_template; ?>">
							<i class="fa fa-toggle-off fa-lg"></i>
						</a>
					<?php 
						} else {
					?>
						<span class="btn tooltips"><i class="fa fa-toggle-on fa-lg"></i></span>
						
					<?php 
						}
					?>
						<a href="<?php echo $template['edit_url']; ?>" class="btn tooltips" data-original-title="<?php echo $text_edit; ?>">
							<i class="fa fa-gear fa-lg"></i>
						</a>
					<?php 
						if($template['extn_url']) {
					?>
						<a href="<?php echo $template['extn_url']; ?>" target="_template" class="btn tooltips" data-original-title="<?php echo $text_edit_extension; ?>">
							<i class="fa fa-puzzle-piece fa-lg"></i>
						</a>

					<?php }

					if($template['clone_button']){ ?>
						<a href="<?php echo $template['clone_button']->href; ?>"
						   target="_template"
						   class="btn tooltips"
						   data-original-title="<?php echo $template['clone_button']->text; ?>"
						   <?php echo $template['clone_button']->attr; ?> >
							<i class="fa fa-clone fa-lg"></i>
						</a>
					<?php } ?>
					
					<?php echo $this->getHookVar('template_control_buttons_'.$template['name']); ?>
					</div>					
				</div>
			</div>		
<?php } ?>
		</div>
	</div>
</div>