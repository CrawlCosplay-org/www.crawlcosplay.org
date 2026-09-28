<?php $this->layout = 'cca'; ?>

<?php

use app\models\CrawlSuccessionUser;

if ($data = $this->request->getPostData()) {

    $username = trim($data['username'] ?? '');
    $password = $data['password'] ?? '';

    $users = CrawlSuccessionUser::find(['username' => $username]);
    $user = null;

    foreach ($users as $found_user) {
        $user = $found_user;
        break;
    }

    if ($user && password_verify($password, $user->password_hash)) {

        session_start();
        $_SESSION['user_id'] = $user->id;

        return $this->request->redirect('/crawl_succession');

    }

    $error = "Invalid username or password.";
}

?>

<h2>CrawlSuccession Login</h2>

<?php if (!empty($error)): ?>
    <p style="color:red;"><b><?=$e($error)?></b></p>
<?php endif; ?>

<form method="POST">
    <fieldset>

        <label>
            <span>Username</span><br />
            <input type="text" name="username" required autofocus />
        </label>

        <br /><br />

        <label>
            <span>Password</span><br />
            <input type="password" name="password" required />
        </label>

        <br /><br />

        <input type="submit" value="Login">

    </fieldset>
</form>

<p>
    Don't have an account?
    <a href="/crawl_succession_register">Register</a>
</p>
