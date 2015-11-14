<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?php echo $title; ?></title>
</head>
<body>
<table style="font-family: Verdana,sans-serif; font-size: 11px; color: #374953; width: 600px;">
	<tr>
		<td class="align_left"><a href="<?php echo $store_url; ?>" title="<?php echo $store_name; ?>"><img
						src="<?php echo $logo; ?>" alt="<?php echo $store_name; ?>" style="border: none;"></a></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td class="align_left"
		    style="background-color: #069; color:#FFF; font-size: 12px; font-weight: bold; padding: 0.5em 1em;"><?php echo $entry_enquiry; ?></td>
	</tr>
	<tr>
		<td class="align_left"
		    style=" font-size: 12px; padding: 0.5em 1em;"><?php echo $enquiry; ?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>
			<table style="width: 100%; font-family: Verdana,sans-serif; font-size: 11px; color: #FFFFFF;">
				<?php foreach($form_fields as $name=>$value){?>
				<tr>
					<td style="padding: 0.3em; background-color: #EEEEEE; color: #000;"><?php echo $name; ?></td>
					<td style="padding: 0.3em; background-color: #EEEEEE; color: #000;"><?php echo $value; ?></td>
				</tr>
				<?php } ?>
			</table>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td class="align_center"
		    style="font-size: 10px; border-top: 1px solid #069; text-decoration: none; color: #374953;">
			<a href="<?php echo $store_url; ?>"
			   style="color: #069; font-weight: bold; text-decoration: none;"><?php echo $store_name; ?></a><br>
			<?php echo $text_project_label; ?>
		</td>
	</tr>
</table>
</body>
</html>
