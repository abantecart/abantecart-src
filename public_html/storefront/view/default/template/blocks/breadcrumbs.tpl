<?php if ($breadcrumbs && count($breadcrumbs) > 1) { ?>
<section class="breadcrumbs">
	<ul class="breadcrumb">
	    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
	    <?php if (!empty($breadcrumb['separator'])) echo '<i class="fa fa-angle-right fa-fw"></i>' ?>
	    <li><a href="<?php echo $breadcrumb['href']; ?>">
	    	<?php echo ($breadcrumb['text'] == $text_home ? '<img src="' . $this->templateResource('/image/icon_breadcrumbs_home.gif') . '" alt="' . $text_home . '" />' : $breadcrumb['text']); ?>
	    </a></li>
	    <?php } ?>
	</ul>
</section>
<?php } ?>