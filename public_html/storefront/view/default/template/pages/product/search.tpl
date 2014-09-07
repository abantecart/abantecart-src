<h1 class="heading1">
  <span class="maintext"><i class="icon-search"></i> <?php echo $heading_title; ?></span>
  <span class="subtext"></span>
</h1>

<div class="container-fluid">

	<h4 class="heading4"><?php echo $text_critea; ?></h4>
	<div class="form-inline">
		<fieldset>
			<div class="control-group">
				<div class="controls">
				    <?php echo $keyword . $category; ?>&nbsp;
				    <?php echo $description; ?>&nbsp;
				    <?php echo $model; ?>&nbsp;
				    <?php echo $submit; ?>
				</div>
			</div>		
		</fieldset>
	</div>
			
	<h4 class="heading4"><?php echo $text_search; ?></h4>
	<?php if ($products) { ?>
	<!-- Sorting + pagination-->
	<div class="sorting well">
	  <form class=" form-inline pull-left">
	    <?php echo $text_sort; ?>&nbsp;&nbsp;<?php echo $sorting; ?>
	  </form>
	  <div class="btn-group pull-right">
	    <button class="btn" id="list"><i class="icon-th-list"></i>
	    </button>
	    <button class="btn btn-orange" id="grid"><i class="icon-th icon-white"></i></button>
	  </div>
	</div>
	<!-- end sorting-->

	<?php include( $this->templateResource('/template/pages/product/product_listing.tpl') ) ?>
		
	<!-- Sorting + pagination-->
	<div class="sorting well">
		<?php echo $pagination_bootstrap; ?>
		<div class="btn-group pull-right">
		</div>
	</div>
	<!-- end sorting-->
				
	
<?php } else { ?>
		<div>
			<?php echo $text_empty; ?>
		</div>
<?php } ?>		

</div>

<script type="text/javascript"><!--
$('#keyword').keydown(function (e) {
	if (e.keyCode == 13) {
		contentSearch();
	}
});
$('#search_button').click(function (e) {
	contentSearch();
});

$('#sort').change(function () {
	contentSearch();
});

function contentSearch() {
	url = 'index.php?rt=product/search&limit=<?php echo $limit; ?>';

	var keyword = $('#keyword').attr('value');

	if (keyword) {
		url += '&keyword=' + encodeURIComponent(keyword);
	}

	var category_id = $('#category_id').attr('value');

	if (category_id) {
		url += '&category_id=' + encodeURIComponent(category_id);
	}

	if ($('#description').is(':checked')) {
		url += '&description=1';
	}

	if ($('#model').is(':checked')) {
		url += '&model=1';
	}
	url += '&sort=' + $('#sort').val();

	location = url;
}
//--></script>