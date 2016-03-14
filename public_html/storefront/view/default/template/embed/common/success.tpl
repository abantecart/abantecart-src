<?php echo $head; ?>
<h1 class="heading1">
  <span class="maintext"><i class="fa fa-thumbs-up"></i> <?php echo $heading_title; ?></span>
  <span class="subtext"></span>
</h1>

<div class="contentpanel">

<section class="mb40">
<h4 class="hidden">&nbsp;</h4>
	<p><?php echo $text_message; ?></p>
	
	<a href="<?php echo $continue; ?>" class="btn btn-default mr10" title="<?php echo $continue_button->text ?>">
	    <i class="fa fa-arrow-right"></i>
	    <?php echo $continue_button->text ?>
	</a>
</section>

</div>
<?php echo $footer; ?>