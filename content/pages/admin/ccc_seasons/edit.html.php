<?php $this->layout = 'admin'; ?>
<?php

use app\models\CCCSeasons;

$id = $_GET['id'] ?? false;

if ($id == false) {
	return $this->request->redirect('/admin/ccc_seasons/list.html');
}

$season = CCCSeasons::get($id);
if (!$season) {
	return $this->request->redirect('/admin/ccc_seasons/list.html');
}


if ($data = $this->request->getPostData()) {
	if ($data['active'] == '1' && $season->active == 0) {
		CCCSeasons::deactivateAll();
	}
	$season->save($data);
	return $this->request->redirect('/admin/ccc_seasons/list.html');
}

?>
<h2>Edit: <?=$season->name?></h2>
<form method="POST">
	<fieldset>
		<input type="submit" name="Save">
		<br />
		<br />
		<label>
			<span>Name</span><br />
			<input type="text" name="name" value="<?=$season->name?>" />
		</label>
		<br />
		<br />
		<label>
			<span>Description</span><br />
			<textarea name="description" cols="100" rows="3"><?=$season->description?></textarea>
		</label>
		<br />
		<br />
		<label>
			<input type="hidden" name="draft" value="0" />
			<input type="checkbox" name="draft" value="1" <?=($season->draft)?'checked="checked"':''?> />
			<span>Draft (Drafts are hidden from public)</span>
		</label>
		<br />
		<br />
		<label>
			<input type="hidden" name="active" value="0" />
			<input type="checkbox" name="active" value="1" <?=($season->active)?'checked="checked"':''?> />
			<span>Active (will deactivate currently active)</span>
		</label>
		<br />
		<br />
		<label>
			<input type="hidden" name="bonus" value="0" />
			<input type="checkbox" name="bonus" value="1" <?=($season->bonus)?'checked="checked"':''?> />
			<span>Bonus challenge (won't count for season scoreboard)</span>
		</label>
		<br />
		<br />
		<label>
			<span>Year</span><br />
			<input type="number" name="year" value="<?=$season->year?>" />
		</label>
		<br />
		<br />
		<label>
			<span>Season</span><br />
			<input type="number" name="season" value="<?=$season->season?>" />
		</label>
		<br />
		<br />
		<label>
			<span>Week</span><br />
			<input type="number" name="week" value="<?=$season->week?>" />
		</label>
		<br />
		<br />
		<label>
			<span>Species</span><br />
			<input type="text" name="species" value="<?=$season->species?>" />
		</label>
		<br />
		<br />
		<label>
			<span>Background</span><br />
			<input type="text" name="background" value="<?=$season->background?>" />
		</label>
		<br />
		<br />
		<label>
			<span>God(s)</span><br />
			<input type="text" name="gods" value="<?=$season->gods?>" />
		</label>
		<br />
		<br />
		<label>
			<span>Multipurpose field called: reddit</span>
			<br />- for CCC challenges, this field is used to link to YouTube Video. Enter the appropriate HTML format for the field.
			<br />
			<textarea name="reddit" cols="125" rows="5"><?=$season->reddit?></textarea>
		</label>
		<br />
		<br />
		<label>
			<span>Wiki URL</span><br />
			<input type="text" name="wiki" value="<?=$season->wiki?>" />
		</label>
		<br />
		<br />
		<label>
			<span>Character icon image</span><br />
			<input type="text" name="icon" value="<?=$season->icon?>" />
		</label>
		<br />
		<br />
		<label>
			<span>a Unique's line from <a href="https://github.com/crawl/crawl/blob/master/crawl-ref/source/dat/database/monspeak.txt" target="_blank">monspeak.txt</a></span><br />
			<!-- the field "shortform" has been reused for this new purpose -->
			<input type="text" name="shortform" value="<?=$season->shortform?>" />
		</label>
		<br />
		<br />
		<label>
			<span>Conduct 1 Name</span><br />
			<input type="text" name="conduct_name_1" value="<?=$season->conduct_name_1?>" />
		</label>
		<br />
		<br />
		<label>
			<span>Conduct 1 Description</span><br />
			<textarea name="conduct_1" cols="100" rows="3"><?=$season->conduct_1?></textarea>
		</label>
		<br />
		<br />
		<label>
			<span>Conduct 2 Name</span><br />
			<input type="text" name="conduct_name_2" value="<?=$season->conduct_name_2?>" />
		</label>
		<br />
		<br />
		<label>
			<span>Conduct 2 Description</span><br />
			<textarea name="conduct_2" cols="100" rows="3"><?=$season->conduct_2?></textarea>
		</label>
		<br />
		<br />
		<label>
			<span>Conduct 3 Name</span><br />
			<input type="text" name="conduct_name_3" value="<?=$season->conduct_name_3?>" />
		</label>
		<br />
		<br />
		<label>
			<span>Conduct 3 Description</span><br />
			<textarea name="conduct_3" cols="100" rows="3"><?=$season->conduct_3?></textarea>
		</label>
		<br />
		<br />
		<label>
			<span>Bonus 1 Name</span><br />
			<input type="text" name="bonus_name_1" value="<?=$season->bonus_name_1?>" />
		</label>
		<br />
		<br />
		<label>
			<span>Bonus 1 Description</span><br />
			<textarea name="bonus_1" cols="100" rows="3"><?=$season->bonus_1?></textarea>
		</label>
		<br />
		<br />
		<label>
			<span>Bonus 2 Name</span><br />
			<input type="text" name="bonus_name_2" value="<?=$season->bonus_name_2?>" />
		</label>
		<br />
		<br />
		<label>
			<span>Bonus 2 Description</span><br />
			<textarea name="bonus_2" cols="100" rows="3"><?=$season->bonus_2?></textarea>
		</label>
		<br />
		<br />
		<label>
			<span>Special Rule or Notes</span><br />
			<textarea name="special_rule" cols="100" rows="3"><?=$season->special_rule?></textarea>
		</label>
		<br />
		<br />
		<input type="submit" name="Save">
	</fieldset>
</form>
