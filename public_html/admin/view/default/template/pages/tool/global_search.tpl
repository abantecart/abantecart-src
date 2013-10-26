<?php if ($error_warning) { ?>
<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>
<div class="contentBox">
	<div class="cbox_tl">
		<div class="cbox_tr">
			<div class="cbox_tc">
				<div class="heading icon_title_search"><?php echo $heading_title; ?></div>
			</div>
		</div>
	</div>
	<div class="cbox_cl">
		<div class="cbox_cr">
			<div class="cbox_cc">
				<div class="search_box">
					<?php echo $search_form; ?>
					<div class="flt_left cl" style="margin-right: 5px;">
						<div class="cr">
							<div class="cc"><span style="margin-top: 3px;" class="icon_search">&nbsp;</span>
								<input type="text" style="font-size: 14px; height: 21px; line-height: 21px;"
								       value="<?php echo $search_form_input ?>" class="atext " id="search"
								       name="search">
							</div>
						</div>
					</div>
					<button class="flt_left btn_standard " type="submit"><?php echo $search_form_button; ?></button>
					</form>
				</div>
				<?php

				if ($search_categories) {
					foreach ($search_categories as $scat) {
						?>
						<div class="search_category_heading icon_title_<?php echo $search_categories_icons[ $scat ];?>"><?php echo $search_categories_names[ $scat ];?></div>
						<?php
	  				echo ${"listing_grid_" . $scat}; ?>
						<?php
					}
				} else {
					?>
					<div class="flt_none clr_both heading"><?php echo $scat;?></div>
					<table class="table_list">
						<tr>
							<td class="left" id="no results"><?php echo $no_results_message; ?></td>
						</tr>
					</table>

					<?php } ?>
			</div>
		</div>
		<div class="cbox_bl">
			<div class="cbox_br">
				<div class="cbox_bc"></div>
			</div>
		</div>
	</div>
<script type="text/javascript">
	$('span.icon_search').click(function(){
		$('#search_form').submit();
	});

	$(document).ready(function(){
		<?php
		$time = 0;
		foreach($grid_inits as $func_name){
			echo 'setTimeout("'.$func_name.'($)",'.$time.');'."\n";
			$time+=500;
		}
	?>
	});
</script>