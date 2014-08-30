<?php if ( count($languages) > 1 ) { ?>  
<ul class="nav language pull-left">
<?php if ($languages) { ?>
  <li class="dropdown hover">
<?php foreach ($languages as $language) { ?>
<?php if ($language[ 'code' ] == $language_code) { ?>  
	<a class="dropdown-toggle" data-toggle="">
	<?php if($language[ 'image' ]) { ?>
		<img src="<?php echo $language[ 'image' ]; ?>" alt="<?php echo $language[ 'name' ]; ?>"/>
	<?php } else { echo '&nbsp;'; } ?>&nbsp;<span><?php echo $language[ 'name' ]; ?></span><b class="caret"></b>
	</a>
<?php } ?>
<?php } ?>
    <ul class="dropdown-menu language">
<?php foreach ($languages as $language) { ?>
      <li>
      <a href="<?php echo $language[ 'href' ]; ?>">
      <?php if($language[ 'image' ]){ ?>
      <img src="<?php echo $language[ 'image' ]; ?>" alt="<?php echo $language[ 'name' ]; ?>"/>
      <?php }else{ echo '&nbsp;';} ?>&nbsp;&nbsp;<?php echo $language[ 'name' ]; ?>
      </a>
<?php } ?>
    </ul>
  </li>
<?php } ?>  
</ul>
<?php } ?>