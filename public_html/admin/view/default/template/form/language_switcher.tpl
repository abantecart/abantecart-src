<?php
 if ($languages) {
 	$cur_lang = array();
 	foreach ($languages as $language) {
		if ($language['code'] == $language_code) {
			$cur_lang = $language;
			break;
		} 
	} 
?>
	<div class="btn-group tooltips content_language" data-original-title="<?php echo $cur_lang['name']; ?>">
	    <button class="btn btn-default btn-xs dropdown-toggle tp-icon" data-toggle="dropdown">
			  <?php if($cur_lang['image']){  ?>
			  <img src="<?php echo $cur_lang['image']; ?>" title="<?php echo $cur_lang['name']; ?>" />
			  <?php } else { ?>
			  <i class="fa fa-language"></i>
			  <?php } ?>
	      <span class="caret"></span>
	    </button>
	  	<div class="dropdown-menu dropdown-menu-sm pull-right switcher">
	  		<h5 class="title"><?php echo $cur_lang['name']; ?></h5>
			<form method="get" id="content_language_form">
	    		<ul class="dropdown-list dropdown-list-sm">
	    			<?php foreach ($languages as $language) { ?>
	    				<li>
	    					<a onClick="$('input[name=\'content_language_code\']').attr('value', '<?php echo $language['code']; ?>'); $('#content_language_form').submit();">
	    						<?php if ($language['image']) { ?>
	    							<img src="<?php echo $language['image']; ?>"
	    								 title="<?php echo $language['name']; ?>"/>
	    						<?php
	    						} else {
	    							echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	    						} ?>
	    						&nbsp;&nbsp;<span><?php echo $language['name']; ?></span>
	    					</a>
	    				</li>
	    			<?php } ?>
	    		</ul>
	    		<input type="hidden" name="content_language_code" value=""/>
	      		<?php foreach($hiddens as $name => $value){   ?>
	            	<input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>"/>
				<?php }?>
	    	</form>
	    </div>
	</div>
<?php } ?>