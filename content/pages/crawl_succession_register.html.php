<?php

use app\models\CrawlSuccessionUser;

if ($data = $this->request->getPostData()) {

    $username = trim($data['username'] ?? '');
    $password = $data['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "All fields are required.";
    } else {
        $user = new CrawlSuccessionUser([
            'username' => $username,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'discord_id' => null,
            'created' => date('Y-m-d H:i:s')
        ]);

        if ($user->save()) {
            session_start();
            $_SESSION['user_id'] = $user->id;

            return $this->request->redirect('/crawl_succession');
        }

        $error = "Unable to create account.";
    }
}

?>

<h2>CrawlSuccession Registration</h2>

<?php if (!empty($error)): ?>
    <p style="color:red;"><b><?=$e($error)?></b></p>
<?php endif; ?>

<form method="POST">
    <fieldset>

        <label>
            <span>Username</span><br />
            <input type="text" name="username" maxlength="50" required autofocus />
        </label>

        <br /><br />

        <label>
            <span>Password</span><br />
            <input type="password" name="password" required />
        </label>

        <br /><br />

        <input type="submit" value="Create Account">

    </fieldset>
</form>
