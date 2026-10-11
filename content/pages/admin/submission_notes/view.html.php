<?php $this->layout = 'admin'; ?>

<?php

use app\models\Challenge;
use app\models\Submission;

// Get and validate the challenge ID.
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || !($challenge = Challenge::get($id))) {
    return $this->request->redirect('/admin/submission_notes/list');
}

// Get submissions for this challenge, newest first.
$submissions = Submission::findAsArray(
    ['challenge_id' => (int) $id],
    ['order' => '`created` DESC']
);

$submissions_with_notes = [];

foreach ($submissions as $submission) {
    if (trim((string) $submission->comment) !== '') {
        $submissions_with_notes[] = $submission;
    }
}

?>

<h2>
    Submission Notes: Set <?=$challenge->setnr?>.<?=$challenge->week?>
    - <?=htmlspecialchars((string) $challenge->name, ENT_QUOTES, 'UTF-8')?>
</h2>

<p>
    <a href="/admin/submission_notes/list">
        &larr; Back to Tournament Submission Notes
    </a>
</p>

<table class="bordered">
    <thead>
        <tr>
            <th>Player</th>
            <th>Submitted</th>
            <th>Notes</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($submissions_with_notes)) : ?>
        <tr>
            <td colspan="3">No submission notes found for this challenge.</td>
        </tr>
        <?php else : ?>
            <?php $r = 0; ?>
            <?php foreach ($submissions_with_notes as $submission) : ?>
            <?php $player = $submission->player(); ?>
            <tr class="<?=$r++ % 2 == 0 ? 'odd' : 'even'?>">
                <td>
                    <?=htmlspecialchars(
                        (string) ($player ? $player->name : 'Unknown player'),
                        ENT_QUOTES,
                        'UTF-8'
                    )?>
                </td>
                <td>
                    <?=htmlspecialchars((string) $submission->created, ENT_QUOTES, 'UTF-8')?>
                </td>
                <td>
                    <?=nl2br(htmlspecialchars(
                        (string) $submission->comment,
                        ENT_QUOTES,
                        'UTF-8'
                    ))?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
