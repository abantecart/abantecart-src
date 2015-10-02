<?php if ($breadcrumbs && count($breadcrumbs) > 1) {?>
<section class="breadcrumbs">
	<ul class="breadcrumb">
	    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
	    <?php if (!empty($breadcrumb['separator'])) echo '<i class="fa fa-angle-right fa-fw"></i>' ?>
	    <li><a href="<?php echo $breadcrumb['href']; ?>">
	    	<?php echo ($breadcrumb['text'] == $text_home ? '<i class="fa fa-home" title="' . $text_home . '"></i> ' : '').$breadcrumb['text']; ?>
	    </a></li>
	    <?php } ?>
	</ul>
</section>
<?php } ?>