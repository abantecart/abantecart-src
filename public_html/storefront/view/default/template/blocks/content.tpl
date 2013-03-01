<div class="s_block">
	<div class="block_tl">
		<div class="block_tr">
			<div class="block_tc"><img src="<?php echo $this->templateResource('/image/information.png'); ?>" alt="" /><?php echo $heading_title; ?></div>
		</div>
	</div>
    <div class="block_cl">
    	<div class="block_cr">
        	<div class="block_cc">
            	<div class="information"><ul>
					<?php echo $this->getHookVar('pre_contents'); ?>
                	<?php foreach ($contents as $content) { ?>
                		<li><a href="<?php echo $content['href']; ?>"><?php echo $content['title']; ?></a></li>
                	<?php } ?>
					<?php echo $this->getHookVar('post_contents'); ?>
                	<li><a href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a></li>
                	<li><a href="<?php echo $sitemap; ?>"><?php echo $text_sitemap; ?></a></li>
                </ul></div>
            </div>
        </div>
    </div>
	<div class="block_bl">
		<div class="block_br">
			<div class="block_bc">&nbsp;</div>
		</div>
	</div>
</div>