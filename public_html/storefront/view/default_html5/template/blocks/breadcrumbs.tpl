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