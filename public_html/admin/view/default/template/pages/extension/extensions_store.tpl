<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div id="content" class="tab-content">

	<div class="panel-heading">

		<div class="btn-group">
		    <a href="<?php echo $my_extensions; ?>" class="btn btn-default" id="btn_my_exts">
		    <i class="fa fa-tags fa-fw"></i>
		    <?php echo $text_my_extensions; ?>
		    </a>
		</div>		

		<div class="btn-group"> 
		    <a href="<?php echo $my_account; ?>" target="_blank" class="btn btn-default" id="btn_my_account">
		    <i class="fa fa-user fa-fw"></i>
		    <?php echo $text_my_account; ?>
		    </a>
		</div>		

		<?php 
		if($content){
		    $current_categ = $text_all_categories;
		    foreach ($content['categories']['subcategories'] as $category) {
		    	if ($category['active']) {
		    		$current_categ = $category['name'];
		    	}
		}
		?>
		<div class="btn-group">
		  <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
		    <i class="fa fa-folder-o"></i>
		     <?php echo $current_categ; ?> <span class="caret"></span>
		  </button>
		  <ul class="dropdown-menu">
		    <?php foreach ($content['categories']['subcategories'] as $category) { ?>
		    		<li class="<?php echo $category['active'] ? 'disabled' : '' ?>">
		    			<a href="<?php echo $category['href'] ?>"
		    			   title="<?php echo trim($category['description']) ?>"><?php echo $category['name'] ?></a>
		    		</li>
		    <?php } ?>
		  </ul>
		</div>
		
		<div class="btn-group form-inline">
		    <?php echo $form['form_open']; ?>
		    <div class="form-group">
		    	<div class="input-group">
		    	<?php echo $form['input']; ?>
		    	</div>
		    	<button type="submit" class="btn btn-primary"><?php echo $button_go; ?></button>
		    </div>
		    </form>
		</div>
		<?php } ?>
		
		<div class="btn-group pull-right">
		    <a class="btn btn-white tooltips" href="https://marketplace.abantecart.com" target="new" data-toggle="tooltip"
		        data-original-title="<?php echo $text_marketplace_site; ?>">
		        <i class="fa fa-external-link fa-lg"></i>
		    </a>
		</div>
		
	    <?php if (!empty ($help_url)) { ?>
		<div class="btn-group pull-right">
		    	<a class="btn btn-white tooltips" href="<?php echo $help_url; ?>" target="new" data-toggle="tooltip"
		    	   title="" data-original-title="Help">
		    		<i class="fa fa-question-circle fa-lg"></i>
		    	</a>
		</div>
	    <?php } ?>

	</div>
	
	<div class="panel-body panel-body-nopadding">
	<?php if($content){	?>
	    <ul class="thumbnails">
	        <?php
	        if ($content['products']['rows']) {
	        	foreach ($content['products']['rows'] as $product) {
	        	
	        		$item = array();	
	        		$item['image'] = $product['cell']['thumb'];
	        		$item['main_image'] = $product['cell']['main_image'];
	        		$item['title'] = $product['cell']['name'];
	        		$item['description'] = $product['cell']['model'];
	        		$item['rating'] = "<img src='" . $this->templateResource('/image/stars_' . (int)$product['cell']['rating'] . '.png') . "' alt='" . (int)$product['stars'] . "' />";
	    
	        		$item['price'] = $product['cell']['price'];
	        		if ( substr( $product['cell']['price'],1) == '0.00' ) {
	        			$item['price'] = 'FREE';
	        		}
	    
	        		if ($item['rating']) {
	        			$review = $item['rating'];
	        		}
	    
	        		?>
	        		<li class="product-item col-md-4" data-product-id="<?php echo $product['id'] ?>">
	        			<div class="ext_thumbnail">
	        				<a class="product_thumb" title='' data-html="true" rel="tooltip">
	        				<img width="57" alt="" src="<?php echo $item['image'] ?>">
	        				</a>
	        				<div class="tooltip-data hidden" style="display: none;">
	        				<div class="product_data">
	        					<span class="prdocut_title" title="<?php echo $item['title'] ?>"><?php echo $item['title'] ?></span>
	        					<span class="review"><?php echo $review ?></span>
	        					<span class="price">
	        					    <span class="oneprice"><?php echo $item['price'] ?></span>
	        					</span>	
	        				</div>			
	        				<div class="product_image">	
	        					<img src="<?php echo $this->templateResource('/image/loading_row.gif'); ?>" class="load_ondemand" data-src="<?php echo $item['main_image'] ?>">
	        				</div>	
	        				</div>			
	        			</div>
	        			<div class="ext_details">
	        				<div class="ext_name">
	        					<div class="text_zoom">
	        					<a title="<?php echo $item['title']; ?>"><?php echo $item['title'] ?></a>
	        					</div>
	        				</div>
	    
	        				<div class="ext_more">
	        					<div class="ext_review"><a class="compare"><?php echo $review ?></a></div>
	        					<div class="ext_price">
	        					    <div class="oneprice"><?php echo $item['price'] ?></div>
	        					</div>
	    
	        					<div class="ext_icons">
	        						<a class="productcart" data-id="<?php echo $product['product_id'] ?>">
	        						<i class="icon-shopping-cart"></i>
	        						</a>
	        					</div>
	        				</div>
	        			</div>				
	        		</li>
	        	<?php
	        	}
	        }
	        ?>
	    </ul>					
	    		
	    <?php if( $sorting && $pagination_bootstrap ) { ?>
	    <div class="container-fluid mt10">
	    	<div class="col-md-4 form-inline pull-left">
				<div class="form-group">
					<div class="input-group input-group-sm">
					<?php echo $sorting; ?>
					</div>
				</div>	
			</div>	
			<div class="form-inline pull-right">
	        <?php echo $pagination_bootstrap; ?>
	        </div>
	    </div>
	    <?php }?>
	<?php } else { ?>
	    <div class="cbox_cc" style="overflow: hidden;">
	    <div class="warning alert alert-danger"><?php echo $error_mp_connection; ?></div>
	    </div>
	<?php } ?>
	</div>
	
</div>

<script type="text/javascript">
	$("#sorting").change(function () {
		location = '<?php echo $listing_url?>&' + $(this).val();
	});

	$('a.productcart, a.product_thumb, .ext_name a').click(function(){
		var product_id = $(this).parents('li.product-item').attr('data-product-id');
		if(!product_id) return false;
		window.open('<?php echo $remote_store_product_url;?>&rt=product/product&product_id=' + product_id, 'MPside');
		return false;
	});

	$('a.productcart').click(function(){
		var product_id = $(this).parents('li.product-item').attr('data-product-id');
		if(!product_id) return false;
		window.open('<?php echo $remote_store_product_url;?>&rt=checkout/cart&product_id=' + product_id, 'MPside');
		return false;
	});

	$('.ext_review a').click(function () {
		var product_id = $(this).parents('li.product-item').attr('data-product-id');
		if(!product_id) return false;
		window.open('<?php echo $remote_store_product_url;?>&rt=product/product/reviews&product_id=' + product_id, 'MPside');
		return false;
	});

	//tooltip for products
	$('.ext_thumbnail').tooltip({
  		selector: "a[rel=tooltip]",
  		placement: 'auto',
  		animation: false,
        title: function() {
          var tooltipdata = $(this).parent().find('.tooltip-data');
          var img_src = tooltipdata.find('.load_ondemand').attr('data-src');
          tooltipdata.find('.load_ondemand').attr('src',img_src);
          return tooltipdata.html();
        }
	})
	
</script>