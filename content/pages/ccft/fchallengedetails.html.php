<?php

use app\models\{Challenge, Submission};

$this->layout = 'ccft';

$id = $_GET['id'] ?? false;
if ($id == false) {
	return $this->request->redirect('/');
}

$cha = Challenge::get($id);
if (!$cha) {
	return $this->request->redirect('/');
}

$is_admin = $this->request->session('admin');
if ($cha->draft && !$is_admin) {
	return $this->request->redirect('/');
}

$name = $e($cha->name);
$this->setData("page_title", "{$name} - Crawl Cosplay Forks Tournament");

$this->setData("meta", ['filename' => $cha->icon]);

?>
<div class="challenge">

<h2>CCFT #<?=abs($e($cha->setnr))?> Week <?=$e($cha->week)?> : <?=$e($cha->name)?></h2>
<p style="font-style: italic; color: #777;"><?=$e($cha->description)?></p>
<!-- shortform field is used for Monster Speak -->
<p style="text-align:right;"><span style="font-size: smaller"><?=$e($cha->shortform)?></span></p>

<!-- Update Fork Details link with each week -->
<p>
	<?php if ($cha->active) : ?>
		<a href="/ccft/submit_ccft?id=<?=$cha->id?>">Submit a CCFT run</a>
	<?php else: ?>
		Submission Closed
	<?php endif;?>
	<?php if ($cha->wiki): ?> |
	<a href="<?=$e($cha->wiki)?>" target="_blank">Wiki page</a><?php endif; ?>
	| <a href="https://www.crawlcosplay.org/ccft/fork_bcrawl.html">Fork details</a>
</p>

<?php if ($cha->icon) : ?><img src="<?=$e($cha->icon)?>" class="detail" /><?php endif; ?>
<table class="table_for_layout">
	<tr><th>Species</th><th>Background<th>Gods</th></tr>
	<tr><td><?=$e($cha->species)?></td><td><?=$e($cha->background)?><td><?=$e($cha->gods)?></td></tr>
</table>
<p class="info">The Species, Background, and God choices are all mandatory. You must be worshipping one of the gods listed above before entering Lair, 
	Orcish Mines, the Vaults or Depths, unless this isn't possible in which case you must worship them as soon as you can. 
	Don't use faded altars (except in challenges where you can choose any god), and don't do anything to lose your religion unless otherwise specified.</p>
<p class="info">IMPORTANT NOTES:<br>
	- Except for Beogh, Ignis, Jiyva and Lugonu, an altar for the other gods will ALWAYS show up by D:10. (Some forks may differ, see Ctrl + o in game for details).<br>
	- Small mistakes will likely NOT be forgiven unless they are small. (If you are unsure, please ask ASAP in our CC Discord linked above.)</p>

<?php if ($cha->special_rule) : ?>
<h3>Special Rule or Notes</h3>
<div class="special_rule"><p><?=$em($cha->special_rule)?></p></div>
<?php endif; ?>

<div class="score-sidebar">
<h3>Submissions</h3>
<br />
<table class="bordered">
	<thead>
		<tr>
			<th>Player</th>
			<th>Score<span class="star">&#9733;&#9733;</span></th>
			<th>Year</th>
		</tr>
	</thead>
	<tbody>
	<?php
		$subs = Submission::scoreboard($id);
		$r = 0;
		foreach ($subs as $s) :
			$year_created = explode('-', $s->created)[0];
	?>

		<tr class="<?=$r++%2==0?'odd':'even'?>">
			<?php
				$player = $s->player();
				$player_name = $player->name;
				$player_id = $player->id;
			?>
			<td><a href="/player?id=<?=$player_id?>"><?=$e($player_name)?></a></td>
			<td>
			<?php
			if (!empty($s->morgue_url)) echo '<a href="'.$s->morgue_url.'" target="_blank">';
			echo $s->score;
			for ($i=0; $i < (int) $s->stars ; $i++) {
				echo '<span class="star">&#9733;</span>';
			}
			if (!empty($s->morgue_url)) echo '</a>';
			?>
			</td>
			<td><?=$year_created ?></td>
		</tr>

	<?php
		endforeach;
	?>
	</tbody>
</table>
</div>

<!-- Adds countdown timer, update Epoch time each week! -->
	
<div style="text-align:center;">
    <h3>Week 4 ends in:</h3>
    <div id="countdown" style="font-size:24px; font-weight:bold;"></div>
</div>
<script>
const target = 1787875200 * 1000;

function updateCountdown() {
    const remaining = target - Date.now();

    if (remaining <= 0) {
        document.getElementById("countdown").textContent = "Week complete!";
        return;
    }

    const totalSeconds = Math.floor(remaining / 1000);

    const days = Math.floor(totalSeconds / 86400);
    const hours = Math.floor((totalSeconds % 86400) / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);
    const seconds = totalSeconds % 60;

    document.getElementById("countdown").textContent =
        days + "d " +
        hours.toString().padStart(2, "0") + "h " +
        minutes.toString().padStart(2, "0") + "m " +
        seconds.toString().padStart(2, "0") + "s";
}

updateCountdown();
setInterval(updateCountdown, 1000);
</script>

	
<h3>Cosplay conduct points</h3>
<dl>
	<dt>1. <?=$e($cha->conduct_name_1)?></dt><dd><?=$em($cha->conduct_1)?></dd>
	<dt>2. <?=$e($cha->conduct_name_2)?></dt><dd><?=$em($cha->conduct_2)?></dd>
	<dt>3. <?=$e($cha->conduct_name_3)?></dt><dd><?=$em($cha->conduct_3)?></dd>
</dl>
<p class="info">Conducts are worth +5 points each, to a maximum of half your score from milestones, rounded down. (So if you achieve 4 milestones (20 points) you can earn up to 10 points from conduct bonuses.)</p>

<h3>Bonus challenges</h3>
<dl>
	<dt>1. <?=$e($cha->bonus_name_1)?></dt><dd><?=$em($cha->bonus_1)?></dd>
	<dt>2. <?=$e($cha->bonus_name_2)?></dt><dd><?=$em($cha->bonus_2)?></dd>
</dl>
<p class="info">Bonus challenges are worth one star each, similar to banners in Crawl tournaments.</p>

<h3>Milestones</h3>
<ul>
	<li>Reach XL3.
	<li>Enter Lair, Orc, or Depths.
	<li>Reach the bottom of Dungeon, Lair, or Orc.
	<li>Collect your first rune.
	<li>Find the entrance to Zot. (Just using magic mapping doesn't count.)
	<li>Collect your third rune.
	<li>Win the game.
</ul>
<p class="info">The main way to score points. +5 points each, and can be done in any order.</p>


</div>
