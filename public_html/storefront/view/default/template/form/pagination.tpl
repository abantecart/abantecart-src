<?php if ($total > 0 ) { ?>

<form class="form-inline pull-left">
<?php echo $text_limit ?>&nbsp;&nbsp;
<?php echo $limit_select ?>&nbsp;&nbsp;
<?php echo $text ?>
</form>

<div class="pull-right">
  <ul class="pagination">  
  	<?php if ($page > 1) { ?>
    	<li><a href="<?php echo $first_url; ?>"><?php echo $text_first; ?></a></li>  	
    	<li><a href="<?php echo $prev_url; ?>"><?php echo $text_prev; ?></a></li>  	
  	<?php } ?>
  	<?php 
  	for ($i = $start; $i <= $end; $i++) {
  			if ($page == $i) {
  	?> 
    	<li class="disabled"><a><?php echo $i ?></a></li>
  	<?php 	} else { ?>  	
    	<li><a href="<?php echo str_replace('{page}', $i, $url) ?>"><?php echo $i ?></a></li>
  	<?php 	}
  		} ?> 
  	<?php if ($page < $total_pages) { ?>
    	<li><a href="<?php echo $next_url; ?>"><?php echo $text_next; ?></a></li>  	
    	<li><a href="<?php echo $last_url; ?>"><?php echo $text_last; ?></a></li>  	
  	<?php } ?>
  </ul>	
</div>

<?php } ?>