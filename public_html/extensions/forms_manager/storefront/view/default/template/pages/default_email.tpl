<h1 class="heading1">
  <span class="maintext"><i class="fa fa-thumbs-up"></i> <?php echo $text_default_email_title; ?></span>
  <span class="subtext"></span>
</h1>

<div class="container-fluid">

<section class="mb40">
	<?php if( !empty($warning) ){ ?>
	<div class="alert alert-danger">
				<?php echo $text_error_message; ?><br>
				<?php foreach ( $warning as $message ){ ?>
					<p><?php echo $message; ?></p><br>
				<?php } ?>
	</div>
	<?php }else{ ?>
		<div class="alert alert-success"><?php echo $text_success_message; ?></div>
	<?php } ?>
<a class="btn btn-default " href="<?php echo $continue_button->href?>" ><i class="fa fa-arrow-right"></i> <?php echo $continue_button->text; ?></a>
</section>
</div>

