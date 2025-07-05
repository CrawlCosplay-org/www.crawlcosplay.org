<?php $this->layout = 'admin'; ?>
<?php
use app\models\Challenge;

?>
<h2>Challenges</h2>
IMPORTANT ADMIN NOTES: 
<p>- Set# 32 and above are reserved for the Crawl Cosplay Trunk Tournaments (CCTT). So set #32 is for the Trunk v0.32 tournament.</p>
<p>- Set #0 is reserved for Crawl Cosplay Academy(CCA). Do NOT make any of those 12 challenges "Active".</p>
<p>- Set# -1 and lower are reserved for the Crawl Cosplay Forks Tournaments (CCFT).</p>
<p>- This leaves set 1 to 31 reserved for CCC.</p>

<br>
<table class="challenges_list bordered">
	<thead>
		<tr>
			<th>S.W</th>
			<th>Icon</th>
			<th>Name</th>
			<th>#</th>
			<th>Actions</th>

		</tr>
	</thead>
	<tbody>
	<?php
		// Supports limit and offset as first and second params for pagination, first param includes drafts
		$challenges = Challenge::findBySets(true, 150, 0, true);
		$r = 0;
		foreach ($challenges as $c) :
	?>

		<tr class="<?=$r++%2==0?'odd':'even'?> <?=($c->active)?'active':''?>">
			<td><?=$c->setnr?>.<?=$c->week?></td>
			<td><a href="/ccc/challengedetails?id=<?=$c->id?>"><img src="<?=$c->icon?>" /></a></td>
			<td class="actions-td">
				<a href="/ccc/challengedetails?id=<?=$c->id?>"><?=$c->name?></a>
				<br /><?=($c->species), ", ", ($c->background), ", ", ($c->gods)?>
			</td>
			<td><?=$c->subs?></td>
			<td class="actions-td"><a href="/admin/challenges/scores?id=<?=$c->id?>">Scores</a> | <a href="/admin/challenges/edit?id=<?=$c->id?>">Edit</a>
		</tr>

	<?php
		endforeach;
	?>
	</tbody>
</table>
