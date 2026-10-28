<?php $this->layout = 'admin'; ?>

<?php

use app\models\CrawlSuccessionGame;
use app\models\CrawlSuccessionQueue;
use app\models\CrawlSuccessionUser;

require_once dirname(__DIR__, 5)
    . '/Private/crawl_succession_credentials_crypto.php';
require_once dirname(__DIR__, 5)
    . '/Private/crawl_succession_discord.php';

if ($data = $this->request->getPostData()) {

    $action = $data['action'] ?? '';
    $game_id = (int) ($data['game_id'] ?? 0);

    if ($game_id > 0 && in_array($action, ['approve', 'reject'], true)) {

        $games_to_update = CrawlSuccessionGame::find([
            'id' => $game_id,
            'status' => 'pending'
        ]);

        $game = null;

        foreach ($games_to_update as $found_game) {
            $game = $found_game;
            break;
        }

        if ($game) {

            if ($action === 'approve') {

                if ($game->save([
                    'status' => 'planned'
                ])) {

                    // Automatically add the creator to the queue.
                    $existing_entries = CrawlSuccessionQueue::find([
                        'game_id' => $game->id,
                        'user_id' => $game->created_by
                    ]);

                    $already_queued = false;

                    foreach ($existing_entries as $entry) {
                        $already_queued = true;
                        break;
                    }

                    if (!$already_queued) {

                        $queue_entry = new CrawlSuccessionQueue([
                            'game_id' => $game->id,
                            'user_id' => $game->created_by,
                            'position' => 1,
                            'active' => 0,
                            'joined' => date('Y-m-d H:i:s')
                        ]);

                        $queue_entry->save();
                    }
                    
                    $creators = CrawlSuccessionUser::find([
                        'id' => $game->created_by
                    ]);

                    $creator = null;

                    foreach ($creators as $found_creator) {
                        $creator = $found_creator;
                        break;
                    }

                    $creator_mention = 'Unknown creator';
                    $discord_ids = [];

                    if ($creator) {
                        $creator_mention = $creator->username;

                        if (!empty($creator->discord_id)) {
                            $discord_ids[] = $creator->discord_id;
                            $creator_mention = '<@'
                                . $creator->discord_id
                                . '>';
                        }
                    }

                    $game_url =
                        'https://www.crawlcosplay.org/crawl_succession_game?id='
                        . (int) $game->id;

                    $message =
                        "**New succession game available!**\n\n"
                        . "**Created by:** " . $creator_mention . "\n"
                        . "**Game:** " . $game->game_name . "\n"
                        . "**Game Type:** " . $game->game_type . "\n"
                        . "**Players:** " . (int) $game->min_players
                        . "–" . (int) $game->max_players . "\n\n"
                        . "Sign up and join this game!\n"
                        . "<" . $game_url . ">";

                    sendCrawlSuccessionDiscordNotification(
                        $message,
                        $discord_ids
                    );
                }

            } elseif ($action === 'reject') {

                $game->save([
                    'status' => 'rejected'
                ]);
            }
        }
    }

    return $this->request->redirect('/admin/crawl_succession/list');
}

$games = CrawlSuccessionGame::find(
    ['status' => 'pending'],
    ['order' => '`created` ASC']
);

$active_games = CrawlSuccessionGame::find([
    'status' => 'active'
]);

$active_games = iterator_to_array($active_games);

$games = iterator_to_array($games);
?>

<h2>Crawl Succession Games Pending Approval</h2>

<p>Note to Admin: Check that the Server Username is not currently in use by other games.</p>

<?php if (empty($games)): ?>

    <p>No Crawl Succession games are currently pending approval.</p>

<?php else: ?>

    <table class="bordered">
        <thead>
            <tr>
                <th>Character</th>
                <th>Game Type</th>
                <th>Server</th>
                <th>Server Username</th>
                <th>Username Status</th>
                <th>Players</th>
                <th>Submitted By</th>
                <th>Submitted</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

        <?php foreach ($games as $game): ?>

            <?php

            $users = CrawlSuccessionUser::find([
                'id' => $game->created_by
            ]);

            $user = null;

            foreach ($users as $found_user) {
                $user = $found_user;
                break;
            }

            $server_username = 'Unavailable';

            if (!empty($game->server_username)) {
                try {
                    $server_username = decryptCrawlSuccessionCredential(
                        $game->server_username
                    );
                } catch (\Throwable $exception) {
                    error_log(
                        'CrawlSuccession: Unable to decrypt server username in admin approvals list.'
                    );
                }
            }
                $username_in_use = false;
                $active_game_name = null;

                if ($server_username !== 'Unavailable') {

                    foreach ($active_games as $active_game) {

                        if (empty($active_game->server_username)) {
                            continue;
                        }

                        try {
                            $active_username = decryptCrawlSuccessionCredential(
                                $active_game->server_username
                            );
                            } catch (\Throwable $exception) {
                            error_log(
                                'CrawlSuccession: Unable to decrypt active game server username.'
                            );
                            continue;
                        }

                        if (
                            strcasecmp(
                                trim($server_username),
                                trim($active_username)
                            ) === 0
                        ) {
                            $username_in_use = true;
                            $active_game_name = $active_game->character_name;
                            break;
                        }
                    }
                }
            ?>

            <tr>

                <td>
                    <a href="/crawl_succession_game?id=<?=$e($game->id)?>">
                        <?=$e($game->character_name)?>
                    </a>
                </td>

                <td><?=$e($game->game_type)?></td>

               <td><?=$e($game->server)?></td>

                <td><?=$e($server_username)?></td>

                <td>
                    <?php if ($username_in_use): ?>
                        <strong>
                            Username in use!
                        </strong>
                        <br>
                        Waiting for active game:
                        <?=$e($active_game_name)?>
                    <?php else: ?>
                        Available
                    <?php endif; ?>
                </td>

                <td>
                    <?=$e($game->min_players)?> /
                    <?=$e($game->max_players)?>
                </td>

                <td>
                    <?=$e($user ? $user->username : 'Unknown')?>
                </td>

                <td>
                    <?=$e($game->created)?>
                </td>

                <td>

                    <input
                        type="button"
                        value="View"
                        onclick="window.location='/crawl_succession_game?id=<?=$e($game->id)?>&admin=1';"
                    >

                    |

                    <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="approve">
                    <input type="hidden" name="game_id" value="<?=$e($game->id)?>">
                    <input
                        type="submit"
                        value="Approve"
                        <?= $username_in_use ? 'disabled' : '' ?>
                        onclick="return confirm('Approve this succession game?');"
                    >
                    </form>

                    |

                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="reject">
                        <input type="hidden" name="game_id" value="<?=$e($game->id)?>">
                        <input
                            type="submit"
                            value="Reject"
                            onclick="return confirm('Reject this succession game?');"
                        >
                    </form>

                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>
    </table>

    <br>

<?php endif; ?>

<p>
    <a href="/admin/dashboard">Back to Admin Dashboard</a>
</p>
