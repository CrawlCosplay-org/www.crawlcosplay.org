<?php $this->layout = 'admin'; ?>

<?php

use app\models\CrawlSuccessionGame;
use app\models\CrawlSuccessionUser;

$games = CrawlSuccessionGame::find(
    ['status' => 'pending'],
    ['order' => '`created` ASC']
);

?>

<h1>CrawlSuccession Games Pending Approval</h1>

<?php if (empty($games)): ?>

    <p>No CrawlSuccession games are currently pending approval.</p>

<?php else: ?>

    <table class="bordered">
        <thead>
            <tr>
                <th>Character</th>
                <th>Species</th>
                <th>Background</th>
                <th>God(s)</th>
                <th>Server</th>
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

            ?>

            <tr>

                <td>
                    <a href="/crawl_succession_game?id=<?=$e($game->id)?>">
                        <?=$e($game->character_name)?>
                    </a>
                </td>

                <td><?=$e($game->species)?></td>

                <td><?=$e($game->background)?></td>

                <td><?=$e($game->god)?></td>

                <td><?=$e($game->server)?></td>

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
                    <a href="/crawl_succession_game?id=<?=$e($game->id)?>">
                        View
                    </a>
                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>
    </table>

<?php endif; ?>

<p>
    <a href="/admin/dashboard">Back to Admin Dashboard</a>
</p>
