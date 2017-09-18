<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	</head>
	<body>
		<table style="font-family: Verdana,sans-serif; font-size: 11px; color: #374953; width: 600px;">
			<tr>
				<td class="align_left">
					<a href="<?php echo $store_url; ?>" title="<?php echo $store_name; ?>">
						<?php if($logo_uri){ ?>
							<img src="<?php echo $logo_uri; ?>" alt="<?php echo $store_name; ?>" style="border: none;">
					<?php }elseif($logo_html){
							echo $logo_html;
						 } ?>
					</a>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo $text_welcome;?></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
				<?php
					if ($text_activate) { echo $text_activate.'<br>'; }
					if ($text_login) { echo $text_login.'<br>'; }
					if ($text_approval) { echo $text_approval.'<br>'; }
					if ($text_login_later) { echo '<br>'.$text_login_later.'<br>'; }
					if ($text_services) { echo '<br>'.$text_services.'<br>'; }
				?>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
				<?php
					echo $text_thanks.'<br>';
					echo $store_name.'<br>';
			?>	</td>
			</tr>
		</table>
	</body>
</html>
