<?php

use app\models\CrawlSuccessionGame;
use app\models\CrawlSuccessionQueue;
use app\models\CrawlSuccessionUser;
use app\models\CrawlSuccessionTurn;

require_once dirname(__DIR__, 3)
    . '/Private/crawl_succession_discord.php';
require_once dirname(__DIR__, 3)
    . '/Private/crawl_succession_credentials_crypto.php';

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
$current_user_discord_linked = false;

if ($current_user_id) {
    $users = CrawlSuccessionUser::find([
        'id' => $current_user_id
    ]);

    foreach ($users as $user) {
        $current_user_discord_linked = !empty($user->discord_id);
        break;
    }
}

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


/*
 * Check access to server credentials.
 */
$is_queued_player = false;

if ($current_user_id && $current_user_discord_linked) {
    foreach ($queue as $player) {
        if ((string) $player->user_id === (string) $current_user_id) {
            $is_queued_player = true;
            break;
        }
    }
}

$can_view_credentials =
    $game->status === 'active'
    && $current_user_id
    && $current_user_discord_linked
    && $is_queued_player;

$server_username = null;
$server_password = null;
$credentials_error = false;

if (
    $can_view_credentials
    && !empty($game->server_username)
    && !empty($game->server_password)
) {
    try {
        $server_username = decryptCrawlSuccessionCredential(
            $game->server_username
        );

        $server_password = decryptCrawlSuccessionCredential(
            $game->server_password
        );
    } catch (\Throwable $exception) {
        error_log(
            'CrawlSuccession: Unable to decrypt server credentials.'
        );

        $credentials_error = true;
    }
}

$ready_to_start = count($queue) >= $game->min_players;


/*
 * Handle POST actions.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $post_data = $this->request->getPostData();
    $action = $post_data['action'] ?? '';

/*
 * Admin-only permanent game deletion.
 */
if ($is_admin && $action === 'delete') {

    $queue_entries = CrawlSuccessionQueue::find([
        'game_id' => $game->id
    ]);

    foreach ($queue_entries as $entry) {
        $entry->delete();
    }

    $turns = CrawlSuccessionTurn::find([
        'game_id' => $game->id
    ]);

    foreach ($turns as $turn) {
        $turn->delete();
    }

    $game->delete();

    return $this->request->redirect(
        '/crawl_succession'
    );
}

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
     * Prepare succession game by randomizing the queue.
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

            /*
             * Randomize the queue.
             */
            shuffle($queue);

            $player_count = count($queue);

            /*
             * Move players to temporary positions first to avoid
             * conflicts if queue positions must be unique.
             */
            foreach ($queue as $index => $player) {
                $player->save([
                    'position' => $player_count + $index + 1
                ]);
            }

            /*
             * Save the randomized queue positions permanently.
             */
            foreach ($queue as $index => $player) {
                $player->save([
                    'position' => $index + 1
                ]);
            }

            /*
             * Allocate the 48 standard-route floors across the queue.
             * Any remainder goes to the earliest players.
             */
            $base_floors = intdiv(48, $player_count);
            $extra_floors = 48 % $player_count;

            foreach ($queue as $index => $player) {
                $allocated_floors = $base_floors;

                if ($index < $extra_floors) {
                    $allocated_floors++;
                }

                $player->save([
                    'allocated_floors' => $allocated_floors
                ]);
            }

            return $this->request->redirect(
                '/crawl_succession_game?id=' . $game->id
                . '&confirm_start=1'
            );
        }
    }

    /*
     * Confirm the queue order and start the succession game.
     */
    if ($action === 'confirm_start') {

        if (!$can_edit) {

            $error = "You do not have permission to start this succession game.";

        } elseif ($game->status !== 'planned') {

            $error = "This succession game has already been started.";

        } elseif (!$ready_to_start) {

            $error = "The minimum number of players has not been reached.";

        } elseif (empty($queue)) {

            $error = "There are no players in the queue.";

        } else {

            /*
             * Load the finalized queue in position order.
             */
            $queue = CrawlSuccessionQueue::find(
                ['game_id' => $game->id],
                ['order' => '`position` ASC']
            );

			$queue = iterator_to_array($queue, false);
			
            $first_player = $queue[0];
            $now = date('Y-m-d H:i:s');

            $turn = new CrawlSuccessionTurn([
                'game_id' => $game->id,
                'turn_number' => 1,
                'user_id' => $first_player->user_id,
                'notes' => null,
                'result' => null,
                'started' => $now,
                'finished' => null
            ]);

            if (!$turn->save()) {

                $error = "Unable to create the first turn.";

            } else {

                if ($game->save([
                    'status' => 'active',
                    'current_user_id' => $first_player->user_id,
                    'turn_number' => 1,
                    'started' => $now
                ])) {

                    $first_player->save([
                        'active' => 1
                    ]);

                    $players = crawlSuccessionDiscordPlayers($queue);
                    $mentions = [];
                    $discordIds = [];
                    $firstPlayerName = 'Unknown';

                    foreach ($players as $player) {
                        $mentions[] = $player['mention'];

                        if ($player['discord_id']) {
                            $discordIds[] = $player['discord_id'];
                        }

                        if (
                            $player['user_id']
                            === (string) $first_player->user_id
                        ) {
                            $firstPlayerName = $player['username'];
                        }
                    }

                    $gameUrl = 'https://www.crawlcosplay.org/crawl_succession_game?id='
                        . (int) $game->id;

                    $message =
                        "**CrawlSuccession game started: "
                        . $game->character_name . "**\n"
                        . "Turn 1: **" . $firstPlayerName . "**\n"
                        . "Play **" . (int) $first_player->allocated_floors
                        . " floors** (check player notes for current progress).\n"
                        . "Player order: " . implode(', ', $mentions) . "\n"
                        . "<" . $gameUrl . ">";

                    sendCrawlSuccessionDiscordNotification(
                        $message,
                        $discordIds
                    );

                    return $this->request->redirect(
                        '/crawl_succession_game?id=' . $game->id
                    );

                } else {

                    $turn->delete();

                    $error = "Unable to start the succession game.";
                }
            }
		}
	} // Close the confirm_start action
	
    /*
     * Handle joining the queue.
     */
   if ($action === 'join' && $game->status === 'planned') {

    $discord_linked = false;

    if ($current_user_id) {
        $users = CrawlSuccessionUser::find([
            'id' => $current_user_id
        ]);

        foreach ($users as $user) {
            $discord_linked = !empty($user->discord_id);
            break;
        }
    }

    if (!$discord_linked) {
        $error = "You need to link your Discord account before joining a succession game.";
    } else {

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

    $player_count = count($queue) + 1;

    /*
     * Notify the character creator once when the minimum
     * player count has been reached.
     */
    if (
        $player_count >= $game->min_players
        && empty($game->minimum_players_notified)
    ) {
        /*
         * Save the flag before sending the notification so
         * later joins cannot trigger it again.
         */
        if ($game->save([
            'minimum_players_notified' => 1
        ])) {
            $creators = CrawlSuccessionUser::find([
                'id' => $game->created_by
            ]);

            $creator = null;

            foreach ($creators as $found_creator) {
                $creator = $found_creator;
                break;
            }

            if ($creator) {
                $creator_name = $creator->username;
                $discord_ids = [];
                $creator_mention = $creator_name;

                if (!empty($creator->discord_id)) {
                    $discord_ids[] = $creator->discord_id;
                    $creator_mention = '<@'
                        . $creator->discord_id
                        . '>';
                }

                $game_url =
                    'https://www.crawlcosplay.org/crawl_succession_game?id='
                    . (int) $game->id;

                $message =
                    $creator_mention
                    . ', your succession game for **'
                    . $game->character_name
                    . '** has reached its minimum player count ('
                    . $player_count
                    . ' players). You can start the game whenever you are ready.'
                    . "\n"
                    . '<' . $game_url . '>';

                sendCrawlSuccessionDiscordNotification(
                    $message,
                    $discord_ids
                );
            }
        }
    }

    return $this->request->redirect(
        '/crawl_succession_game?id=' . $game->id
    );
}


            $error = "Unable to join the succession game.";
        }
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

                if ($game->save([
                'status' => 'concluded',
                'current_user_id' => null,
                'concluded' => $now
            ])) {
                $players = crawlSuccessionDiscordPlayers($queue);
                $mentions = [];
                $discordIds = [];

                foreach ($players as $player) {
                    $mentions[] = $player['mention'];

                    if ($player['discord_id']) {
                        $discordIds[] = $player['discord_id'];
                    }
                }

                $gameUrl = 'https://www.crawlcosplay.org/crawl_succession_game?id='
                    . (int) $game->id;

                $message =
                    "**CrawlSuccession game concluded: "
                    . $game->character_name . "**\n"
                    . "Final result: **" . $result . "**\n"
                    . "Players: " . implode(', ', $mentions) . "\n"
                    . "<" . $gameUrl . ">";

                sendCrawlSuccessionDiscordNotification(
                    $message,
                    $discordIds
                );

                return $this->request->redirect(
                    '/crawl_succession_game?id=' . $game->id
                );
            }

            $error = "Unable to conclude the succession game.";
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

    $nextUser = crawlSuccessionDiscordUserInfo(
        $next_player->user_id
    );

    $gameUrl = 'https://www.crawlcosplay.org/crawl_succession_game?id='
        . (int) $game->id;

    if ($nextUser) {
        $recipient = $nextUser['discord_id']
            ? '<@' . $nextUser['discord_id'] . '>'
            : $nextUser['username'];

        
        $total_turns = count($queue);

        $message =
             $recipient . ", it's your turn on **"
            . $game->character_name . "**!\n"
            . "Turn " . $next_turn_number . " of " . $total_turns . "\n"
            . "Play **" . (int) $next_player->allocated_floors
            . " floors** (check player notes for current progress).\n"
            . "<" . $gameUrl . ">";

        
        sendCrawlSuccessionDiscordNotification(
            $message,
            $nextUser['discord_id']
                ? [$nextUser['discord_id']]
                : []
        );
    }

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
 * Update a player's allocated floor count.
 */
if (
    $can_edit
    && $action === 'update_floor_allocation'
    && $game->status === 'active'
) {
    $queue_id = (int) ($post_data['queue_id'] ?? 0);
    $allocated_floors = filter_var(
        $post_data['allocated_floors'] ?? null,
        FILTER_VALIDATE_INT
    );

  $queue_entries = CrawlSuccessionQueue::find([
    'id' => $queue_id,
    'game_id' => $game->id
	]);

	$player = null;

	foreach ($queue_entries as $found_player) {
	    $player = $found_player;
	    break;
	}

    if (
        $player
        && $allocated_floors !== false
        && $allocated_floors !== null
        && $allocated_floors >= 0
        && $allocated_floors <= 48
    ) {
        $player->save([
            'allocated_floors' => $allocated_floors
        ]);
    }

    return $this->request->redirect(
        '/crawl_succession_game?id=' . $game->id
    );
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

                    $redirect_url = '/crawl_succession_game?id=' . $game->id;

                    if (
                        $game->status === 'planned'
                        && isset($post_data['confirm_start'])
                    ) {
                        $redirect_url .= '&confirm_start=1';
                    }

                    return $this->request->redirect($redirect_url);
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

                    $redirect_url = '/crawl_succession_game?id=' . $game->id;

                    if (
                        $game->status === 'planned'
                        && isset($post_data['confirm_start'])
                    ) {
                        $redirect_url .= '&confirm_start=1';
                    }

                    return $this->request->redirect($redirect_url);
                }

            } elseif ($action === 'remove') {

                $next_player = null;

                /*
                 * If removing the active player, find the next player
                 * in queue order before deleting the current player.
                 */
                if (
                    $game->status === 'active'
                    && $target->active
                ) {
                    foreach ($queue as $player) {
                        if (
                            $player->position > $target->position
                            && (
                                $next_player === null
                                || $player->position < $next_player->position
                            )
                        ) {
                            $next_player = $player;
                        }
                    }

                    /*
                     * Do not remove the final remaining player in an
                     * active game, as there would be nobody to take over.
                     */
                    if ($next_player === null) {
                        $error = "This player cannot be removed because there is no next player in the queue.";
                    }
                }

                if (
                    !(
                        $game->status === 'active'
                        && $target->active
                        && $next_player === null
                    )
                ) {
                    $was_active = (
                        $game->status === 'active'
                        && $target->active
                    );

                    $now = date('Y-m-d H:i:s');

                  /*
					 * Remove the current turn record for the removed player.
					 */
						if ($was_active) {
						    $current_turns = CrawlSuccessionTurn::find([
						    'game_id' => $game->id,
    					    'turn_number' => $game->turn_number,
 					      	'user_id' => $target->user_id
					    ]);

					    foreach ($current_turns as $current_turn) {
					        $current_turn->delete();
					        break;
					    }
					}

                    if ($target->delete()) {

                     	/*
						 * Renumber the remaining queue positions.
						 */
						$remaining = CrawlSuccessionQueue::find(
						    ['game_id' => $game->id],
						    ['order' => '`position` ASC']
						);

						$remaining = iterator_to_array($remaining, false);

						$position = 1;

						foreach ($remaining as $player) {
						    $player->save([
						        'position' => $position
						    ]);

						    $position++;
						}

						/*
						 * Automatically redistribute the 48 standard-route floors
						 * among the remaining players.
						 */
						$remaining_count = count($remaining);

						if ($remaining_count > 0) {
						    $base_floors = intdiv(48, $remaining_count);
						    $extra_floors = 48 % $remaining_count;

						    foreach ($remaining as $index => $player) {
						        $allocated_floors = $base_floors;

						        if ($index < $extra_floors) {
						            $allocated_floors++;
						        }

						        $player->save([
						            'allocated_floors' => $allocated_floors
						        ]);
						    }
						}
						
                        /*
                         * Transfer the active turn to the next player.
                         */
                        if ($was_active && $next_player) {

                            $next_turn_number = $game->turn_number;

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

                            if (!$next_turn->save()) {
                                $error = "The player was removed, but the next turn could not be created.";
                            } else {
                                /*
                                 * Notify the next player on Discord.
                                 */
                                $next_user = crawlSuccessionDiscordUserInfo(
                                    $next_player->user_id
                                );

                                if ($next_user) {
                                    $recipient = !empty($next_user['discord_id'])
                                        ? '<@' . $next_user['discord_id'] . '>'
                                        : $next_user['username'];

                                    $game_url =
                                        'https://www.crawlcosplay.org/crawl_succession_game?id='
                                        . (int) $game->id;

                                    $total_turns = count($remaining);

                                    $message =
                                        $recipient
                                        . ", it's your turn on **"
                                        . $game->character_name . "**!\n"
                                        . "Turn " . $next_turn_number
                                        . " of " . $total_turns . "\n"
                                        . "Play **"
                                        . (int) $next_player->allocated_floors
                                        . " floors** (check player notes for current progress).\n"
                                        . "<" . $game_url . ">";

                                    sendCrawlSuccessionDiscordNotification(
                                        $message,
                                        !empty($next_user['discord_id'])
                                            ? [$next_user['discord_id']]
                                            : []
                                    );
                                }
                            }
                        }

                         if ($game->status === 'active') {
                            return $this->request->redirect(
                                '/crawl_succession_game?id=' . $game->id
                            );
                        }

                        $redirect_url =
                            '/crawl_succession_game?id=' . $game->id;

                        if (
                            $game->status === 'planned'
                            && isset($post_data['confirm_start'])
                        ) {
                            $redirect_url .= '&confirm_start=1';
                        }

                        return $this->request->redirect($redirect_url);
                    }

                    $error = "Unable to remove player.";
                }
			}	
        }
    }
} // End POST actions


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

<?php if ($is_admin): ?>

    <h2>Admin Actions</h2>

    <form method="POST" style="display:inline;">
        <input type="hidden" name="action" value="delete">
        <input
            type="submit"
            value="Delete Game"
            onclick="return confirm('Permanently delete this succession game and all of its queue and turn data?');"
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
<table class="bordered" style="font-size: 1.25em;">
    <tbody>

        <tr>
            <th>Species</th>
            <td><?=$e($game->species)?></td>
        </tr>
		
 		<tr>
            <th>Game Type</th>
            <td><?=$e($game->game_type)?></td>
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

		<tr>
			<th>Submitted By</th>
			<td>
                    <?php
                    $users = CrawlSuccessionUser::find([
                        'id' => $game->created_by
                    ]);

                    $user = null;

                    foreach ($users as $found_user) {
                        $user = $found_user;
                        break;
                    }
                    ?>

                    <?=$e($user ? $user->username : 'Unknown')?>
            </td>
		</tr>

    </tbody>
</table>

<?php if ($can_view_credentials): ?>
    <h3>Server Credentials</h3>

    <?php if ($credentials_error): ?>

        <p>Unable to load server credentials.</p>

    <?php elseif (
        $server_username !== null
        && $server_password !== null
    ): ?>

        <p>
            <strong>Username:</strong>
            <code><?=$e($server_username)?></code>
        </p>

        <p>
            <strong>Password:</strong>
            <code><?=$e($server_password)?></code>
        </p>

    <?php else: ?>

        <p>Server credentials are not available.</p>

    <?php endif; ?>
<?php endif; ?>

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
    
<?php  
echo '<p><strong><u>Standard route (48 floors):</u></strong> Dungeon (15) → Lair (5) → Orc (2) → S-branches (8) → Elf (3) → Vaults:1–4 (4) → Slime or Vaults:5 (1) → Depths (4) → Zot (5) → Orb Run (1). Extra floors outside the standard branch order are allowed. Check the player notes or Ctrl + o to see what\'s next.</p>';
?>

    <table class="bordered" style="font-size: 1.25em;">

       
        <thead>
            <tr>
                <th>Position</th>
                <th>Player</th>

                <?php if ($game->status === 'active'): ?>
                    <th>Floors</th>
                <?php endif; ?>

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
              
                
                <?php if ($game->status === 'active'): ?>
                    <td>
                        <?php if ($can_edit): ?>
                            <div id="floor-display-<?= (int) $player->id ?>">
                                <?= $e($player->allocated_floors) ?>
                                <button
                                    type="button"
                                    onclick="document.getElementById('floor-display-<?= (int) $player->id ?>').style.display='none'; document.getElementById('floor-edit-<?= (int) $player->id ?>').style.display='inline';"
                                >Edit</button>
                            </div>

                            <form
                                id="floor-edit-<?= (int) $player->id ?>"
                                method="POST"
                                style="display: none;"
                            >
                                <input
                                    type="hidden"
                                    name="action"
                                    value="update_floor_allocation"
                                >
                                <input
                                    type="hidden"
                                    name="queue_id"
                                    value="<?= (int) $player->id ?>"
                                >
                                <input
                                    type="number"
                                    name="allocated_floors"
                                    min="0"
                                    max="48"
                                    step="1"
                                    required
                                    value="<?= (int) $player->allocated_floors ?>"
                                    style="width: 65px;"
                                >
                                <button type="submit">Save</button>
                                <button
                                    type="button"
                                    onclick="document.getElementById('floor-edit-<?= (int) $player->id ?>').style.display='none'; document.getElementById('floor-display-<?= (int) $player->id ?>').style.display='inline';"
                                >Cancel</button>
                            </form>
                        <?php else: ?>
                            <?= $e($player->allocated_floors) ?>
                        <?php endif; ?>
                    </td>
                <?php endif; ?>


                <?php if ($can_edit): ?>

                    <td>

                        <?php if ($player->position > 1): ?>

                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="up">
                                <input type="hidden" name="player_id" value="<?=$e($player->id)?>">
                                <?php if (isset($_GET['confirm_start'])): ?>
                                    <input type="hidden" name="confirm_start" value="1">
                                <?php endif; ?>
                                <input type="submit" value="Move Up">
                            </form>

                        <?php endif; ?>

                        <?php if ($player->position < count($queue)): ?>

                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="down">
                                <input type="hidden" name="player_id" value="<?=$e($player->id)?>">
                                <?php if (isset($_GET['confirm_start'])): ?>
                                    <input type="hidden" name="confirm_start" value="1">
                                <?php endif; ?>
                                <input type="submit" value="Move Down">
                            </form>

                        <?php endif; ?>

                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="player_id" value="<?=$e($player->id)?>">
                            <?php if (isset($_GET['confirm_start'])): ?>
                                <input type="hidden" name="confirm_start" value="1">
                            <?php endif; ?>
                            <input
                                type="submit"
                                value="Remove"
                                onclick="return confirm('Remove this player from the succession game?');"
                            >
                        </form>

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

    <div style="
        border: 2px solid #d99a24;
        background-color: #fff4d6;
        padding: 16px 20px;
        margin: 20px 0;
        border-radius: 6px;
    ">
        <h2 style="margin-top: 0; color: #8B0000;">
            You need to log in before you can join this succession game.
        </h2>

        <input
            type="button"
            value="Login"
            onclick="window.location='/crawl_succession_login';"
        >
    </div>


<?php elseif (empty($current_user_discord_linked)): ?>

   <div style="
    border: 2px solid #d99a24;
    background-color: #fff4d6;
    padding: 16px 20px;
    margin: 20px 0;
    border-radius: 6px;
">
    <h2 style="margin-top: 0; color: #8B0000;">
        You need to link your Discord account before you can
        join this succession game.
    </h2>

    <input
        type="button"
        value="Link Discord Account"
        onclick="window.location='/link_discord';"
    >
</div>

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

       <?php if ($can_edit && $ready_to_start && $game->status === 'planned'): ?>

        <?php if (isset($_GET['confirm_start'])): ?>

            <form method="POST" style="margin-top: 1em;">
                <input type="hidden" name="action" value="confirm_start">
                <input
                    type="submit"
                    value="Confirm and Start Succession Game"
                    onclick="return confirm('Confirm the player order and start this succession game?');"
                >
            </form>

        <?php else: ?>

            <form method="POST" style="margin-top: 1em;">
                <input type="hidden" name="action" value="start">
                <input
                    type="submit"
                    value="Ready to Start?"
                    onclick="return confirm('Randomize the player queue so you can review and rearrange the order before starting?');"
                >
            </form>

         <?php endif; ?> <!-- confirm_start -->

    <?php endif; ?> <!-- start controls -->

<?php endif; ?> <!-- game status is planned -->
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
    value="Back to Crawl Succession"
    onclick="window.location='/crawl_succession';"
>
<br>
