<?php $this->layout = 'admin'; ?>
<?php
use app\models\CCCSeasons;
?>

<h2>CCC Seasons</h2>

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
			<td><a href="/ccc/challengedetails?id=<?=$c->id?>"><img src="<?=$c->icon?>" /></a></td>
			<td class="actions-td">
				<a href="/ccc/challengedetails?id=<?=$c->id?>"><?=$c->name?></a>
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
