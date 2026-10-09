<?php $this->layout = 'admin'; ?>
<?php

use app\models\Player;

$id = $_GET['id'] ?? false;

if ($id == false) {
    return $this->request->redirect('/admin/players/list.html');
}

$player = Player::get($id);
if (!$player) {
    return $this->request->redirect('/admin/players/list.html');
}

$error = null;

if ($data = $this->request->getPostData()) {
    if (($data['action'] ?? '') === 'delete') {
        $submissions = $player->submissions();

        if (!empty($submissions)) {
            $error = 'Cannot delete this player because they have submissions.';
        } elseif (!$player->delete()) {
            $error = 'Player deletion failed.';
        } else {
            return $this->request->redirect('/admin/players/list.html');
        }
    } else {
        $player->save($data);
        return $this->request->redirect('/admin/players/list.html');
    }
}

?>

<h2>Edit Player</h2>

<?php if ($error): ?>
    <p class="error"><?=htmlspecialchars($error, ENT_QUOTES, 'UTF-8')?></p>
<?php endif; ?>

<form method="POST">
    <fieldset>
        <label>
            <span>Name</span><br />
            <input type="text" name="name"
                value="<?=htmlspecialchars($player->name, ENT_QUOTES, 'UTF-8')?>" />
        </label>
        <br />

        <label>
            <span>Reddit account</span><br />
            <input type="text" name="reddit"
                value="<?=htmlspecialchars($player->reddit, ENT_QUOTES, 'UTF-8')?>" />
        </label>
        <br />

        <label>
            <span>Discord name</span><br />
            <input type="text" name="discord"
                value="<?=htmlspecialchars($player->discord, ENT_QUOTES, 'UTF-8')?>" />
        </label>
        <br />

        <input type="submit" name="Save" value="Save">
    </fieldset>
</form>

<hr />

<form method="POST"
      onsubmit="return confirm('Are you sure you want to delete this player? This cannot be undone.');">
    <input type="hidden" name="action" value="delete">
    <button type="submit">Delete Player</button>
</form>
