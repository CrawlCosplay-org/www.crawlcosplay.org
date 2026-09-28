<?php

use app\models\CrawlSuccessionGame;
use app\models\CrawlSuccessionQueue;
use app\models\CrawlSuccessionUser;

session_start();

$id = (int) ($this->request->get['id'] ?? 0);

$games = CrawlSuccessionGame::find(['id' => $id]);

$game = null;

foreach ($games as $found_game) {
    $game = $found_game;
    break;
}

if (!$game) {
    die('Game not found. ID = ' . $id);
}

$current_user_id = $_SESSION['user_id'] ?? null;


/*
 * Handle joining the queue.
 */
if ($current_user_id && $game->status === 'planned' && $this->request->getPostData()) {

    $queue = CrawlSuccessionQueue::find([
        'game_id' => $game->id,
        'user_id' => $current_user_id
    ]);

    $already_joined = false;

    foreach ($queue as $existing) {
        $already_joined = true;
        break;
    }

    if ($already_joined) {

        $error = "You are already in this succession.";

    } else {

        $players = CrawlSuccessionQueue::find([
            'game_id' => $game->id
        ]);

        $player_count = 0;
        $highest_position = 0;

        foreach ($players as $player) {
            $player_count++;

            if ($player->position > $highest_position) {
                $highest_position = $player->position;
            }
        }

        if ($player_count >= $game->max_players) {

            $error = "This succession is full.";

        } else {

            $entry = new CrawlSuccessionQueue([
                'game_id' => $game->id,
                'user_id' => $current_user_id,
                'position' => $highest_position + 1,
                'active' => 0,
                'joined' => date('Y-m-d H:i:s')
            ]);

            if ($entry->save()) {
                return $this->request->redirect(
                    '/crawl_succession_game?id=' . $game->id
                );
            }

            $error = "Unable to join the succession.";
        }
    }
}

/*
 * Load queue.
 */
$queue = [];

$players = CrawlSuccessionQueue::find(
    ['game_id' => $game->id],
    ['order' => '`position` ASC']
);

foreach ($players as $player) {
    $queue[] = $player;
}

?>

<h2><?=$e($game->character_name)?></h2>

<table class="bordered">
    <tbody>

        <tr>
            <th>Species</th>
            <td><?=$e($game->species)?></td>
        </tr>

        <tr>
            <th>Background</th>
            <td><?=$e($game->background)?></td>
        </tr>

        <tr>
            <th>God(s)</th>
            <td><?=$e($game->god)?></td>
        </tr>

        <tr>
            <th>Server</th>
            <td><?=$e($game->server)?></td>
        </tr>

        <tr>
            <th>Status</th>
            <td><?=$e(ucfirst($game->status))?></td>
        </tr>

        <tr>
            <th>Players</th>
            <td>
                <?=$e(count($queue))?> / <?=$e($game->max_players)?>
            </td>
        </tr>

        <tr>
            <th>Minimum Players to Start</th>
            <td><?=$e($game->min_players)?></td>
        </tr>

        <tr>
            <th>Turn</th>
            <td><?=$e($game->turn_number)?></td>
        </tr>

    </tbody>
</table>

<?php if (!empty($game->description)): ?>

    <h3>Description</h3>

    <p>
        <?=$e($game->description)?>
    </p>

<?php endif; ?>


<h3>Player Queue</h3>

<?php if (empty($queue)): ?>

    <p>No players have joined yet.</p>

<?php else: ?>

    <table class="bordered">

        <thead>
            <tr>
                <th>Position</th>
                <th>Player</th>
            </tr>
        </thead>

        <tbody>

        <?php foreach ($queue as $player): ?>

            <?php
                $users = CrawlSuccessionUser::find([
                    'id' => $player->user_id
                ]);

                $user = null;

                foreach ($users as $found_user) {
                    $user = $found_user;
                    break;
                }
            ?>

            <tr>
                <td><?=$e($player->position)?></td>
                <td>
                    <?=$e($user ? $user->username : 'Unknown')?>

                    <?php if ($player->user_id == $current_user_id): ?>
                        <strong>(You)</strong>
                    <?php endif; ?>
                </td>
            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

<?php endif; ?>


<?php if (!empty($error)): ?>

    <p style="color:red;"><b><?=$e($error)?></b></p>

<?php endif; ?>


<?php if ($game->status === 'planned'): ?>

    <?php
        $already_joined = false;

        if ($current_user_id) {
            foreach ($queue as $player) {
                if ($player->user_id == $current_user_id) {
                    $already_joined = true;
                    break;
                }
            }
        }
    ?>

    <?php if (!$current_user_id): ?>

        <p>
            <a href="/crawl_succession_login">Login to join this succession.</a>
        </p>

    <?php elseif ($already_joined): ?>

        <p>
            You are already in the player queue.
        </p>

    <?php elseif (count($queue) >= $game->max_players): ?>

        <p>
            This succession is full.
        </p>

    <?php else: ?>

        <form method="POST">
            <input type="submit" value="Join the succession">
        </form>

    <?php endif; ?>

<?php endif; ?>


<p>
    <a href="/crawl_succession">Back to CrawlSuccession</a>
</p>
