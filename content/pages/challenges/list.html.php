<?php
use app\models\Challenge;

if (!$this->request->session('admin')) {
	$this->request->redirect('/');
}

?>
<h2>Challenges</h2>
<table class="challenges_list">
	<thead>
		<tr>
			<th>&nbsp;</th>
			<th>Name</th>
			<th>Set</th>
			<th>Week</th>
			<th>Background</th>
			<th>Gods</th>
			<th>Species</th>
			<th>Submissions</th>
			<th>Scores</th>
			<th>Actions</th>
			
		</tr>
	</thead>
	<tbody>
	<?php
		$challenges = Challenge::findBySets();
		foreach ($challenges as $c) :
	?>
		
		<tr <?=($c->active)?'style="active"':''?>>
			<td><?php if ($c->icon):?><img src="<?=$c->icon?>" /><?php endif; ?> </td>
			<td><a href="<?=$c->reddit?>"><?=$c->name?></a></td>
			<td><?=$c->setnr?></td>
			<td><?=$c->week?></td>
			<td><?=$c->background?></td>
			<td><?=$c->gods?></td>
			<td><?=$c->species?></td>
			<td><?=$c->subs?></td>
			<td><a href="/challenges/scores?id=<?=$c->id?>">See scores</a></td>
			<td><a href="/challenges/edit?id=<?=$c->id?>">Edit</a></td>
		</tr>

	<?php
		endforeach;
	?>
	</tbody>
</table>
