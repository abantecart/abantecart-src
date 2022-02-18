<h1 class="heading1">
  <span class="maintext"><i class="fa fa-list"></i> <?php echo $heading_title; ?></span>
  <span class="subtext"></span>
</h1>

<div class="contentpanel">
	<h4 class="heading4"><?php echo $description; ?></h4>

	<div class="content">
	  <div class="row">
	    <div class="col-md-12 pull-left">
		<?php echo $this->getHookVar('pre_content'); ?>
		<?php echo $content; ?>
		<?php echo $this->getHookVar('post_content'); ?>
	    </div>
	  </div>
	</div>

	<a href="<?php echo $continue; ?>" class="btn btn-default pull-right mr10  mb10" title="">
		<i class="fa fa-arrow-right"></i>
		<?php echo $button_continue->text ?>
	</a>
	<?php echo $this->getHookVar('post_content_button'); ?>		

</div>