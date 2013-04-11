<section id="special" class="row mt40">
    <div class="container-fluid">
<?php if ( $block_framed ) { ?>
      <h1 class="heading1"><span class="maintext"><?php echo $heading_title; ?></span><span class="subtext"><?php echo $heading_subtitle; ?></span></h1>
<?php } ?>

	<?php include( $this->templateResource('/template/blocks/product_list.tpl') ) ?>

<?php if ( $block_framed ) { ?>
<?php } ?>
	</div>
</section>