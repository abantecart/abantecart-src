<div class="info">
		<ul class="nav info_links_header">
			<li class="dropdown hover">
				<a class="dropdown-toggle" data-toggle=""><span><?php echo $heading_title; ?></span></a>
					<ul class="dropdown-menu dropup-menu">
			<?php echo $this->getHookVar('pre_contents'); ?>
			<?php foreach ($contents as $content) {	?>
			    <li><a href="<?php echo $content['href']; ?>"><?php echo $content['title']; ?></a></li>
			<?php } ?>
			<li><a href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a></li>
			<li><a href="<?php echo $sitemap; ?>"><?php echo $text_sitemap; ?></a></li>
			<?php if (!$logged) { ?>
				<li><a href="<?php echo $login; ?>"><?php echo $text_login; ?></a></li>
			<?php } else { ?>
			<li><a href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a></li>
			<li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
			<li><a href="<?php echo $cart; ?>"><?php echo $text_cart; ?></a></li>
			<?php } ?>
			<?php echo $this->getHookVar('post_contents'); ?>
				</ul>
			</li>

		</ul>
</div>