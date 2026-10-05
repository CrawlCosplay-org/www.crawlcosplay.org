<?php $this->layout = 'admin'; ?>

<?php

use app\models\CrawlSuccessionGame;
use app\models\CrawlSuccessionUser;

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

                $game->save([
                    'status' => 'planned'
                ]);

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

?>

<h2>Crawl Succession Games Pending Approval</h2>
<br>
<?php if (empty($games)): ?>

    <p>No Crawl Succession games are currently pending approval.</p>

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
