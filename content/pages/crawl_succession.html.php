<?php $this->layout = 'cca'; ?>

<?php

use app\models\CrawlSuccessionGame;

$games = [
    'active' => [],
    'planned' => [],
    'concluded' => []
];

foreach ($games as $status => &$list) {

    $results = CrawlSuccessionGame::find(
        ['status' => $status],
        ['order' => '`created` DESC']
    );

    foreach ($results as $game) {
        $list[] = $game;
    }
}

unset($list);

?>

<h2>CrawlSuccession</h2>

<h3>Active</h3>

<?php if (empty($games['active'])): ?>

    <p>No active characters.</p>

<?php else: ?>

    <table class="bordered">
        <thead>
            <tr>
                <th>Character</th>
                <th>Server</th>
                <th>Turn</th>
                <th>Current Player</th>
            </tr>
        </thead>
        <tbody>

        <?php foreach ($games['active'] as $game): ?>

            <tr>
                <td>
                    <a href="/crawl_succession/game?id=<?=$e($game->id)?>">
                        <?=$e($game->character_name)?>
                    </a>
                </td>
                <td><?=$e($game->server)?></td>
                <td><?=$e($game->turn_number)?></td>
                <td>
                    <?php if ($game->current_user_id): ?>
                        <?=$e($game->current_user_id)?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>

        <?php endforeach; ?>

        </tbody>
    </table>

<?php endif; ?>


<h3>Planned</h3>

<?php if (empty($games['planned'])): ?>

    <p>No planned characters.</p>

<?php else: ?>

    <table class="bordered">
        <thead>
            <tr>
                <th>Character</th>
                <th>Server</th>
                <th>Players</th>
                <th>Submitted By</th>
            </tr>
        </thead>
        <tbody>

        <?php foreach ($games['planned'] as $game): ?>

            <tr>
                <td>
                    <a href="/crawl_succession/game?id=<?=$e($game->id)?>">
                        <?=$e($game->character_name)?>
                    </a>
                </td>
                <td><?=$e($game->server)?></td>
                <td>
                    0 / <?=$e($game->max_players)?>
                </td>
                <td><?=$e($game->created_by)?></td>
            </tr>

        <?php endforeach; ?>

        </tbody>
    </table>

<?php endif; ?>


<h3>Concluded</h3>

<?php if (empty($games['concluded'])): ?>

    <p>No concluded characters.</p>

<?php else: ?>

    <table class="bordered">
        <thead>
            <tr>
                <th>Character</th>
                <th>Server</th>
                <th>Result</th>
                <th>Turns</th>
            </tr>
        </thead>
        <tbody>

        <?php foreach ($games['concluded'] as $game): ?>

            <tr>
                <td>
                    <a href="/crawl_succession/game?id=<?=$e($game->id)?>">
                        <?=$e($game->character_name)?>
                    </a>
                </td>
                <td><?=$e($game->server)?></td>
                <td><?=$e($game->result ?? '-')?></td>
                <td><?=$e($game->turn_number)?></td>
            </tr>

        <?php endforeach; ?>

        </tbody>
    </table>

<?php endif; ?>


<?php
session_start();

if (!empty($_SESSION['user_id'])):
?>

    <p>
        <a href="/crawl_succession_submit">Submit a new character</a>
        |
        <a href="/crawl_succession_logout">Logout</a>
    </p>

<?php else: ?>

    <p>
        <a href="/crawl_succession_login">Login</a>
        |
        <a href="/crawl_succession_register">Register</a>
    </p>

<?php endif; ?>
