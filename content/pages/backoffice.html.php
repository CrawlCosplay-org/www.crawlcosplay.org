<?php $this->layout = 'admin'; ?>
<?php

use alkemann\h2l\{ Environment, Log };

$error_message = false;
if ($data = $this->request->getPostData()) {
	$key = Environment::get('admin_phrase');
	if (empty($key)) {
		Log::error("No Admin phrase configured");
		$this->request->redirect('/');
		return;
	}

	if ($data['passphrase'] === $key) {
		session_start();
		$_SESSION['admin'] = true;
		$this->request->redirect('/admin/dashboard');
		return;
	} else {
		Log::warning("Admin phrase failed : [{$data['passphrase']}] != [{$key}]");
		$error_message = "Wrong passphrase. Try again.";
	}
}

?>
<h2 style="text-align:right;">Admin Authentication</h2>

<?php if ($error_message) : ?>
<p class="error"><?=$error_message?></p>
<?php endif; ?>
<form method="POST">
<p style="text-align:right;"><label><span>Passphrase</span><br />
		<input type="password" name="passphrase" placeholder="Do you remember?" autofocus />
	</label>
	<br />
	<input type="submit" name="Submit" />
</p>
</form>
