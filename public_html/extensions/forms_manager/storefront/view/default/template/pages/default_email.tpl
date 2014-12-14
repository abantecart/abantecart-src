<h1 class="heading1">
  <span class="maintext"><i class="icon-thumbs-up"></i> <?php echo $text_default_email_title; ?></span>
  <span class="subtext"></span>
</h1>

<div class="container-fluid">

<section class="mb40">
	<?php if( !empty($warning) ){ ?>
				<?php echo $text_error_message; ?>
				<?php foreach ( $warning as $message ): ?>
					<p></div><?php echo $message; ?></p>
				<?php endforeach; ?>
			<?php }else{ ?>
	<p><?php echo $text_success_message; ?></p>
	<?php } echo $continue_button;?>
</section>
</div>

