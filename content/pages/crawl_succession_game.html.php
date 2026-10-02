<?php

use app\models\CrawlSuccessionGame;
use app\models\CrawlSuccessionQueue;
use app\models\CrawlSuccessionUser;
use app\models\CrawlSuccessionTurn;

session_start();

$id = (int) ($this->request->getGetData()['id'] ?? 0);

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

$can_edit = ($current_user_id == $game->created_by);


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

$ready_to_start = count($queue) >= $game->min_players;


/*
 * Handle POST actions.
 */
if ($current_user_id && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $this->request->getPostData()['action'] ?? '';


    /*
     * Start succession game.
     */
    if ($action === 'start') {

        if (!$can_edit) {

            $error = "You do not have permission to start this succession game.";

        } elseif ($game->status !== 'planned') {

            $error = "This succession game has already been started.";

        } elseif (!$ready_to_start) {

            $error = "The minimum number of players has not been reached.";

        } elseif (empty($queue)) {

            $error = "There are no players in the queue.";

        } else {

            $first_player = $queue[0];

            /*
             * Make the first player active.
             */
            $first_player->save([
                'active' => 1
            ]);

            /*
             * Start the game.
             */
            $game->save([
                'status' => 'active',
                'current_user_id' => $first_player->user_id,
                'turn_number' => 1,
                'started' => date('Y-m-d H:i:s')
            ]);

            /*
             * Create the first turn.
             */
            $turn = new CrawlSuccessionTurn([
                'game_id' => $game->id,
                'turn_number' => 1,
                'user_id' => $first_player->user_id,
                'notes' => null,
                'started' => date('Y-m-d H:i:s'),
                'finished' => null
            ]);

            if ($turn->save()) {

                return $this->request->redirect(
                    '/crawl_succession_game?id=' . $game->id
                );
            }

            $error = "Unable to create the first turn.";
        }
    }


    /*
     * Handle joining the queue.
     */
    if ($action === 'join' && $game->status === 'planned') {

        $already_joined = false;

        foreach ($queue as $player) {

            if ($player->user_id == $current_user_id) {
                $already_joined = true;
                break;
            }
        }

        if ($already_joined) {

            $error = "You are already in this succession game.";

        } elseif (count($queue) >= $game->max_players) {

            $error = "This succession game is full.";

        } else {

            $highest_position = 0;

            foreach ($queue as $player) {

                if ($player->position > $highest_position) {
                    $highest_position = $player->position;
                }
            }

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

            $error = "Unable to join the succession game.";
        }
    }
        /*
     * Handle queue management.
     */
    if (
        $can_edit
        && ($game->status === 'planned' || $game->status === 'active')
        && in_array($action, ['up', 'down', 'remove'], true)
    ) {

        $player_id = (int) ($this->request->getPostData()['player_id'] ?? 0);

        $target = null;

        foreach ($queue as $player) {

            if ($player->id == $player_id) {
                $target = $player;
                break;
            }
        }

        if (!$target) {

            $error = "Player not found.";

        } elseif ($action === 'remove' && $game->status === 'active' && $target->active) {

            $error = "The current player cannot be removed while their turn is active.";

        } else {

            if ($action === 'up') {

                $previous = null;

                foreach ($queue as $player) {

                    if ($player->position < $target->position) {

                        if ($previous === null || $player->position > $previous->position) {
                            $previous = $player;
                        }
                    }
                }

                if ($previous) {

                    $target_position = $target->position;
                    $previous_position = $previous->position;

                    $target->save([
                        'position' => $previous_position
                    ]);

                    $previous->save([
                        'position' => $target_position
                    ]);

                    return $this->request->redirect(
                        '/crawl_succession_game?id=' . $game->id
                    );
                }

            } elseif ($action === 'down') {

                $next = null;

                foreach ($queue as $player) {

                    if ($player->position > $target->position) {

                        if ($next === null || $player->position < $next->position) {
                            $next = $player;
                        }
                    }
                }

                if ($next) {

                    $target_position = $target->position;
                    $next_position = $next->position;

                    $target->save([
                        'position' => $next_position
                    ]);

                    $next->save([
                        'position' => $target_position
                    ]);

                    return $this->request->redirect(
                        '/crawl_succession_game?id=' . $game->id
                    );
                }

            } elseif ($action === 'remove') {

                if ($target->delete()) {

                    /*
                     * Renumber the remaining queue positions.
                     */
                    $remaining = CrawlSuccessionQueue::find(
                        ['game_id' => $game->id],
                        ['order' => '`position` ASC']
                    );

                    $position = 1;

                    foreach ($remaining as $player) {

                        $player->save([
                            'position' => $position
                        ]);

                        $position++;
                    }

                    return $this->request->redirect(
                        '/crawl_succession_game?id=' . $game->id
                    );
                }

                $error = "Unable to remove player.";
            }
        }
    }
}


/*
 * Reload the queue after any POST handling.
 */
$queue = [];

$players = CrawlSuccessionQueue::find(
    ['game_id' => $game->id],
    ['order' => '`position` ASC']
);

foreach ($players as $player) {
    $queue[] = $player;
}

$ready_to_start = count($queue) >= $game->min_players;

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
            <td>
                <?php if ($game->status === 'planned' && $ready_to_start): ?>
                    Ready to Start
                <?php else: ?>
                    <?=$e(ucfirst($game->status))?>
                <?php endif; ?>
            </td>
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

                <?php if ($can_edit): ?>
                    <th>Manage</th>
                <?php endif; ?>
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

                    <?php if ($player->active): ?>
                        <strong>(Current Turn)</strong>
                    <?php endif; ?>
                </td>

                <?php if ($can_edit): ?>

    <td>

        <?php if ($player->position > 1): ?>

            <form method="POST" style="display:inline;">
                <input type="hidden" name="action" value="up">
                <input type="hidden" name="player_id" value="<?=$e($player->id)?>">
                <input type="submit" value="Move Up">
            </form>

        <?php endif; ?>

        <?php if ($player->position < count($queue)): ?>

            <form method="POST" style="display:inline;">
                <input type="hidden" name="action" value="down">
                <input type="hidden" name="player_id" value="<?=$e($player->id)?>">
                <input type="submit" value="Move Down">
            </form>

        <?php endif; ?>

        <?php if (!($game->status === 'active' && $player->active)): ?>

            <form method="POST" style="display:inline;">
                <input type="hidden" name="action" value="remove">
                <input type="hidden" name="player_id" value="<?=$e($player->id)?>">
                <input
                    type="submit"
                    value="Remove"
                    onclick="return confirm('Remove this player from the succession game?');"
                >
            </form>

        <?php endif; ?>

        </td>

        <?php endif; ?>
    
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
            <a href="/crawl_succession_login">Login to join this succession game.</a>
        </p>

    <?php elseif ($already_joined): ?>

        <p>
            You are already in the player queue.
        </p>

    <?php elseif (count($queue) >= $game->max_players): ?>

        <p>
            This succession game is full.
        </p>

    <?php else: ?>

        <form method="POST">
            <input type="hidden" name="action" value="join">
            <input type="submit" value="Join the succession game">
        </form>

    <?php endif; ?>


    <?php if ($can_edit && $ready_to_start): ?>

        <form method="POST" style="margin-top: 1em;">
            <input type="hidden" name="action" value="start">
            <input
                type="submit"
                value="Start Succession Game"
                onclick="return confirm('Start this succession game? The player queue will be locked.');"
            >
        </form>

    <?php endif; ?>

<?php endif; ?>


<?php if ($can_edit): ?>

    <p>
        <a href="/crawl_succession_edit?id=<?=$e($game->id)?>">
            Edit Character
        </a>
    </p>

<?php endif; ?>


<p>
    <a href="/crawl_succession">Back to CrawlSuccession</a>
</p>
