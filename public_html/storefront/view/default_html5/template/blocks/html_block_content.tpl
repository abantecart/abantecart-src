<?php if ( $block_framed ) { ?>
<div class="c_block">
	<div class="block_tl">
		<div class="block_tr">
			<div class="block_tc"><?php echo $heading_title; ?></div>
		</div>
	</div>
    <div class="block_cl">
    	<div class="block_cr">
        	<div class="block_cc">
<?php } ?>
<?php echo  $content; ?>
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