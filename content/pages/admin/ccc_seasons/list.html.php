<?php $this->layout = 'admin'; ?>
<?php
use app\models\CCCSeasons;
?>

<h2>CCC Seasons</h2>

IMPORTANT ADMIN NOTES: 
<p>- This list is for the 2027 rework of CCC Seasonal Challenges.</p>
<p>- Use the link at the top of the page to create NEW CCC Seasons Challenges.</p>
<p>- Each year will be broken into 4 seasons with roughly challenges per season (so we aim to make at least 36 challenges here).</p>
<p>- Each challenge starts at "Season 1 Week 1" until filled with 9 weeks, then "Season 2" restarts at week 1.</p>
<p>- DO NOT set any challenge to ACTIVE; keep them as DRAFTS until we are ready to launch this in 2027.</p>
<p>- Enjoy yourselves; be creative with naming, challenges, etc! Ask in Discord with questions regarding balance + How-To.</p>

<br>

<table class="challenges_list bordered">
	<thead>
		<tr>
			<th>Y.S.W</th>
			<th>Icon</th>
			<th>Name</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
	<?php
		// Supports limit and offset as first and second params for pagination, first param includes drafts
		$seasons = CCCSeasons::findBySeasons(true, 150, 0);
		$r = 0;
		foreach ($seasons as $c) :
	?>

		<tr class="<?=$r++%2==0?'odd':'even'?> <?=($c->active)?'active':''?>">
			<td><?=$c->year?>.<?=$c->season?>.<?=$c->week?></td>
			<td><a href="/ccc/seasonchallenge.html?id=<?=$c->id?>"><img src="<?=$c->icon?>" /></a></td>
            <td class="actions-td">
			<a href="/ccc/seasonchallenge.html?id=<?=$c->id?>"><?=$c->name?></a>
				<br /><?=($c->species), ", ", ($c->background), ", ", ($c->gods)?>
			</td>
			<td class="actions-td">
				<a href="/admin/ccc_seasons/edit?id=<?=$c->id?>">Edit</a>
			</td>
		</tr>

	<?php
		endforeach;
	?>
	</tbody>
</table>
