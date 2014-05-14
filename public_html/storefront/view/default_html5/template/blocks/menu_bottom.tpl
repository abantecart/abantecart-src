<div class="navbar" id="footernav">
    <div class="navbar-inner">
		<ul id="main_menu_bottom" class="nav main_menu">
			<?php 
				//NOTE:
				//HTML tree builded in helper/html.php
				//To controll look and style of the menu use CSS in styles.css
			?>
		    <?php echo buildStoreFrontMenuTree( $storemenu ); ?>
		</ul>
    </div>
</div>