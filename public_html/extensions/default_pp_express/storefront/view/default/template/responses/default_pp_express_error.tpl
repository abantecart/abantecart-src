<div id="content">
	<div class="top">
		<div class="left"></div>
		<div class="right"></div>
		<div class="center">
			<h1><?php echo $heading_title; ?></h1>
		</div>
	</div>
	<div class="middle">
		<div class="content"><?php echo $message; ?></div>
		<div class="buttons mt20">
			<table>
				<tbody>
				<tr>
					<td align="right">
						<?php
						$button_continue->icon = '';
						echo $button_continue; ?>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="bottom">
		<div class="left"></div>
		<div class="right"></div>
		<div class="center"></div>
	</div>
</div>

<script>
	$('#button_continue').click(function() {
		location = '<?php echo $continue; ?>';
	});
</script>