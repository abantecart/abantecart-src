<a class="sidebartitle w-100" data-toggle="collapse" href="#short_stats_collapse" aria-expanded="true" aria-controls="short_stats_collapse">
    <i class="fa fa-th"></i>
    <?php echo $text_short_stats; ?>
</a>
<ul class="collapse" id="short_stats_collapse">
    <?php if ($viewcustomer) { ?>
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
    <?php }
    if ($vieworder) { ?>
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
    <?php }
    if ($viewcustomer) { ?>
    <li>
    	<div class="icon"><i class="fa fa-user fa-2x fa-fw"></i></div> 
        <div class="stats_info">
            <span class="text-muted"><?php echo $text_today_customers; ?></span>
            <h4><?php echo $today_customer_count; ?></h4>
        </div>
    </li>
    <?php }
    if ($viewreview) { ?>
    <li>
    	<div class="icon"><i class="fa fa-comment fa-2x fa-fw"></i></div> 
        <div class="stats_info">
            <span class="text-muted"><?php echo $text_today_reviews; ?></span>
            <h4><?php echo $today_review_count; ?></h4>
        </div>
    </li>
    <?php } ?>
    <li>
    	<div class="icon"><i class="fa fa-clock-o fa-2x fa-fw"></i></div>
        <div class="stats_info">
            <span class="text-muted"><?php echo $text_server_time; ?></span>
            <h4><?php echo $server_date .'<br>'.$server_time; ?></h4>
            <span class="text-muted"><?php echo ini_get('date.timezone'); ?></span>
        </div>
    </li>
    <?php echo $this->getHookVar('side_summary_li'); ?>
</ul>
<?php echo $this->getHookVar('side_summary_bottom'); ?>
