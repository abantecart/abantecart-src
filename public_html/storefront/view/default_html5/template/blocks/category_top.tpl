<nav class="subnav">
	<ul class="nav-pills categorymenu">
		<li><a class="active" href="<?php echo HTTP_SERVER; ?>"><?php echo $text_home; ?></a></li>
		<?php if ($categories) { ?>
			<?php foreach ($categories as $category) { ?>
				<li><a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a>
					<?php $sub_cat = $category['children']; ?>
					<?php if ($sub_cat) { ?>
						<!-- Subcategories -->
						<div>
							<ul>
								<?php foreach ($sub_cat as $scat) { ?>
									<li><a href="<?php echo $scat['href']; ?>"><?php echo $scat['name']; ?></a></li>
								<?php } ?>
							</ul>
							<?php if ($category['thumb']) { ?>
								<ul>
									<li><img style="display:block" src="<?php echo $scat['thumb']; ?>"
											 alt="<?php echo $scat['name']; ?>" title="<?php echo $scat['name']; ?>">
									</li>
								</ul>
							<?php } ?>
						</div>
					<?php } ?>
				</li>
			<?php } ?>
		<?php } ?>
	</ul>
</nav>