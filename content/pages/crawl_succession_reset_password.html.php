<?php

use app\models\CrawlSuccessionUser;

session_start();

$reset_user_id = $_SESSION['password_reset_user_id'] ?? null;
$expires = $_SESSION['password_reset_expires'] ?? 0;
$csrf = $_SESSION['password_reset_csrf'] ?? '';

if (
    empty($reset_user_id)
    || !is_numeric($expires)
    || time() > $expires
    || !is_string($csrf)
    || $csrf === ''
) {
    unset(
        $_SESSION['password_reset_user_id'],
        $_SESSION['password_reset_expires'],
        $_SESSION['password_reset_csrf']
    );

    die('Your password reset session has expired. Please start again.');
}

$error = '';

if ($data = $this->request->getPostData()) {
    $posted_csrf = $data['csrf'] ?? '';

    if (
        !is_string($posted_csrf)
        || !hash_equals($csrf, $posted_csrf)
    ) {
        http_response_code(403);
        die('Invalid form submission. Please start recovery again.');
    }

    if (time() > $_SESSION['password_reset_expires']) {
        die('Your password reset session has expired. Please start again.');
    }

    $password = $data['password'] ?? '';
    $confirmation = $data['password_confirmation'] ?? '';

   if (!is_string($password) || strlen($password) < 8) {
    $error = 'Your new password must be at least 8 characters long.';
    } elseif ($password !== $confirmation) {
        $error = 'The passwords do not match.';
    } else {
        $users = CrawlSuccessionUser::find([
            'id' => $reset_user_id
        ]);

        $user = null;

        foreach ($users as $found_user) {
            $user = $found_user;
            break;
        }

        if (!$user) {
            die('The account could not be found.');
        }

        $password_hash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        if (!$user->save(['password_hash' => $password_hash])) {
            error_log(
                'CrawlSuccession: password reset save failed for user ' .
                $user->id
            );

            $error = 'The password could not be saved. Please try again.';
        } else {
            unset(
                $_SESSION['password_reset_user_id'],
                $_SESSION['password_reset_expires'],
                $_SESSION['password_reset_csrf']
            );

            return $this->request->redirect('/crawl_succession_login');
        }
    }
}

?>

<h2>Reset Your Password</h2>

<p>Choose a new password of at least 8 characters.</p>

<?php if ($error !== ''): ?>
    <p style="color:red;"><b><?=$e($error)?></b></p>
<?php endif; ?>

<form method="POST">
    <input
        type="hidden"
        name="csrf"
        value="<?=$e($csrf)?>"
    >

    <label>
        <span>New password</span><br>
        <input
            type="password"
            name="password"
            minlength="8"
            autocomplete="new-password"
            required
        >
    </label>

    <br><br>

    <label>
        <span>Confirm new password</span><br>
        <input
            type="password"
            name="password_confirmation"
            minlength="8"
            autocomplete="new-password"
            required
        >
    </label>

    <br><br>

    <input type="submit" value="Reset Password">
</form>
