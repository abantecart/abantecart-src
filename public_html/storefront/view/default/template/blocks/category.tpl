<?php if ( $block_framed ) { ?>
<div class="s_block">
	<div class="block_tl">
		<div class="block_tr">
			<div class="block_tc"><img src="<?php echo $this->templateResource('/image/category.png'); ?>" alt="" /><?php echo $heading_title; ?></div>
		</div>
	</div>
    <div class="block_cl">
    	<div class="block_cr">
        	<div class="block_cc">
<?php } ?>
            	<div class="category_list">
		            <ul>
		            <?php
		            foreach($category_list as $item){
			            $cname = $item['category_id']==$selected_category_id ? '<b>'.$item['name'].'</b>' : $item['name'];
			         echo '<li>'.str_repeat('&nbsp;&nbsp;&nbsp;',$item['level']).'<a href="'.$item['href'].'">'.$cname.'</a></li>';
		            }?>
	            </ul>
	            </div>
<?php if ( $block_framed ) { ?>
            </div>
        </div>
    </div>
	<div class="block_bl">
		<div class="block_br">
			<div class="block_bc">&nbsp;</div>
		</div>
	</div>
</div>
<?php } ?>