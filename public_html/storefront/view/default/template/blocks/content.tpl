<div class="sidewidt">
	<h2 class="heading2"><span><?php echo $heading_title; ?></span></h2>
		<ul class="nav list-group side_list">
			<?php echo $this->getHookVar('pre_contents'); ?>
			<?php foreach ($contents as $content) { ?>
			    <li><a href="<?php echo $content['href']; ?>"><?php echo $content['title']; ?></a></li>
			<?php } ?>
			<?php echo $this->getHookVar('post_contents'); ?>
			<li><a href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a></li>
			<li><a href="<?php echo $sitemap; ?>"><?php echo $text_sitemap; ?></a></li>
		</ul>
</div>