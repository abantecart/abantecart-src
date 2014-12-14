<h5 class="sidebartitle"><?php echo $text_short_stats; ?></h5>    
<ul>
    <li>
        <div class="icon"><i class="fa fa-users fa-2x fa-fw"></i></div>   
        <div class="stats_info">
            <span class="text-muted"><?php echo $text_online_now; ?></span>
            <h4>
            	<?php echo $text_online_registered; ?>: <?php echo $online_registered; ?>
            	</br>	
            	<?php echo $text_online_new; ?>: <?php echo $online_new; ?>
            </h4>
        </div>
    </li>
    <li>
		<div class="icon"><i class="fa fa-credit-card fa-2x fa-fw"></i></div>   
        <div class="stats_info">
            <span class="text-muted"><?php echo $text_today_orders; ?></span>
            <h4><?php echo $today_order_count; ?></h4>
        </div>
          
    </li>
    <li>
    	<div class="icon"><i class="fa fa-money fa-2x fa-fw"></i></div> 
        <div class="stats_info">
            <span class="text-muted"><?php echo $text_today_sales; ?></span>
            <h4><?php echo $today_sales_amount; ?></h4>
        </div>
    </li>
    <li>
    	<div class="icon"><i class="fa fa-user fa-2x fa-fw"></i></div> 
        <div class="stats_info">
            <span class="text-muted"><?php echo $text_today_customers; ?></span>
            <h4><?php echo $today_customer_count; ?></h4>
        </div>
    </li>
    <li>
    	<div class="icon"><i class="fa fa-comment fa-2x fa-fw"></i></div> 
        <div class="stats_info">
            <span class="text-muted"><?php echo $text_today_reviews; ?></span>
            <h4><?php echo $today_review_count; ?></h4>
        </div>
    </li>
    <?php echo $this->getHookVar('side_summary_li'); ?>
</ul>
<?php echo $this->getHookVar('side_summary_bottom'); ?>
