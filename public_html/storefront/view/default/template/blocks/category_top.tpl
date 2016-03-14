<section id="categorymenu">
<h4 class="hidden">&nbsp;</h4>
    <nav class="subnav">
    	<ul class="nav-pills categorymenu">
    		<li><a class="active menu_home" href="<?php echo $home_href; ?>"><?php echo $text_home; ?></a>

    			<div>
    				<ul id="main_menu" class="nav">
    					<?php
    					$storefront_menu = (array)$this->session->data['storefront_menu'];
    					foreach ($storefront_menu as $i => $menu_item) {
    						if ($menu_item['id'] == 'home') {
    							unset($storefront_menu[$i]);
    							break;
    						}
    					}?>
    					<!-- Top Nav Start -->
    					<?php echo  buildStoreFrontMenuTree($storefront_menu); ?>
    				</ul>
    			</div>
    		</li>
    		<?php if ($categories) { ?>  		
    			<?php foreach ($categories as $category) {
    					if ($category['current']) {
    						$category['current'] = ' class="current" '; 
    					}	
    			?>
    				<li <?php echo $category['current']; ?>><a href="<?php echo $category['href']; ?>">&nbsp;&nbsp;<?php echo $category['name']; ?></a>
    					<?php $sub_cat = $category['children']; ?>
    					<?php if ($sub_cat) { ?>
    						<!-- Subcategories -->
    						<div class="subcategories">
    							<ul>
    								<?php foreach ($sub_cat as $scat) {
   										if ($scat['current']) {
    										$scat['current'] = ' class="current" '; 
    									}	
    									$width = $this->config->get('config_image_category_width');
    									$height = $this->config->get('config_image_category_height');
    								?>
     									<li <?php echo $scat['current']; ?>><a href="<?php echo $scat['href']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $scat['name']; ?></a>
    									<img class="sub_cat_image"
									         style="display:none; width: <?php echo $width; ?>px; height: <?php echo $height; ?>px;"
									         src="<?php echo $scat['thumb']; ?>"
    									     alt="<?php echo $scat['name']; ?>"
    									     title="<?php echo $scat['name']; ?>"
    									     width="<?php echo $width; ?>"
    									     height="<?php echo $height; ?>">
    									</li>
    								<?php } ?>
    							</ul>
    							<?php if ($category['thumb']) { ?>
    								<ul>
    									<li class="parent_cat_image" style="display:none">
										    <img class="root_cat_image"
										         style="display:block;  width: <?php echo $width; ?>px; height: <?php echo $height; ?>px;"
										         src="<?php echo $category['thumb']; ?>"
    											 alt="<?php echo $category['name']; ?>"
    											 title="<?php echo $category['name']; ?>"
	    									     width="<?php echo $width; ?>"
	    									     height="<?php echo $height; ?>">
    									</li>
    									<li class="cat_image">
										    <img class="root_cat_image"
										         style="display:block;  width: <?php echo $width; ?>px; height: <?php echo $height; ?>px;"
										         src="<?php echo $category['thumb']; ?>"
    											 alt="<?php echo $category['name']; ?>"
    											 title="<?php echo $category['name']; ?>"
	    									     width="<?php echo $width; ?>"
	    									     height="<?php echo $height; ?>">
    									</li>
    								</ul>
    							<?php } ?>
    						</div>
    					<?php } ?>
    				</li>
    			<?php } ?>
    		<?php } ?>
    	</ul>
    </nav>
</section>