<h1 class="heading1">
  <span class="maintext"><?php echo $heading_title; ?></span>
  <span class="subtext"></span>
</h1>

<div class="contentpanel">

	<?php if ($description) { ?>
	<div style="margin-bottom: 15px;"><?php echo $description; ?></div>
	<?php } ?>
	<?php if (!$categories && !$products) { ?>
	<div class="content"><?php echo $text_error; ?></div>
	<?php } ?>
	<?php if ($categories) { ?>
	<ul class="thumbnails row">
	    <?php for ($i = 0; $i < sizeof($categories); $i++) { ?>
	     <li class="col-md-2 col-sm-2 col-xs-6 align_center">
	    	<a href="<?php echo $categories[$i]['href']; ?>">
	    		<?php echo $categories[$i]['thumb']['thumb_html']; ?>
	    	</a>
	    	<div class="mt10 align_center" style="height: 40px;">
	    	<a href="<?php echo $categories[$i]['href']; ?>"><?php echo $categories[$i]['name']; ?></a>
	    	</div>
	    </li>
	    <?php } ?>
	</ul>
	<?php } ?>

	<?php if ($products) { ?>
	<div class="sorting well">
	  <form class=" form-inline pull-left">
	    <?php echo $text_sort; ?>&nbsp;&nbsp;<?php echo $sorting; ?>
	  </form>
	  <div class="btn-group pull-right">
	    <button class="btn" id="list"><i class="fa fa-th-list"></i>
	    </button>
	    <button class="btn btn-orange" id="grid"><i class="fa fa-th"></i></button>
	  </div>
	</div>

	<?php include( $this->templateResource('/template/pages/product/product_listing.tpl') ) ?>
		
	<div class="sorting well">
		<?php echo $pagination_bootstrap; ?>
		<div class="btn-group pull-right">
		</div>
	</div>
	
<?php } ?>		
		
</div>

<script type="text/javascript">

$('#sort').change(function () {
	Resort();
});

function Resort() {
	url = '<?php echo $url; ?>';
	url += '&sort=' + $('#sort').val();
	url += '&limit=' + $('#limit').val();
	location = url;
}
</script>