<?php $this->layout = 'ccft'; ?>
<?php
use app\models\{Challenge, Submission, Player};
$active = Challenge::ccftActive();
if ($active) :
	$set = $active->setnr;
	$scores = Player::scoreboardForSet($set);
	$challenges_in_set = Challenge::findAsArray(['setnr' => $set, 'draft' => 0], ['order' => '`week` ASC']);
	$weeks = sizeof($challenges_in_set);
	$this->setData("meta", ['filename' => $active->icon]);
?>
<h2>CCFT#<?=abs($e($active->setnr))?> Week <?=$e($active->week)?>: <a href="/ccft/fchallengedetails?id=<?=$e($active->id)?>"><?=$e($active->name)?></a>
	<?php if ($active->icon): ?><img src="<?=$e($active->icon)?>" class="head-icon" height="30px" /> <?php endif; ?>
</h2>
<p style="font-style: italic; color: #777;"><?=$e($active->description)?></p>
<!-- shortform field is used for Monster Speak -->
<p style="text-align:right;"><span style="font-size: smaller"><?=$e($active->shortform)?></span></p>

<p>  <a href="/ccft/fchallengedetails?id=<?=$e($active->id)?>">Challenge details</a>  
   | <a href="/ccft/submit_ccft?id=<?=$e($active->id)?>">Submit a CCFT run</a> 
   | <a href="https://discord.gg/pW7nqC8Wu3">Discuss it in CC's Discord</a> 
   | <a href="<?=$e($active->reddit)?>">Fork details</a>
   | Next challenge starts on Friday 00:00 UTC.</p>
<table class="table_for_layout">
	<tr><th>Species</th><th>Background<th>Gods</th></tr>
	<tr><td><?=$e($active->species)?></td><td><?=$e($active->background)?></td><td><?=$e($active->gods)?></td></tr>
</table>
<?php if ($active->special_rule) : ?>
<div class="special_rule"><p><?=$em($active->special_rule)?></p></div>
<?php endif; ?>

<img src="/img/HR-right.png"><br />

<!-- disable video news bar for now
<!-- ?php echo $this->part('latest_twitch'); ?> -->

<h2>Scoreboard for CCFT#<?=abs($e($active->setnr))?></h2>
<table class="set-list">
<?php
	$made_seperator = false;
	foreach ($challenges_in_set as $cha) :
		if ($cha->bonus && !$made_seperator) {
			echo '<tr><td colspan="3">&nbsp;</td></tr>';
			$made_seperator = true; // only make one seperator if multiple bonuses
		}
?>
	<tr>
		<td>Week <?=$e($cha->week)?>.</td>
		<td><?php if ($cha->icon):?><img src="<?=$e($cha->icon)?>" style="height: 1em" /><?php endif; ?>
		    <b><a href="/ccft/fchallengedetails?id=<?=$e($cha->id)?>"><?=$e($cha->name)?></a></b></td>
		<td><span style="font-size: smaller"><?=$e($cha->species), ", ", $e($cha->background), ", ", $e($cha->gods)?></td>
		
	</tr>
<?php endforeach; ?>
</table>

<table class="bordered">
	<tr>
		<th>Player</th>
		<th>Total <span class="star">&#9733;</span></th>
		<?php
		$made_seperator = false;
		foreach ($challenges_in_set as $c) {

			if ($c->bonus && !$made_seperator) {
				echo '<th rowspan="' . (sizeof($scores) + 1) . '">&nbsp;</th>';
				$made_seperator = true; // only make one seperator if multiple bonuses
			}
			echo '<th>' . $e($c->week) . '. ';
			if ($cha->icon) echo '<a href="/ccc/challengedetails?id='.$c->id.'"><img src="'.$e($c->icon).'" style="height: 1.5em" /></a>';
			echo "</th>";
		}
		?>
	</tr>
	<?php
	foreach ($scores as $i => $row) : ?>
		<tr class="<?=$i%2==0?'odd':'even'?>">
			<td><a href="/player?id=<?=$e($row['pid'])?>"><?=$e($row['player'])?></a></td>
			<td><?=$e($row['total'])?> <?=$e($row['stars'])?><span class="star">&#9733;</span></td>
			<?php

			foreach ($challenges_in_set as $c) {
				$week = $row['week'][$c->id];
				if ($week == null) {
					echo "<td>&nbsp;</td>";
					continue;
				}
				$out = '<td>';
				if (!empty($week['morgue'])) $out .= '<a href="'. $e($week['morgue']) .'" target="_blank">';
				$out .= $week['score'];
				for ($i=0; $i < (int) $week['stars'] ; $i++) {
					$out .= '<span class="star">&#9733;</span>';
				}
				if (!empty($week['morgue'])) $out .= '</a>';
				echo $out . "</td>";
			}
			for ($i=0; $i < $weeks - sizeof($row['week']); $i++) {
				echo "<td>&nbsp;</td>";
			}
			?>
		</tr>
	<?php endforeach; ?>
</table>


<?php else : // no active challenge
	echo '<h2 style="color:rgb(69, 136, 5);">Welcome to the <b>Crawl Cosplay Forks Tournament</b> (CCFT)</h2>';
	echo '<!-- <p><a href="/ccft/past_ccft_results.html">Past CCFTournaments Results</a>.</p> -->';
  echo '<h4>Next Tournament</h4>';
  echo '<p>Starts June 6th 2025 @ 00:00 UTC.</p>';
  echo '<h2>General Rules, Scoring and Credits</h2>';
  echo 'See the <a href="/ccft/about_ccft.html">CCFT About page</a>.';
	echo "<br>";
endif;
?>
