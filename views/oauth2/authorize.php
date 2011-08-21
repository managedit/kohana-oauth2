<html>
	<head><?=$user->email ?> Authorize to ClientID <?=$client->id ?></head>
	<body>
		<form method="post" action="">
			<?php foreach ($auth_params as $k => $v): ?>
				<input type="hidden" name="<?php echo $k ?>" value="<?php echo $v ?>" />
			<?php endforeach; ?>
			Do you authorize the app to do its thing?
			<p>
				<input type="submit" name="accept" value="Yes" />
				<input type="submit" name="accept" value="No" />
			</p>
		</form>
	</body>
</html>