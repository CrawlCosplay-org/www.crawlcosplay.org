
<?php $this->layout = 'cca';
use app\models\{Challenge, Submission, Player};

 	$set = 0;  //set #0 is reserver for the CCA's 12 challenges
	$scores = Player::scoreboardForSet($set);
	$challenges_in_set = Challenge::findAsArray(['setnr' => $set, 'draft' => 0], ['order' => '`week` ASC']);
	$weeks = sizeof($challenges_in_set);
?>
<h2 style="color:rgb(69, 136, 5);">Welcome green Crawler!</h2>
<h3>Ready?</h3>
<p>Whether you're chasing your first win, or simply looking for a fresh way to experience DCSS, Crawl Cosplay Academy offers a guided introduction to several major build archetypes.</p>
<p>Want to know more before jumping in? Read the <a href="/cca/about_cca">About CCA</a> webpage.</p>
<p>Want to see a run in action? See: </p>
	- <a href="https://www.youtube.com/playlist?list=PLY2Drf0NBvk-TkLUf7T9Ceg_HtoPwOHtT" target="_blank">Particleface's CCA tutorial videos</a>.</br>
	- <a href="https://www.youtube.com/playlist?list=PLy8-17WgznzHupXlm9SbgNVWfeF46Yvku" target="_blank">Dumacalath's CCA videos</a>.</br>
<h3>Set...</h3>
<p>If you have never won a game, your first Academy goal is to play one of the <b>"Strength Brutes"</b> listed below since they are considered to be the
<b><i>easier</i></b> combos of the easiest category.</p>
<p>Just click on one of the Uniques for the challenge's details listing the suggested 3 conducts and 2 bonuses.</p>

<h4><b>Strength Brutes</b></h4>
  <p>Focus primarily on the Strength attribute, prefer to wear heavy armor, and typically spend most of their time in melee combat. Skills to focus on are your primary weapon, fighting, armour, shields, throwing, evocations, and invocations (if applicable).</p>
  <p> <a href="/cca/achallengedetails?id=926" title="Snorg, the Troll Berserker (TrBe)"><img src="/img/uniques/Snorg.png" width="72" height="72"></a> 
      <a href="/cca/achallengedetails?id=927" title="Asterion, the Minotaur Fighter (MiFi)"><img src="/img/uniques/Asterion.png" width="72" height="72"></a> 
      <a href="/cca/achallengedetails?id=928" title="Wiglaf, the Mountain Dwarf Gladiator (MDGl)"><img src="/img/uniques/Wiglaf.png" width="72" height="72"></a></p>
<h4><b>Dexterity based</b></h4>
  <p>Focus primarily on the Dexterity attribute, prefer to wear medium armor, and can fight up close or at range. Skills to focus on are your primary weapon, fighting, dodging, stealth, hexes, evocations, and invocations (if applicable).</p>
  <p> <a href="/cca/achallengedetails?id=929" title="Donald, the Merfolk Monk (MfMo)"><img src="/img/uniques/Donald.png" width="72" height="72"></a>
      <a href="/cca/achallengedetails?id=930" title="Sonja, the Kobold Brigand (KoBr)"><img src="/img/uniques/Sonja.png" width="72" height="72"></a>
      <a href="/cca/achallengedetails?id=931" title="Vashnia, the Naga Hunter (NaHu)"><img src="/img/uniques/Vashnia.png" width="72" height="72"></a> </p>
<h4><b>Mages: Mostly Magic</b></h4>
  <p>Focus primarily on the Intelligence attribute, prefer to wear light armor (robes in particular), and often but not always fight at long range. Skills to focus on are roughly two magic school(s), spellcasting, fighting, stealth, evocations, and invocations (if applicable).</p>
  <p> <a href="/cca/achallengedetails?id=935" title="Frederick, the Demigod Conjurer (DgCj)"><img src="/img/uniques/Frederick.png" width="72" height="72"></a>
      <a href="/cca/achallengedetails?id=936" title="Aantaeus, the Oni Ice Elementalist (OnIE)"><img src="/img/uniques/Antaeus.png" width="60" height="72"></a> 
      <a href="/cca/achallengedetails?id=937" title="Roxanne, the Gargoyle Earth Elementalist (GrEE)"><img src="/img/uniques/Roxanne.png" width="72" height="72"></a></p>
<h4><b>Miscellaneous</b></h4>
  <p>An assortment of less straightforward builds including Summoner, Shapeshifter, and Enchanter</p>
  <p> <a href="/cca/achallengedetails?id=932" title="Jeremiah, the Barachi Summoner (BaSu)"><img src="/img/uniques/Jeremiah.png" width="72" height="72"></a> 
      <a href="/cca/achallengedetails?id=933" title="Bai Suzhen, the Draconian Shapeshifter (DrSh)"><img src="/img/uniques/Bai_Suzhen.png" width="72" height="72"></a> 
      <a href="/cca/achallengedetails?id=944" title="The Enchantress, the Spriggan Enchanter (SpEn)"><img src="/img/uniques/the_Enchantress.png" width="72" height="72"></a> </p>
<h3>GO!</h3>
Play a game of your favourite DCSS Uniques and once you have completed one of your better games, <b>Submit your Morgue</b> on the challenge's detail page and a moderator will approve it.
Your best 2 runs for each challenge will be kept in the website's database...so submit as often as you like.
<br>
<h3>Need more help?...or have some questions</h3>
<p>Come chat with us on our <a href="https://discord.gg/pW7nqC8Wu3" target="_blank">Crawl Cosplay Discord server</a> in the <b>#academy_cca</b> text channel.</p>

<h2>Crawl Cosplay Academy Scoreboard</h2>
<table class="bordered">
	<tr>
		<th>Player</th>
		<th style="white-space:nowrap">Total <span class="star">&#9733;</span></th>
		<?php
			foreach ($challenges_in_set as $c) {
				echo '<th title="'.$e($c->name).'">';
				if ($c->icon) echo '<a href="/cca/achallengedetails?id='.$c->id.'"><img src="'.$e($c->icon).'" style="height: 2.5em" /></a>';
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
	<tr>
		<th>Player</th>
		<th style="white-space:nowrap">Total <span class="star">&#9733;</span></th>
		<?php
			foreach ($challenges_in_set as $c) {
				echo '<th title="'.$e($c->name).'">';
				if ($c->icon) echo '<a href="/cca/achallengedetails?id='.$c->id.'"><img src="'.$e($c->icon).'" style="height: 2.5em" /></a>';
				echo "</th>";
			}
		?>
	</tr>
</table>
<br>
