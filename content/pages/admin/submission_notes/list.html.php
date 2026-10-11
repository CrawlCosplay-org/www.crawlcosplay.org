<?php $this->layout = 'admin'; ?>

<?php

use app\models\Challenge;
use app\models\Submission;

// Configure which challenge sets and weeks to display.
// Add more set numbers to include future tournament sets.
$included_sets = [35];
$min_week = 1;
$max_week = 5;
$tournament_labels = [
    35 => 'CCTT .35',
    // 36 => 'CCTT .36',
    // Add future set labels here.
];

$challenges = Challenge::list();
$challenges_with_notes = [];

foreach ($challenges as $id => $name) {
    $challenge = Challenge::get($id);

    if (!$challenge) {
        continue;
    }

    // Only include the configured sets and weeks.
    if (
        !in_array((int) $challenge->setnr, $included_sets, true)
        || (int) $challenge->week < $min_week
        || (int) $challenge->week > $max_week
    ) {
        continue;
    }

    // Find submissions for this challenge.
    $submissions = Submission::findAsArray([
        'challenge_id' => (int) $id
    ]);

    $note_count = 0;

    foreach ($submissions as $submission) {
        if (trim((string) $submission->comment) !== '') {
            $note_count++;
        }
    }

    // Only list challenges that have notes.
    if ($note_count > 0) {
        $challenges_with_notes[] = [
            'id' => $id,
            'setnr' => (int) $challenge->setnr,
            'week' => (int) $challenge->week,
            'name' => $name,
            'note_count' => $note_count
        ];
    }
}

// Sort by set, then week.
usort($challenges_with_notes, function ($a, $b) {
    if ($a['setnr'] !== $b['setnr']) {
        return $a['setnr'] <=> $b['setnr'];
    }

    return $a['week'] <=> $b['week'];
});

?>

<h2>Tournament Submission Notes</h2>

<table class="bordered">
    <thead>
        <tr>
            <th>Tournament</th>
            <th>Week</th>
            <th>Challenge</th>
            <th>Submissions with Notes</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($challenges_with_notes)) : ?>
        <tr>
            <td colspan="5">No submission notes found.</td>
        </tr>
        <?php else : ?>
            <?php $r = 0; ?>
            <?php foreach ($challenges_with_notes as $challenge) : ?>
            <tr class="<?=$r++ % 2 == 0 ? 'odd' : 'even'?>">
                <td><?=htmlspecialchars(
                    $tournament_labels[$challenge['setnr']] ?? 'Set ' . $challenge['setnr'],
                    ENT_QUOTES,
                    'UTF-8'
                    )?>
                </td>
                <td><?=$challenge['week']?></td>
                <td><?=htmlspecialchars((string) $challenge['name'], ENT_QUOTES, 'UTF-8')?></td>
                <td><?=$challenge['note_count']?></td>
                <td class="actions-td">
                    <a href="/admin/submission_notes/view?id=<?=(int) $challenge['id']?>">
                        View Notes
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
