<div class="t_block">
	<div class="block_cl">
		<div class="block_cr">
			<div class="block_cc">
				<div id="language">
					<?php if ($languages) { ?>
					<div class="switcher">
						<?php foreach ($languages as $language) { ?>
						<?php if ($language[ 'code' ] == $language_code) { ?>
							<div class="selected">
								<a>
								<?php if($language[ 'image' ]){ ?>
									<img src="<?php echo $language[ 'image' ]; ?>" alt="<?php echo $language[ 'name' ]; ?>"/>
								<?php }else{ echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';} ?>
								&nbsp;&nbsp;<span><?php echo $language[ 'name' ]; ?></span></a>
							</div>
							<?php } ?>
						<?php } ?>
						<div class="option">
							<?php foreach ($languages as $language) { ?>
							<a href="<?php echo $language[ 'href' ]; ?>">
								<?php if($language[ 'image' ]){ ?>
																	<img src="<?php echo $language[ 'image' ]; ?>" alt="<?php echo $language[ 'name' ]; ?>"/>
																<?php }else{ echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';} ?>
								&nbsp;&nbsp;<?php echo $language[ 'name' ]; ?>
							</a>
							<?php } ?>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>