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

$is_admin = !empty($_SESSION['admin']);

$can_edit = (
    $current_user_id == $game->created_by
    || $is_admin
);


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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $post_data = $this->request->getPostData();
    $action = $post_data['action'] ?? '';

    if ($is_admin && in_array($action, ['approve', 'reject'], true)) {

    if ($game->status !== 'pending') {

        $error = "This succession game is no longer pending approval.";

    } elseif ($action === 'approve') {

        $game->save([
            'status' => 'planned'
        ]);

        return $this->request->redirect(
            '/crawl_succession_game?id=' . $game->id
        );

    } elseif ($action === 'reject') {

        $game->save([
            'status' => 'rejected'
        ]);

        return $this->request->redirect(
            '/admin/crawl_succession/list'
        );
    }
}


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
            $now = date('Y-m-d H:i:s');

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
                'started' => $now
            ]);

            /*
             * Create the first turn.
             */
            $turn = new CrawlSuccessionTurn([
                'game_id' => $game->id,
                'turn_number' => 1,
                'user_id' => $first_player->user_id,
                'notes' => null,
                'result' => null,
                'started' => $now,
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
 * Handle finishing the current turn.
 */
if ($action === 'finish_turn') {

    if ($game->status !== 'active') {

        $error = "This succession game is not active.";

    } elseif ($game->current_user_id != $current_user_id) {

        $error = "It is not your turn.";

    } else {

        $notes = trim($post_data['notes'] ?? '');
        $now = date('Y-m-d H:i:s');

        /*
         * Find the current active queue entry.
         */
        $current_player = null;

        foreach ($queue as $player) {

            if (
                $player->user_id == $current_user_id
                && $player->active
            ) {
                $current_player = $player;
                break;
            }
        }

        if (!$current_player) {

            $error = "Unable to find your active turn.";

        } else {

            /*
             * Find the current turn record.
             */
            $turns = CrawlSuccessionTurn::find([
                'game_id' => $game->id,
                'turn_number' => $game->turn_number,
                'user_id' => $current_user_id
            ]);

            $current_turn = null;

            foreach ($turns as $found_turn) {
                $current_turn = $found_turn;
                break;
            }

            if (!$current_turn) {

                $error = "Unable to find the current turn record.";

            } else {

                /*
                 * Determine whether this is the final player.
                 */
                $is_final_player = (
                    $current_player->position == count($queue)
                );

                $result = trim($post_data['result'] ?? '');

                /*
                 * Save the current turn.
                 */
                if (!$current_turn->save([
                    'notes' => $notes,
                    'result' => $is_final_player ? $result : null,
                    'finished' => $now
                ])) {

                    $error = "Unable to finish the current turn.";

                } else {

                    /*
                     * Deactivate the current player.
                     */
                    $current_player->save([
                        'active' => 0
                    ]);

                    /*
                     * If this is the final player, conclude the succession game.
                     */
                    if ($is_final_player) {

                        $game->save([
                            'status' => 'concluded',
                            'current_user_id' => null,
                            'concluded' => $now
                        ]);

                        return $this->request->redirect(
                            '/crawl_succession_game?id=' . $game->id
                        );
                    }

                    /*
                     * Find the next player in the queue.
                     */
                    $next_player = null;

                    foreach ($queue as $player) {

                        if (
                            $player->position > $current_player->position
                        ) {
                            $next_player = $player;
                            break;
                        }
                    }

                    /*
                     * Start the next player's turn.
                     */
                    if ($next_player) {

                        $next_turn_number = $game->turn_number + 1;

                        $next_player->save([
                            'active' => 1
                        ]);

                        $game->save([
                            'current_user_id' => $next_player->user_id,
                            'turn_number' => $next_turn_number
                        ]);

                        $next_turn = new CrawlSuccessionTurn([
                            'game_id' => $game->id,
                            'turn_number' => $next_turn_number,
                            'user_id' => $next_player->user_id,
                            'notes' => null,
                            'result' => null,
                            'started' => $now,
                            'finished' => null
                        ]);

                        if ($next_turn->save()) {

                            return $this->request->redirect(
                                '/crawl_succession_game?id=' . $game->id
                            );
                        }

                        $error = "Unable to create the next turn.";

                    } else {

                        $error = "Unable to find the next player.";
                    }
                }
            }
        }
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

        $player_id = (int) ($post_data['player_id'] ?? 0);

        $target = null;

        foreach ($queue as $player) {

            if ($player->id == $player_id) {
                $target = $player;
                break;
            }
        }

        if (!$target) {

            $error = "Player not found.";

        } elseif (
            $action === 'remove'
            && $game->status === 'active'
            && $target->active
        ) {

            $error = "The current player cannot be removed while their turn is active.";

        } else {

            if ($action === 'up') {

                $previous = null;

                foreach ($queue as $player) {

                    if ($player->position < $target->position) {

                        if (
                            $previous === null
                            || $player->position > $previous->position
                        ) {
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

                        if (
                            $next === null
                            || $player->position < $next->position
                        ) {
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


/*
 * Determine whether the logged-in user is the current player.
 */
$is_current_player = (
    $game->status === 'active'
    && $current_user_id
    && $game->current_user_id == $current_user_id
);

/*
 * Determine whether the current player is the final player.
 */
$is_final_player = false;

if ($is_current_player && !empty($queue)) {

    foreach ($queue as $player) {

        if ($player->user_id == $current_user_id) {

            $is_final_player = ($player->position == count($queue));

            break;
        }
    }
}

/*
 * Load turn history.
 */
$turns = [];

$turn_results = CrawlSuccessionTurn::find(
    ['game_id' => $game->id],
    ['order' => '`turn_number` ASC']
);

foreach ($turn_results as $turn) {
    $turns[] = $turn;
}

/*
 * Get the final result.
 */
$final_result = null;

if ($game->status === 'concluded' && !empty($turns)) {

    $last_turn = end($turns);

    if (!empty($last_turn->result)) {
        $final_result = $last_turn->result;
    }
}

?>

<h1><?=$e($game->character_name)?></h1>
<br>
<br>

<?php if ($is_admin && $game->status === 'pending'): ?>

    <h2>Admin Review</h2>

    <form method="POST" style="display:inline;">
        <input type="hidden" name="action" value="approve">
        <input
            type="submit"
            value="Approve"
            onclick="return confirm('Approve this succession game?');"
        >
    </form>

    <form method="POST" style="display:inline;">
        <input type="hidden" name="action" value="reject">
        <input
            type="submit"
            value="Reject"
            onclick="return confirm('Reject this succession game?');"
        >
    </form>

    <br>
    <br>

<?php endif; ?>

<?php if ($game->status === 'concluded' && !empty($final_result)): ?>

    <div class="succession-result">
        <h2>GAME CONCLUDED</h2>
        <p>
            <?=$e($final_result)?>
        </p>
    </div>

<?php endif; ?>
<br>
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
                    Ready to Start Succession Game
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


<?php if ($is_current_player): ?>

    <h3>Your Turn</h3>

    <form method="POST">

        <input type="hidden" name="action" value="finish_turn">

        <fieldset>

            <label>
                <span>Turn Notes</span><br />
                <textarea
                    name="notes"
                    rows="8"
                    cols="60"
                    placeholder="Describe what happened during your turn..."
                ></textarea>
            </label>

            <?php if ($is_final_player): ?>

                <br /><br />

                    <label>
                        <span>Result</span><br />
                            <input
                            type="text"
                            name="result"
                            maxlength="100"
                            placeholder="Win/Lose, Rune Count, Gems, Other Facts..."    
                            required
                            />
                    </label>

                    <br />

                    <p>
                    <strong>This is the final turn.</strong>
                    Completing it will conclude the succession game.
                    </p>

            <?php endif; ?>

            <br /><br />

            <input
                type="submit"
                value="Finish Turn"
                onclick="return confirm('<?=$is_final_player ? 'Conclude the game?' : 'Finish your turn and pass to the next player?'?>');"
            >

        </fieldset>

    </form>

<?php endif; ?>

<br>
<h2>Player Queue</h2>

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

<br>
<h2>Turn History</h2>

<?php if (empty($turns)): ?>

    <p>No turns have been completed yet.</p>

<?php else: ?>

    <?php foreach ($turns as $turn): ?>

        <?php
            $users = CrawlSuccessionUser::find([
                'id' => $turn->user_id
            ]);

            $user = null;

            foreach ($users as $found_user) {
                $user = $found_user;
                break;
            }
        ?>

        <h4>
            Turn <?=$e($turn->turn_number)?>
            —
            <?=$e($user ? $user->username : 'Unknown')?>
        </h4>

        <p>
            <strong>Started:</strong>
            <?=$e($turn->started ?? '-')?>
            <br />

            <strong>Finished:</strong>
            <?=$e($turn->finished ?? 'In progress')?>
        </p>

        <?php if (!empty($turn->notes)): ?>

            <p>
                <?=$e($turn->notes)?>
            </p>

        <?php else: ?>

            <p>
                <em>No notes recorded.</em>
            </p>

        <?php endif; ?>
    
        <?php if (!empty($turn->result)): ?>

            <p>
                <strong>Result:</strong>
                <?=$e($turn->result)?>
            </p>

        <?php endif; ?>

    <?php endforeach; ?>

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
<br>

<?php if ($can_edit): ?>

    <input
        type="button"
        value="Edit Character"
        onclick="window.location='/crawl_succession_edit?id=<?=$e($game->id)?>';"
    >

<?php endif; ?>

<input
    type="button"
    value="Back to CrawlSuccession"
    onclick="window.location='/crawl_succession';"
>
