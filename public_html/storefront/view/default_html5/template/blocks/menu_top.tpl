<div class="navbar" id="topnav">
    <div class="navbar-inner">
		<ul id="main_menu" class="nav">
		<?php
			foreach ($storemenu as $i => $menu_item) {
		 	   if ($menu_item['id'] == 'home') {
		    		unset($storemenu[$i]);
		    		break;
		    	}
			}?>
		    <?php echo renderStoreMenu( $storemenu ); ?>
		</ul>
    </div>
</div>
