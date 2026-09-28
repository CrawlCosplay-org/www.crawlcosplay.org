<?php

use app\models\CrawlSuccessionGame;

session_start();

if (empty($_SESSION['user_id'])) {
    return $this->request->redirect('/crawl_succession_login');
}

if ($data = $this->request->getPostData()) {

    $character_name = trim($data['character_name'] ?? '');
    $species = trim($data['species'] ?? '');
    $background = trim($data['background'] ?? '');
    $god = trim($data['god'] ?? '');
    $server = trim($data['server'] ?? '');
    $min_players = (int) ($data['min_players'] ?? 6);
    $max_players = (int) ($data['max_players'] ?? 25);
    $description = trim($data['description'] ?? '');

    if ($character_name === '' || $server === '') {

        $error = "Character name and server are required.";

    } elseif ($min_players < 1 || $max_players < $min_players) {

        $error = "Invalid player limits.";

    } else {

        $game = new CrawlSuccessionGame([
            'character_name' => $character_name,
            'species' => $species,
            'background' => $background,
            'god' => $god,
            'server' => $server,
            'min_players' => $min_players,
            'max_players' => $max_players,
            'description' => $description,
            'created_by' => $_SESSION['user_id'],
            'current_user_id' => null,
            'turn_number' => 1,
            'status' => 'planned',
            'result' => null,
            'created' => date('Y-m-d H:i:s'),
            'started' => null,
            'concluded' => null
        ]);

        if ($game->save()) {
            return $this->request->redirect('/crawl_succession');
        }

        $error = "Unable to create character.";
    }
}

?>

<h2>Submit a CrawlSuccession Character</h2>

<?php if (!empty($error)): ?>
    <p style="color:red;"><b><?=$e($error)?></b></p>
<?php endif; ?>

<form method="POST">
    <fieldset>

        <label>
            <span>Character Name (suggestion: name it after an in-game unique)</span><br />
            <input type="text" name="character_name" maxlength="100" required autofocus />
        </label>

        <br /><br />

        <label>
            <span>Species</span><br />
            <input type="text" name="species" maxlength="50" />
        </label>

        <br /><br />

        <label>
            <span>Background</span><br />
            <input type="text" name="background" maxlength="50" />
        </label>

        <br /><br />

        <label>
            <span>God(s)</span><br />
            <input type="text" name="god" maxlength="50" />
        </label>

        <br /><br />

        <label>
            <span>Server (suggest your home server or pick a currently unused one)</span><br />
            <input type="text" name="server" maxlength="50" required />
        </label>

        <br /><br />

        <label>
            <span>Minimum Players to Start</span><br />
            <input type="number" name="min_players" min="1" value="6" required />
        </label>

        <br /><br />

        <label>
            <span>Maximum Players (determines how many floors each plays)</span><br />
            <input type="number" name="max_players" min="1" value="25" required />
        </label>

        <br /><br />

        <label>
            <span>Description of Character</span><br />
            <textarea name="description" rows="6" cols="60"></textarea>
        </label>

        <br /><br />

        <input type="submit" value="Submit Character">

    </fieldset>
</form>

<p>
    <a href="/crawl_succession">Back to CrawlSuccession</a>
</p>
