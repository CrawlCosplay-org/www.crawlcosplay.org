<?php

use app\models\{Player, Submission};
use alkemann\h2l\Log;

$id = $_GET['id'] ?? false;

if ($id == false) {
    Log::warning("Player page visited without ID parameter");
    return $this->request->redirect('/');
}

$p = Player::get($id);
if (!$p) {
    Log::warning("Player [$id] does not exist!");
    return $this->request->redirect('/');
}

$submissions = $p->submissions();

$tournament_submissions = [];
$normal_submissions = [];
$acadamy_sumissions = [];
$best_by_challenge = [];

foreach ($submissions as $s) {
    $cha = $s->challenge();

    if ($cha->setnr > 30) {
        $tournament_submissions[] = $s;
    } elseif ($cha->setnr == 0) {
        $acadamy_sumissions[] = $s;
    } else {
        $normal_submissions[] = $s;
    }

    if (!$s->accepted) {
        continue;
    }

    $cid = (int) $s->challenge_id;

    if (!isset($best_by_challenge[$cid])) {
        $best_by_challenge[$cid] = $s;
        continue;
    }

    $best = $best_by_challenge[$cid];

    if (
        $s->score > $best->score ||
        (
            $s->score == $best->score &&
            $s->stars > $best->stars
        ) ||
        (
            $s->score == $best->score &&
            $s->stars == $best->stars &&
            $s->created < $best->created
        )
    ) {
        $best_by_challenge[$cid] = $s;
    }
}


/*
 * Split the selected submissions into the same sections
 * as the existing player page.
 */
$best_tournament_submissions = [];
$best_normal_submissions = [];
$best_academy_submissions = [];

foreach ($best_by_challenge as $s) {
    $cha = $s->challenge();

    if ($cha->setnr > 30) {
        $best_tournament_submissions[] = $s;
    } elseif ($cha->setnr == 0) {
        $best_academy_submissions[] = $s;
    } else {
        $best_normal_submissions[] = $s;
    }
}


if ($best_tournament_submissions) :


    $board = [];
    $first_set = 999;
    $last_set = 0;
    
    foreach ($best_tournament_submissions as $sub) {
    $cha = $sub->challenge();
    $set = (int) $cha->setnr;
    $week = (int) $cha->week;

    if ($week < 1 || $week > 6 || $cha->bonus) {
        continue;
    }
        $first_set = $first_set < $set ? $first_set : $set;
        $last_set = $last_set > $set ? $last_set : $set;
        $board[$set][$week] = $sub;
        if (!isset($board[$set]['total'])) $board[$set]['total'] = 0;
        if (!isset($board[$set]['stars'])) $board[$set]['stars'] = 0;
        $board[$set]['total'] += $sub->score;
        $board[$set]['stars'] += $sub->stars;
    }
    for ($set_key=$last_set; $set_key >= $first_set; $set_key--) {
        if (isset($board[$set_key]) == false) {
            $board[$set_key]['total'] = 0;
            $board[$set_key]['stars'] = 0;
        }
    }

?>
<h2>Submissions by <?=$e($p->name)?></h2>

<h3>Crawl Cosplay Trunk Tournaments (CCTT)</h3>
<table class="bordered player-list">
    <thead>
        <tr>
            <th>Trunk</th>
            <th>Total</th>
            <th>Wk 1</th>
            <th>Wk 2</th>
            <th>Wk 3</th>
            <th>Wk 4</th>
            <th>Wk 5</th>
            <th>Wk 6</th>
        </tr>
    </thead>
    <tbody>
    <?php
        $r = 0;
        for ($set_key=$last_set; $set_key >= $first_set; $set_key--) : ?>
        <tr class="<?=$r++%2==0?'odd':'even'?>">
            <th>v0.<?=$set_key?></th>
            <td><?=$board[$set_key]['total']?> <?=$board[$set_key]['stars']?><span class="star">&#9733;</span></td>
        <?php for ($i=1; $i <= 6; $i++) : ?>
            <?php
                $sub = $board[$set_key][$i] ?? false;
                if ($sub === false) {
                    echo '<td>&nbsp;</td>';
                    continue;
                }?>
            <td>
                <?php if ($sub->morgue_url) : ?><a href="<?=$e($sub->morgue_url)?>" target="_blank"><?php endif; ?>
                <?php
                echo $sub->score;
                for ($stars=0; $stars < (int) $sub->stars ; $stars++) {
                    echo '<span class="star">&#9733;</span>';
                }
                ?>
                <?php if ($sub->morgue_url) : ?></a><?php endif; ?>
            </td>
        <?php endfor; ?>
        </tr>
    <?php endfor; ?>
    </tbody>
</table>

<br/>
<?php
endif; // tournament submissions

$has_non_scoring = false;
$board = [];
$first_set = 999;
$last_set = 0;
foreach ($best_normal_submissions as $sub) {
    $cha = $sub->challenge();
    $set = (int) $cha->setnr;
    $week = (int) $cha->week;

    if ($week < 1 || $cha->bonus) {
        continue;
    }
    $first_set = $first_set < $set ? $first_set : $set;
    $last_set = $last_set > $set ? $last_set : $set;
    $board[$set][$week] = $sub;
    if (!isset($board[$set]['total'])) $board[$set]['total'] = 0;
    if (!isset($board[$set]['stars'])) $board[$set]['stars'] = 0;
    $board[$set]['total'] += $sub->score;
    $board[$set]['stars'] += $sub->stars;
}
for ($set_key=$last_set; $set_key >= $first_set; $set_key--) {
    if (isset($board[$set_key]) == false) {
        $board[$set_key]['total'] = 0;
        $board[$set_key]['stars'] = 0;
    }
}
?>
<h3>Regular weekly Crawl Cosplay Challenges (CCC)</h3>


<table class="bordered player-list">
    <thead>
        <tr>
            <th>Set</th>
            <th>Total</th>
            <th>Wk 1</th>
            <th>Wk 2</th>
            <th>Wk 3</th>
            <th>Wk 4</th>
            <th>Wk 5</th>
        </tr>
    </thead>
    <tbody>
    <?php
        $r = 0;
        for ($set_key=$last_set; $set_key >= $first_set; $set_key--) : ?>
        <tr class="<?=$r++%2==0?'odd':'even'?>">
            <th><?=$set_key?></th>
            <td><?=$board[$set_key]['total']?> <?=$board[$set_key]['stars']?><span class="star">&#9733;</span></td>
        <?php for ($i=1; $i <= 5; $i++) : ?>
            <?php
                $sub = $board[$set_key][$i] ?? false;
                if ($sub === false) {
                    echo '<td>&nbsp;</td>';
                    continue;
                }?>
            <td>
                <?php if ($sub->morgue_url) : ?><a href="<?=$e($sub->morgue_url)?>" target="_blank"><?php endif; ?>
                <?php
                echo $sub->score;
                for ($stars=0; $stars < (int) $sub->stars ; $stars++) {
                    echo '<span class="star">&#9733;</span>';
                }
                ?>
                <?php if ($sub->morgue_url) : ?></a><?php endif; ?>
            </td>
        <?php endfor; ?>
        </tr>
    <?php endfor; ?>
    </tbody>
</table>

<br/>

<?php 
if ($best_academy_submissions) :

    $has_non_scoring = false;
    $board = [];

    // All academy submissions are setnr 0 - so no need to worry about first and last
   foreach ($best_academy_submissions as $sub) {
    $cha = $sub->challenge();
    $set = (int) $cha->setnr;
    $week = (int) $cha->week;

    if ($week < 1 || $week > 12 || $cha->bonus) {
        continue;
    }

    $board[$set][$week] = $sub;

    if (!isset($board[$set]['total'])) {
        $board[$set]['total'] = 0;
    }

    if (!isset($board[$set]['stars'])) {
        $board[$set]['stars'] = 0;
    }

    $board[$set]['total'] += (int) $sub->score;
    $board[$set]['stars'] += (int) $sub->stars;
}

    $set_key = 0;

    if (isset($board[$set_key]) == false) { 
        $board[$set_key]['total'] = 0;
        $board[$set_key]['stars'] = 0;
    }
?>
<h3>Crawl Cosplay Academy (CCA) - 12 challenges</h3>
<table class="bordered player-list">
    <thead>
        <tr>
            <th>Total</th>
            <th> 1. </th>
            <th> 2. </th>
            <th> 3. </th>
            <th> 4. </th>
            <th> 5. </th>
            <th> 6. </th>
            <th> 7. </th>
            <th> 8. </th>
            <th> 9. </th>
            <th>10. </th>
            <th>11. </th>
            <th>12. </th>
        </tr>
    </thead>
    <tbody>
    <?php
        $r = 0;
        $set_key = 0;
    ?>
        <tr class="<?=$r++%2==0?'odd':'even'?>'">
        <td><?=$board[$set_key]['total']?> <?=$board[$set_key]['stars']?><span class="star">&#9733;</span></td>
        <?php for ($i=1; $i <= 12; $i++) : ?>
            <?php
                $sub = $board[$set_key][$i] ?? false;
                if ($sub === false) {
                    echo '<td>&nbsp;</td>';
                    continue;
                }?>
            <td>
                <?php if ($sub->morgue_url) : ?><a href="<?=$e($sub->morgue_url)?>" target="_blank"><?php endif; ?>
                <?php
                echo $sub->score;
                for ($stars=0; $stars < (int) $sub->stars ; $stars++) {
                    echo '<span class="star">&#9733;</span>';
                }
                ?>
                <?php if ($sub->morgue_url) : ?></a><?php endif; ?>
            </td>
        <?php endfor; ?>
        </tr>
    </tbody>
</table>
<?php
endif; // acadamy submissions
if ($has_non_scoring) : ?>

<h3>Non-scoring entries</h3>
<table class="bordered player-list">
    <thead>
        <tr>
            <th>Challenge</th>
            <th>Score **</th>
        </tr>
    </thead>
    <tbody>
    <?php
        $r = 0;
        foreach ($submissions as $s) :
            $cha = $s->challenge();
            if ($cha->week <= 5 && $s->hs) continue;
    ?>

        <tr class="<?=$r++%2==0?'odd':'even'?>">
            <td><a href="/ccc/challengedetails?id=<?=$e($cha->id)?>"><?=$e($cha->setnr)?>.<?=$e($cha->week)?> <?=$e($cha->name)?></a></td>
            <td><?php if ($s->morgue_url) : ?><a href="<?=$e($s->morgue_url)?>" target="_blank"><?php endif; ?>
            <?php

            echo $s->score;
            for ($i=0; $i < (int) $s->stars ; $i++) {
                echo '<span class="star">&#9733;</span>';

            }
            ?><?php if ($s->morgue_url) : ?></a><?php endif; ?>
            </td>
        </tr>

    <?php
        endforeach;
    ?>
    </tbody>
</table>
<?php endif; // has non scoring ?>
