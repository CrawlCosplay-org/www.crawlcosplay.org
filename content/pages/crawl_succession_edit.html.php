<?php

use app\models\CrawlSuccessionGame;

session_start();

$id = (int) ($this->request->getGetData()['id'] ?? 0);

$games = CrawlSuccessionGame::find(['id' => $id]);

$game = null;

foreach ($games as $found_game) {
    $game = $found_game;
    break;
}

if (!$game) {
    die('Game not found.');
}


/*
 * Require a logged-in CrawlSuccession account.
 */
$current_user_id = $_SESSION['user_id'] ?? null;

if (!$current_user_id) {
    return $this->request->redirect('/crawl_succession_login');
}


/*
 * Only the creator can currently edit the succession.
 */
if ($current_user_id != $game->created_by) {
    die('You do not have permission to edit this succession.');
}


/*
 * Handle form submission.
 */
if ($data = $this->request->getPostData()) {

    $character_name = trim($data['character_name'] ?? '');
    $species = trim($data['species'] ?? '');
    $background = trim($data['background'] ?? '');
    $god = trim($data['god'] ?? '');
    $server = trim($data['server'] ?? '');
    $min_players = (int) ($data['min_players'] ?? 0);
    $max_players = (int) ($data['max_players'] ?? 0);
    $description = trim($data['description'] ?? '');

    if ($character_name === '' || $server === '') {

        $error = "Character name and server are required.";

    } elseif ($min_players < 1 || $max_players < $min_players) {

        $error = "Invalid player limits.";

    } else {

       $save_data = [
    'character_name' => $character_name,
    'species' => $species,
    'background' => $background,
    'god' => $god,
    'server' => $server,
    'min_players' => $min_players,
    'max_players' => $max_players,
    'description' => $description
];

if ($game->save($save_data)) {
    return $this->request->redirect(
        '/crawl_succession_game?id=' . $game->id
    );
}

$error = "Unable to save changes.";
    }
}

?>

<h2>Edit CrawlSuccession Character</h2>

<?php if (!empty($error)): ?>

    <p style="color:red;"><b><?=$e($error)?></b></p>

<?php endif; ?>

<form method="POST">
    <fieldset>

        <label>
            <span>Character Name</span><br />
            <input
                type="text"
                name="character_name"
                maxlength="100"
                value="<?=$e($game->character_name)?>"
                required
                autofocus
            />
        </label>

        <br /><br />

        <label>
            <span>Species</span><br />
            <input
                type="text"
                name="species"
                maxlength="50"
                value="<?=$e($game->species)?>"
            />
        </label>

        <br /><br />

        <label>
            <span>Background</span><br />
            <input
                type="text"
                name="background"
                maxlength="50"
                value="<?=$e($game->background)?>"
            />
        </label>

        <br /><br />

        <label>
            <span>God(s)</span><br />
            <input
                type="text"
                name="god"
                maxlength="50"
                value="<?=$e($game->god)?>"
            />
        </label>

        <br /><br />

        <label>
            <span>Server</span><br />
            <input
                type="text"
                name="server"
                maxlength="50"
                value="<?=$e($game->server)?>"
                required
            />
        </label>

        <br /><br />

        <label>
            <span>Minimum Players to Start</span><br />
            <input
                type="number"
                name="min_players"
                min="1"
                value="<?=$e($game->min_players)?>"
                required
            />
        </label>

        <br /><br />

        <label>
            <span>Maximum Players</span><br />
            <input
                type="number"
                name="max_players"
                min="1"
                value="<?=$e($game->max_players)?>"
                required
            />
        </label>

        <br /><br />

        <label>
            <span>Description / Restart Rules</span><br />
            <textarea
                name="description"
                rows="8"
                cols="60"
            ><?=$e($game->description)?></textarea>
        </label>

        <br /><br />

        <input type="submit" value="Save Changes">

    </fieldset>
</form>

<p>
    <a href="/crawl_succession_game?id=<?=$e($game->id)?>">
        Back to Character
    </a>
</p>
