<?php if ($error) { ?>
<div class="warning alert alert-error"><?php echo $error; ?></div>
<?php } ?>

<div id="content">
<div class="product_info">

<div class="content_block">
	<div class="top">
		<div class="left"></div>
		<div class="right"></div>
		<div class="center"></div>
	</div>
	<div class="middle">
		<div class="product_options">
			<div class="top">
				<div class="left"></div>
				<div class="right"></div>
				<div class="center"></div>
			</div>
			<div class="middle">
				<div class="price-wrapper">
					<?php if ($display_price) { ?>
					<?php if (!$special) { ?>
						<span class="price"><?php echo $price; ?></span>
						<?php } else { ?>
						<span class="old_price"><?php echo $price; ?></span>
						<span class="special_price"><?php echo $special; ?></span>
						<?php }
					}?>

					<?php echo $this->getHookVar('extended_product_options'); ?>

				</div>
				<div class="flt_right">
				<?php if (!$product_info['call_to_order']){ ?>
					<a class="btn_standard" href="javascript:window.print();"><span
							class="button2"><span><img src="<?php echo $this->templateResource('/image/icon_print.png'); ?>"
													   alt="print" /><?php echo $button_print; ?></span></span></a>

				<?php }else{?>
					<a href="#" class="btn_standard call_to_order"><span class="button2"><span><?php echo $text_call_to_order; ?></span></span></a>
				<?php }?>
				</div>
				<br class="clr_both"/>
				<div class="separator"></div>
				<?php if ($display_price) { ?>
					<?php echo $form['form_open'];

				  if ($options) { ?>
					<table style="width: 100%;">
						<?php foreach ($options as $option) { ?>
						<tr>
							<td>
								<div class="option_title"><?php echo $option[ 'name' ]; ?>:</div>
								<?php echo $option[ 'html' ]; ?>
							</td>
						</tr>
						<tr>
							<td>
								<div class="separator"></div>
							</td>
						</tr>
						<?php } ?>
					</table>
					<?php } ?>

					<?php if ($display_price) { ?>
					<?php if ($discounts) { ?>
						<b><?php echo $text_discount; ?></b><br/>
						<table style="width: 100%;">
							<tr>
								<td style="text-align: right;"><b><?php echo $text_order_quantity; ?></b></td>
								<td style="text-align: right;"><b><?php echo $text_price_per_item; ?></b></td>
							</tr>
							<?php foreach ($discounts as $discount) { ?>
							<tr>
								<td style="text-align: right;"><?php echo $discount[ 'quantity' ]; ?></td>
								<td style="text-align: right;"><?php echo $discount[ 'price' ]; ?></td>
							</tr>
							<?php } ?>
						</table>
						<div class="separator"></div>
						<?php } ?>
					<?php } ?>

					<table cellspacing="0" cellpadding="0" width="100%">
						<?php if($form['minimum']){ ?>
						<tr><td><span style="float: left; margin-top: 3px;"><?php echo $text_qty;?></span><?php echo $form['minimum']; ?></td>
							<td align="right"><?php echo $form['add_to_cart'];?></td>
						</tr>
						<?php  if ($minimum > 1) { ?>
						<tr>
							<td colspan="2">
								<small><?php echo $text_minimum; ?></small>
							</td>
						</tr>
						<?php } ?>
						<?php if ($maximum > 0) { ?>
						<tr>
							<td colspan="2">
								<small><?php echo $text_maximum; ?></small>
							</td>
						</tr><?php }
						} ?>
						<?php echo $this->getHookVar('buttons'); ?>
					</table>

					<div>
						<?php echo $form['product_id'].$form['redirect']; ?>
					</div>
				</form>
				<?php } ?>

			</div>
			<div class="bottom">
				<div class="left"></div>
				<div class="right"></div>
				<div class="center"></div>
			</div>
		</div>
		<div class="middle_content">
			<h1 class="product_title"><?php echo $heading_title; ?></h1>

			<div class="product_images">
				<div class="main_image">
					<?php if($image_main['origin']=='internal'){ ?>
						  <a href="<?php echo $image_main[ 'main_url' ]; ?>" title="<?php echo $image_main[ 'title' ]; ?>" class="thickbox" rel="gallery">
					<?php }
						echo $image_main[ 'thumb_html'];
						if($image_main['origin']=='internal'){ ?>
						</a>
					<?php }?>
					
				</div>
			<?php if ($images) { ?>
				<div class="additional_images">
			<?php	if (sizeof($images) > 1) {
						foreach ($images as $image) {
							?>
							<div>
								<?php if($image['origin']=='internal'){ ?>
								<a href="<?php echo $image[ 'main_url' ]; ?>" title="<?php echo $image[ 'title' ]; ?>" class="thickbox" rel="gallery">
								<?php }
								echo $image[ 'thumb_html'];
								if($image['origin']=='internal'){ ?>
								</a>
								<?php }?>
							</div>
							<?php }
					} ?>
				</div>
				<?php } ?>
				<br class="clr_both"/>
				<?php if($image_main){ ?>
				<div class="enlarge"><span><?php echo $text_enlarge; ?></span></div>
				<?php } ?>
			</div>
			<div class="product_overview">
				<table cellpadding="3">
					<?php if($stock){ ?>
					<tr>
						<td><b><?php echo $text_availability; ?></b></td>
						<td><?php echo $stock; ?></td>
					</tr>
					<?php } ?>
					<?php if($model){ ?>
					<tr>
						<td><b><?php echo $text_model; ?></b></td>
						<td><?php echo $model; ?></td>
					</tr>
					<?php } ?>
					<?php if ($manufacturer) { ?>
					<tr>
						<td><b><?php echo $text_manufacturer; ?></b></td>
						<td>
							<a href="<?php echo $manufacturers; ?>">
		                    <?php if ( $manufacturer_icon ) { ?>  
		                    <img src="<?php echo $manufacturer_icon; ?>" title="<?php echo $manufacturer; ?>" border="0"/>
	    	                <?php } else { echo $manufacturer; }  ?> 
							</a>
						</td>
					</tr>
					<?php } ?>
					<?php if ($review_status) { ?>
					<tr>
						<td><b><?php echo $text_average; ?></b></td>
						<td><?php if ($average) { ?>
							<img src="<?php echo $this->templateResource('/image/stars_' . $average . '.png'); ?>"
							     alt="<?php echo $text_stars; ?>" style="margin-top: 2px;"/>
							<?php } else { ?>
							<?php echo $text_no_rating; ?>
							<?php } ?></td>
					</tr>
					<?php } ?>
					<?php if ($tags) { ?>
					<tr>
						<td><b><?php echo $text_tags; ?></b></td>
						<td>
							<div class="tags">
								<?php foreach ($tags as $tag) { ?>
								<a href="<?php echo $tag[ 'href' ]; ?>"><?php echo $tag[ 'tag' ]; ?></a>&nbsp;
								<?php } ?>
							</div>
						</td>
					</tr>
					<?php } ?>
				</table>
			</div>
			<br class="clr_both"/>
		</div>
		<br class="clr_both"/>
	</div>
	<div class="bottom">
		<div class="left"></div>
		<div class="right"></div>
		<div class="center"></div>
	</div>
</div>

<div class="tabs">
	<a rel="#tab_description">
		<span class="tab_right"></span>
		<span class="tab_left"></span>
		<span class="tab_text">
			<?php echo $tab_description; ?>
		</span>
	</a>
	<?php if ($review_status) { ?>
		<a rel="#tab_review">
			<span class="tab_right"></span>
			<span class="tab_left"></span>
			<span class="tab_text">
				<?php echo $tab_review; ?>
			</span>
		</a>
	<?php } ?>
	<a rel="#tab_related">
		<span class="tab_right"></span>
		<span class="tab_left"></span>
		<span class="tab_text">
			<?php echo $tab_related; ?>
			(<?php echo count($related_products); ?>)
		</span>

	</a>
	<?php if ($downloads) { ?>
			<a rel="#tab_download">
				<span class="tab_right"></span>
				<span class="tab_left"></span>
				<span class="tab_text">
					<?php echo $tab_downloads; ?>
				</span>
			</a>
		<?php } ?>

	<?php echo $this->getHookVar('product_features_tab'); ?>

</div>
<div class="content_block tab_block">
	<div class="top">
		<div class="left"></div>
		<div class="right"></div>
		<div class="center"></div>
	</div>
	<div class="middle">
		<div class="middle_content">

			<div id="tab_description" class="tab_page"><?php echo $description; ?></div>
			<?php if ($review_status) { ?>
			<div id="tab_review" class="tab_page">
				<div id="review"></div>
				<div class="heading" id="review_title"><?php echo $text_write; ?></div>
				<div class="content"><b><?php echo $entry_name; ?></b><br/>
					<?php echo $review_name; ?>
					<br/>
					<br/>
					<b><?php echo $entry_review; ?></b><br/>
					<?php echo $review_text; ?>
					<div style="font-size: 11px; clear:both;"><?php echo $text_note; ?></div><br/>
					<br/>
					<b class="flt_left" style="margin-right: 10px;"><?php echo $entry_rating; ?></b><?php echo $rating_element; ?><br class="clr_both"/>
					<br/>
					<b><?php echo $entry_captcha; ?></b><br/>
					<?php echo $review_captcha; ?>
					<br/><br/>
					<img src="index.php?rt=common/captcha" id="captcha_img" alt="" /></div>
				<div class="buttons">
					<table>
						<tr>
							<td align="right"><?php echo $review_button; ?></td>
						</tr>
					</table>
				</div>
			</div>
			<?php } ?>
			<div id="tab_related" class="tab_page">
			<?php if ($related_products) {
				foreach ($related_products as $related_product){ ?>

					<div class="related_product">
						<a href="<?php echo $related_product[ 'href' ]; ?>"><?php echo $related_product['image'][ 'thumb_html' ] ?></a><br/>
						<a href="<?php echo $related_product[ 'href' ]; ?>"><?php echo $related_product[ 'name' ]; ?></a><br/>
						<span style="color: #999; font-size: 11px;"><?php echo $related_product[ 'model' ]; ?></span><br/>

							<div class="price-add">
							<?php if ($display_price) { ?>
							<?php if (!$related_product[ 'special' ]) { ?>
								<span style="color: #900; font-weight: bold;"><?php echo $related_product[ 'price' ]; ?></span>
								<?php } else { ?>
								<span style="color: #900; font-weight: bold; text-decoration: line-through;"><?php echo $related_product[ 'price' ]; ?></span>
								<span style="color: #F00;"><?php echo $related_product[ 'special' ]; ?></span>
								<?php } ?>
							<?php } ?>

							<?php if(!$related_product['call_to_order']){ ?>
								<a class="buy" id="<?php echo $related_product['product_id']?>" href="<?php echo $related_product[ 'add' ]?>" title="<?php echo $button_add_to_cart; ?>"></a>
							<?php }else{ ?>
								<a href="#" class="call_to_order"><span class="price"><?php echo $text_call_to_order;?></span></a>
							</div>
							<?php }?>


						<br/>
						<?php if ($related_product[ 'rating' ]) { ?>
						<img src="<?php echo $this->templateResource('/image/stars_' . $related_product[ 'rating' ] . '.png'); ?>"
						     alt="<?php echo $related_product[ 'stars' ]; ?>"/>
						<?php } ?>
					</div>

					<?php } ?>
				<br class="clr_both"/>

				<?php } else { ?>
				<div style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;"><?php echo $text_no_related; ?></div>
				<?php } ?>
			</div>

			<?php if ($downloads) { ?>
				<div id="tab_download" class="tab_page">
				<div class="tab-pane" id="productdownloads">
					<ul class="downloads">
						<?php foreach ($downloads as $download) { ?>
						<li class="row">
							<div class="pull-left"><?php echo $download['name']; ?><div class="download-list-attributes">
							<?php foreach($download['attributes'] as $name=>$value){
								echo '<small>- '.$name.': '.(is_array($value) ? implode(' ',$value) : $value).'</small>';
								}?></div>
							</div>
							<div style="float:right;"><?php echo $download['href']; ?></div>
						</li>
							<?php } ?>
					</ul>
				</div>
			<?php } ?>

			<?php echo $this->getHookVar('product_features'); ?>

		</div>
	</div>
	<div class="bottom">
		<div class="left"></div>
		<div class="right"></div>
		<div class="center"></div>
	</div>
</div>

</div>
</div>

<script type="text/javascript"><!--

jQuery(function($) {
	$.tabs('.tabs a');
	$('#review .pagination a').live('click', function() {
		$('#review').slideUp('slow');
		$('#review').load(this.href);
		$('#review').slideDown('slow');
		return false;
	});
	$('div.main_image a').click(function() {
		$('div.additional_images a').first().click();
		return false;
	});

	$('#review').load('index.php?rt=product/review/review&product_id=<?php echo $product_id; ?>');

});

function review() {
	$.ajax({
		type: 'POST',
		url: 'index.php?rt=product/review/write&product_id=<?php echo $product_id; ?>',
		dataType: 'json',
		data: 'name=' + encodeURIComponent($('input[name=\'name\']').val()) + '&text=' + encodeURIComponent($('textarea[name=\'text\']').val()) + '&rating=' + encodeURIComponent($('input[name=\'rating\']:checked').val() ? $('input[name=\'rating\']:checked').val() : '') + '&captcha=' + encodeURIComponent($('input[name=\'captcha\']').val()),
		beforeSend: function() {
			$('.success, .warning').remove();
			$('#review_button').attr('disabled', 'disabled');
			$('#review_title').after('<div class="wait"><img src="<?php echo $this->templateResource('/image/loading_1.gif'); ?>" alt="" /> <?php echo $text_wait; ?></div>');
		},
		complete: function() {
			$('#review_button').attr('disabled', '');
			$('.wait').remove();
		},
		success: function(data) {
			if (data.error) {
				$('#review_title').after('<div class="warning alert alert-error">' + data.error + '</div>');
			}
			if (data.success) {
				$('#review_title').after('<div class="success alert alert-success">' + data.success + '</div>');

				$('input[name=\'name\']').val('');
				$('textarea[name=\'text\']').val('');
				$('input[name=\'rating\']:checked').attr('checked', '');
				$('input[name=\'captcha\']').val('');
			}
			$('img#captcha_img').attr('src',$('img#captcha_img').attr('src')+'&'+Math.random());
		}
	});
}
$('#product_add_to_cart').click(function(){ $('#product').submit();});
$('#review_submit').click(function(){ review();})

var base_img = $('div.main_image').html();
var base_imgs = $('div.additional_images').html();

$('input[name^=\'option\'], select[name^=\'option\']').click(function(){
		$.ajax({
		type: 'POST',
		url: 'index.php?rt=r/product/product/get_option_resources&attribute_value_id='+$(this).val(),
		dataType: 'json',

		success: function(data) {
			var html='';
			if (data.main) {
				if(data.main.origin=='internal'){
					html = '<a href="'+data.main.main_url+'" class="thickbox"  rel="gallery" id="image">'+data.main.thumb_html+'</a>';
				}else{
					html = data.main.thumb_html;
				}
			}else{
				html = base_img;
			}
			$('div.main_image').html(html);
			tb_init('div.main_image a.thickbox');

			if(data.images){
				html = '';
				for(img in data.images){
					if(data.images[img].origin=='internal'){
						//var src = data.images[img].main_url ? data.images[img].main_url : data.images[img].thumb_url
						html += '<div><a href="'+data.images[img].main_url+'" class="thickbox" rel="gallery">'+data.images[img].thumb_html+'</a></div>';
					}else{
						html = data.images[img].thumb_html;
					}
				}
			}else{			
				html = base_imgs;
			}
			$('div.additional_images').html(html);
			tb_init('div.additional_images a.thickbox');
		}
	});
});
//--></script>