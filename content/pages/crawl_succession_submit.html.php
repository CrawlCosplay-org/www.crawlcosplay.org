<?php

use app\models\CrawlSuccessionGame;
use app\models\CrawlSuccessionUser;

require_once dirname(__DIR__, 3)
    . '/Private/crawl_succession_credentials_crypto.php';

session_start();

if (empty($_SESSION['user_id'])) {
    return $this->request->redirect('/crawl_succession_login');
}

$users = CrawlSuccessionUser::find([
    'id' => $_SESSION['user_id']
]);

$current_user = null;

foreach ($users as $user) {
    $current_user = $user;
    break;
}

if (!$current_user) {
    die('CrawlSuccession account not found.');
}

if (empty($current_user->discord_id)) {
    return $this->request->redirect('/crawl_succession');
}

if ($data = $this->request->getPostData()) {

    $character_name = trim($data['character_name'] ?? '');
    $game_type = trim($data['game_type'] ?? '');
    $species = trim($data['species'] ?? '');
    $background = trim($data['background'] ?? '');
    $god = trim($data['god'] ?? '');
    $server = trim($data['server'] ?? '');
    $server_username = trim($data['server_username'] ?? '');
    $server_password = $data['server_password'] ?? '';
    $min_players = (int) ($data['min_players'] ?? 6);
    $max_players = (int) ($data['max_players'] ?? 25);
    $description = trim($data['description'] ?? '');

   if ($character_name === '' || $server === '') {
    $error = "Character name and server are required.";
    }

    } elseif (!in_array($game_type, [
    'Winrate',
    'Challenge',
    'Just For Fun',
    'Educational',
    'Other'
    ], true)) {

    $error = "Please select a valid game type.";    

    } elseif ($min_players < 1 || $max_players < $min_players) {

        $error = "Invalid player limits.";

    } else {

        try {

            $encrypted_username = $server_username !== ''
                ? encryptCrawlSuccessionCredential($server_username)
                : null;

            $encrypted_password = $server_password !== ''
                ? encryptCrawlSuccessionCredential($server_password)
                : null;

            $game = new CrawlSuccessionGame([
                'character_name' => $character_name,
                'game_type' => $game_type,
                'species' => $species,
                'background' => $background,
                'god' => $god,
                'server' => $server,
                'server_username' => $encrypted_username,
                'server_password' => $encrypted_password,
                'min_players' => $min_players,
                'max_players' => $max_players,
                'description' => $description,
                'created_by' => $_SESSION['user_id'],
                'current_user_id' => null,
                'turn_number' => 1,
                'status' => 'pending',
                'result' => null,
                'created' => date('Y-m-d H:i:s'),
                'started' => null,
                'concluded' => null
            ]);

            if ($game->save()) {
                return $this->request->redirect('/crawl_succession');
            }

            $error = "Unable to create character.";

        } catch (\Throwable $exception) {

            error_log(
                'CrawlSuccession: Unable to encrypt credentials during character creation.'
            );

            $error = "Unable to process server credentials. Please try again.";
        }
    }
}

?>

<h2>Submit a Crawl Succession Game</h2>

<p>
    Your succession game will be reviewed by an admin before it becomes available
    for players to join.
</p>

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
            <span>Game Type</span><br />
            <select name="game_type" required>
                <option value="Winrate">Winrate</option>
                <option value="Challenge">Challenge</option>
                <option value="Just For Fun" selected>Just For Fun</option>
                <option value="Educational">Educational</option>
                <option value="Other">Other</option>
            </select>
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
            <span>Server Username</span><br />
            <input
                type="text"
                name="server_username"
                maxlength="200"
                autocomplete="off"
                required
            />
        </label>

        <br /><br />

        <label>
            <span>Server Password</span><br />
            <input
                type="password"
                name="server_password"
                autocomplete="new-password"
                required
            />
        </label>

        <p>
            <u>Note:</u> Your server username and password will be encrypted before being
            stored. Once the succession game becomes active, the credentials
            will only be shown to players who are signed in, have linked their
            Discord account, and are registered in this game's player queue.
            <br>
            You may leave the server username and password fields blank and write below if you want to use one of Crawl Cosplay's accounts: Winrate, Challenge, Just For Fun, Educational,
        </p>

        <br />

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
    <a href="/crawl_succession">Back to Crawl Succession</a>
</p>
