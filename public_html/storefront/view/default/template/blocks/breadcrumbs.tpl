<?php if ($breadcrumbs && count($breadcrumbs) > 1) {?>
<h4 class="hidden"> </h4>
<section class="breadcrumbs">
	<ul class="breadcrumb">
	    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
	    <li>
	    <a href="<?php echo $breadcrumb['href']; ?>">
	    	<?php echo ($breadcrumb['text'] == $text_home ? '<i class="fa fa-home" title="' . $text_home . '"></i> ' : '').$breadcrumb['text']; ?>
	    </a>
	    </li>
	    <?php } ?>
	</ul>
</section>
<?php } ?>