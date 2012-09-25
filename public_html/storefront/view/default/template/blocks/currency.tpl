<div class="t_block">
	<div class="block_cl">
		<div class="block_cr">
			<div class="block_cc">
				<div id="currency">
					<?php if ($currencies) { ?>
					<div class="switcher">
						<?php foreach ($currencies as $currency) { ?>
						<?php if ($currency[ 'code' ] == $currency_code) { ?>
							<div class="selected"><a><span><?php echo $currency[ 'title' ]; ?></span></a></div>
							<?php } ?>
						<?php } ?>
						<div class="option">
							<?php foreach ($currencies as $currency) { ?>
							<a href="<?php echo $currency[ 'href' ] ?>"><?php echo $currency[ 'title' ]; ?></a>
							<?php } ?>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>