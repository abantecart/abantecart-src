<?php if ( count($currencies) > 1) { ?>
<ul class="nav language pull-left">
  <li class="dropdown hover">
<?php foreach ($currencies as $currency) { ?>  
	<?php if ($currency[ 'code' ] == $currency_code) { ?>
	<a class="dropdown-toggle" data-toggle=""><span><?php echo $currency[ 'title' ]; ?></span><b class="caret"></b></a>
	<?php } ?>
<?php } ?>    
    <ul class="dropdown-menu currency">
<?php foreach ($currencies as $currency) { ?>  
      <li>
      	<a href="<?php echo $currency[ 'href' ] ?>"><?php echo $currency[ 'title' ]; ?></a>
      </li>
<?php } ?>
    </ul>
  </li>
</ul>
<?php } ?>