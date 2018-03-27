<?php if (sizeof((array)$languages) > 1){ ?>
	<ul class="nav language pull-left">
		<?php if ($languages){ ?>
			<li class="dropdown hover">
				<?php
				$img_w = 16;
				$img_h = 11;

				foreach ($languages as $language){ ?>
					<?php if ($language['code'] == $language_code){ ?>
						<a class="dropdown-toggle" data-toggle="">
							<?php if ($language['image']){ ?>
								<img src="<?php echo $language['image']; ?>"
									 style="width: <?php echo($language['image_width'] ? $language['image_width'] : $img_w); ?>px;
											 height: <?php echo $language['image_height'] ? $language['image_height'] : $img_h; ?>px;"
									 alt="<?php echo $language['name']; ?>"/>
							<?php } else{
								echo '&nbsp;';
							} ?>&nbsp;<span><?php echo $language['name']; ?></span><b class="caret"></b>
						</a>
					<?php } ?>
				<?php } ?>
				<ul class="dropdown-menu language">
					<?php foreach ($languages as $language) { ?>
					<li>
						<a href="<?php echo $language['href']; ?>">
							<?php if ($language['image']){
								$language['image_width'] = !$language['image_width'] ? $img_w : $language['image_width'];
								$language['image_height'] = !$language['image_height'] ? $img_h : $language['image_height'];
								?>
								<img src="<?php echo $language['image']; ?>"
									 style="width: <?php echo $language['image_width']; ?>px; height: <?php echo $language['image_height']; ?>px;"
									 alt="<?php echo $language['name']; ?>"/>
							<?php } else{ ?>
								<span class="ml20"></span>
							<?php } ?>&nbsp;&nbsp;<?php echo $language['name']; ?>
						</a>
						<?php } ?>
				</ul>
			</li>
		<?php } ?>
	</ul>
<?php } ?>