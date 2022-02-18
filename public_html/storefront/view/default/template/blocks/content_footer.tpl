<div class="info">
	<ul class="info_links_footer">
		<?php echo $this->getHookVar('pre_contents'); ?>
		<?php
		foreach ($contents as $k=>$content) {
			$level = $content['level'];
			if(!$level){?>
				<li><div class="dropdown">
					<a href="<?php echo $content['href']; ?>"><?php echo $content['title']; ?></a>
			<?php if($contents[$k+1]['level']){?>
					<ul class="dropdown-menu dropup-menu">
			<?php }
			}else{ ?>
					<li class="hover">
						<a href="<?php echo $content['href']; ?>"><?php echo $content['title']; ?></a>
					</li>
			<?php }
			if(!$contents[$k+1]['level']){
				if($level){?>
					</ul>
				<?php }?>
				</div></li>
			<?php }
		}?>
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
</div>