<div class="t_block">
	<div class="block_cl">
		<div class="block_cr">
			<div class="block_cc">
				<div id="currency">
					<?php if ($currencies) { ?>
					<?php echo  $form[ 'form_open' ]; ?>
					<div class="switcher">
						<?php foreach ($currencies as $currency) { ?>
						<?php if ($currency[ 'code' ] == $currency_code) { ?>
							<div class="selected"><a><span><?php echo $currency[ 'title' ]; ?></span></a></div>
							<?php } ?>
						<?php } ?>
						<div class="option">
							<?php foreach ($currencies as $currency) { ?>
							<a onclick="$('input[name=\'currency_code\']').attr('value', '<?php echo $currency[ 'code' ]; ?>'); $('#currency_form').submit();"><?php echo $currency[ 'title' ]; ?></a>
							<?php } ?>
						</div>
						<?php echo  $form[ 'code' ]; ?>
						<?php echo  $form[ 'redirect' ]; ?>
					</div>
					</form>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>