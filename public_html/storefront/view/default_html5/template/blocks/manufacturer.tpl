<div class="side_block">
<?php if ( $block_framed ) { ?>
	<h2><?php echo $heading_title; ?></h2>
<?php } ?>
            	<div class="brands">
            		<?php if ($product_id) { ?>  
            			<center>
	                    <a href="<?php echo $manufacturer['href']; ?>">
	                    <?php if ( $manufacturer['icon'] ) { ?>  
	                    <img src="<?php echo $manufacturer['icon']; ?>" title="<?php echo $manufacturer['name']; ?>" border="0"/>	                    
	                    <?php } else { echo $manufacturer['name']; }  ?> 
	                    </a>
	                    </center>
                    <?php } else { ?>        	
                    	<ul>
	                    <?php foreach ($manufacturers as $manufacturer) { ?>
	                    <?php if ($manufacturer['manufacturer_id'] == $manufacturer_id) { ?>
	                    <li><a href="<?php echo $manufacturer['href']; ?>"><b><?php echo $manufacturer['name']; ?></b></a></li>
	                    <?php } else { ?>
	                    <li><a href="<?php echo $manufacturer['href']; ?>"><?php echo $manufacturer['name']; ?></a></li>
	                    <?php } ?>
	                    <?php } ?>
	                	</ul>    
                    <?php } ?>
                </div>

</div>
