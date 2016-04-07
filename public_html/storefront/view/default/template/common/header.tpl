<header>
<div class="headerstrip navbar navbar-inverse" role="navigation">
	<div class="container-fluid">
	  <div class="navbar-header header-logo">
	    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
	      <span class="sr-only"></span>
	      <span class="icon-bar"></span>
	      <span class="icon-bar"></span>
	      <span class="icon-bar"></span>
	    </button>
	    <?php if (is_file(DIR_RESOURCE . $logo)) { ?>
		<a class="logo" href="<?php echo $homepage; ?>">
			<img src="resources/<?php echo $logo; ?>" width="<?php echo $logo_width; ?>" height="<?php echo $logo_height; ?>" title="<?php echo $store; ?>" alt="<?php echo $store; ?>"/>
		</a>
		<?php } else if (!empty($logo)) { ?>
	    	<a class="logo" href="<?php echo $homepage; ?>"><?php echo $logo; ?></a>
	    <?php } ?>
	  </div>
	  <div class="navbar-collapse collapse">
	  	<div class="navbar-right headerstrip_blocks">
	  	    <div class="block_1"><?php echo ${$children_blocks[0]}; ?></div>
	  	    <div class="block_2"><?php echo ${$children_blocks[1]}; ?></div>
	  	    <div class="block_3"><?php echo ${$children_blocks[2]}; ?></div>
	  	    <div class="block_4"><?php echo ${$children_blocks[3]}; ?></div>
	  	</div>
	   </div><!--/.navbar-collapse -->
	</div>         
</div>
<div class="container-fluid">
    <div class="col-md-12 headerdetails">
    	<!-- header blocks placeholder -->
    	<div class="block_5"><?php echo ${$children_blocks[4]}; ?></div>			
    	<div class="block_6"><?php echo ${$children_blocks[5]}; ?></div>
    	<div class="block_7"><?php echo ${$children_blocks[6]}; ?></div>
    	<div class="block_8"><?php echo ${$children_blocks[7]}; ?></div>
    	<!-- header blocks placeholder (EOF) -->
    </div>
</div>

</header>
<?php if (1==0) { ?>
<!-- Header Start -->
<header>
	<div class="headerstrip">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<?php if (is_file(DIR_RESOURCE . $logo)) { ?>
					<a class="logo pull-left" href="<?php echo $homepage; ?>"><img src="resources/<?php echo $logo; ?>"
																				   title="<?php echo $store; ?>"
																				   alt="<?php echo $store; ?>"/></a>
					<?php } else if (!empty($logo)) { ?>
					<a class="logo pull-left" href="<?php echo $homepage; ?>"><?php echo $logo; ?></a>
					<?php } ?>
					<div class="headerstrip_blocks">
						<div class="pull-left ml10"><?php echo ${$children_blocks[0]}; ?></div>
						<div class="pull-left"><?php echo ${$children_blocks[1]}; ?></div>
						<div class="pull-left"><?php echo ${$children_blocks[2]}; ?></div>
						<div class="pull-right"><?php echo ${$children_blocks[3]}; ?></div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="container-fluid">
		<div class="headerdetails">
			<!-- header blocks placeholder -->
			<div class="pull-left"><?php echo ${$children_blocks[4]}; ?></div>			
			<div class="pull-left"><?php echo ${$children_blocks[5]}; ?></div>
			<div class="pull-left"><?php echo ${$children_blocks[6]}; ?></div>
			<div class="pull-right"><?php echo ${$children_blocks[7]}; ?></div>
			<!-- header blocks placeholder (EOF) -->
		</div>
	</div>
</header>
<!-- Header End -->
<?php } ?>
