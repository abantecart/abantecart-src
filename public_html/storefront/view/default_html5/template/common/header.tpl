<!-- Header Start -->
<header>
	<div class="headerstrip">
		<div class="container">
			<div class="row">
				<div class="span12">

					<?php if (is_file(DIR_RESOURCE . $logo)) { ?>
					<a class="logo pull-left" href="<?php echo $homepage; ?>"><img src="resources/<?php echo $logo; ?>"
																				   title="<?php echo $store; ?>"
																				   alt="<?php echo $store; ?>"/></a>
					<?php } else if (!empty($logo)) { ?>
					<a class="logo pull-left" href="<?php echo $homepage; ?>"><?php echo $logo; ?></a>
					<?php } ?>

					<!-- Top Nav Start -->
					<div class="pull-left"><?php echo ${$children_blocks[0]}; ?></div>
					<!-- Top Nav End -->
					<div class="pull-left"><?php echo ${$children_blocks[1]}; ?></div>
					<div class="pull-right"><?php echo ${$children_blocks[2]}; ?></div>
				</div>
			</div>
		</div>
	</div>

	<div class="container">
		<div class="headerdetails">
			<!-- header blocks placeholder -->
			<div class="pull-left"><?php echo ${$children_blocks[3]}; ?></div>
			<div class="pull-left"><?php echo ${$children_blocks[4]}; ?></div>			
			<div class="pull-left"><?php echo ${$children_blocks[5]}; ?></div>
			<div class="pull-right"><?php echo ${$children_blocks[6]}; ?></div>
			<!-- header blocks placeholder (EOF) -->
		</div>		
		<div id="categorymenu">
			<?php echo ${$children_blocks[7]}; ?>
		</div>		
	</div>
</header>
<!-- Header End -->

<?php if ($breadcrumbs && count($breadcrumbs) > 1) { ?>
<section class="breadcrumbs">
	<div class="container">
		<ul class="breadcrumb">
			<?php foreach ($breadcrumbs as $breadcrumb) { ?>
			<?php if (!empty($breadcrumb['separator'])) echo '<span class="breadcrumb-separator"><img src="' . $this->templateResource('/image/icon_breadcrumbs_more.gif') . '" alt="" /></span>' ?>
			<li><a href="<?php echo $breadcrumb['href']; ?>">
				<?php echo ($breadcrumb['text'] == $text_home ? '<img src="' . $this->templateResource('/image/icon_breadcrumbs_home.gif') . '" alt="' . $text_home . '" />' : $breadcrumb['text']); ?>
			</a></li>
			<?php } ?>
		</ul>
	</div>
</section>
<?php } ?>