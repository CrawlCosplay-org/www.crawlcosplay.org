<?php $this->layout = 'ccc'; ?>
<?php

use app\models\CCCSeasons;

$id = $_GET['id'] ?? false;
if ($id == false) {
	return $this->request->redirect('/');
}

$season = CCCSeasons::get($id);
if (!$season) {
	return $this->request->redirect('/');
}

$is_admin = $this->request->session('admin');
if ($season->draft && !$is_admin) {
	return $this->request->redirect('/');
}

$name = $e($season->name);
$this->setData("page_title", "{$name} - Crawl Cosplay Challenge");

$this->setData("meta", ['filename' => $season->icon]);

?>
<div class="challenge">

<h2>Year <?=$e($season->year)?> Season <?=$e($season->season)?> Week <?=$e($season->week)?> : <?=$e($season->name)?></h2>
<p style="font-style: italic; color: #777;"><?=$e($season->description)?></p>

<!-- shortform field is used for Monster Speak -->
<p style="text-align:right;"><span style="font-size: smaller"><?=$e($season->shortform)?></span></p>

<?php if ($season->icon) : ?>
	<img src="<?=$e($season->icon)?>" style="height:192px; width:auto; image-rendering:pixelated;" />
<?php endif; ?>

<p>
	<?php if ($season->wiki): ?>
		<a href="<?=$e($season->wiki)?>">Wiki page</a>
	<?php endif; ?>
</p>

<h2>Challenge Details</h2>
<table class="table_for_layout">
	<tr><th>Species</th><th>Background</th><th>Gods</th></tr>
	<tr>
		<td><?=$e($season->species)?></td>
		<td><?=$e($season->background)?></td>
		<td><?=$e($season->gods)?></td>
	</tr>
</table>

<p class="info">The Species, Background, and God choices are all mandatory. You must be worshipping one of the gods listed above before entering Lair, Orcish Mines, the Vaults or Depths, unless this isn't possible in which case you must worship them as soon as you can. Don't use faded altars (except in challenges where you can choose any god), and don't do anything to lose your religion unless otherwise specified.</p>

<p class="info">NOTE: Except for Beogh, Ignis, Jiyva and Lugonu, an altar for the other gods will ALWAYS show up by D:10.</p>

<?php if ($season->special_rule) : ?>
<h3>Special Rule or Notes</h3>
<div class="special_rule"><p><?=$em($season->special_rule)?></p></div>
<?php endif; ?>

<h3>Cosplay conduct points</h3>
<dl>
	<dt>1. <?=$e($season->conduct_name_1)?></dt>
	<dd><?=$em($season->conduct_1)?></dd>

	<dt>2. <?=$e($season->conduct_name_2)?></dt>
	<dd><?=$em($season->conduct_2)?></dd>

	<dt>3. <?=$e($season->conduct_name_3)?></dt>
	<dd><?=$em($season->conduct_3)?></dd>
</dl>

<p class="info">Conducts are worth +5 points each, to a maximum of half your score from milestones, rounded down. (So if you achieve 4 milestones (20 points) you can earn up to 10 points from conduct bonuses.) Small mistakes in following conducts will usually be forgiven.</p>

<h3>Bonus challenges</h3>
<dl>
	<dt>1. <?=$e($season->bonus_name_1)?></dt>
	<dd><?=$em($season->bonus_1)?></dd>

	<dt>2. <?=$e($season->bonus_name_2)?></dt>
	<dd><?=$em($season->bonus_2)?></dd>
</dl>

<p class="info">Bonus challenges are worth one star each, similar to banners in Crawl tournaments. Small mistakes will usually be forgiven.</p>

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
