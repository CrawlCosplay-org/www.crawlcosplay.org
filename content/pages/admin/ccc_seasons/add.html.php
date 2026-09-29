<?php $this->layout = 'admin'; ?>
<?php

if ($data = $this->request->getPostData()) {
	$season = new app\models\CCCSeasons($this->request->getPostData());
	$season->save();
	$this->request->redirect('/admin/ccc_seasons/list.html');
}

?>
<h2>Adding new CCC Season Challenge</h2>
<form method="POST">
	<fieldset>
		<label>
			<span>Name</span><br />
			<input type="text" name="name" placeholder="Name of character to play" />
		</label>
		<br />
		<br />
		<label>
			<span>Description</span><br />
			<textarea name="description" placeholder="Description of the unique or details about the challenge" cols="100" rows="3"></textarea>
		</label>
		<br />
		<br />
		<label>
			<input type="hidden" name="draft" value="0" />
			<input type="checkbox" name="draft" value="1" />
			<span>Draft</span>
		</label>
		<br />
		<br />
		<label>
			<input type="hidden" name="bonus" value="0" />
			<input type="checkbox" name="bonus" value="1" />
			<span>Bonus challenge (won't count for season scoreboard)</span>
		</label>
		<br />
		<br />
		<label>
			<span>Year</span><br />
			<input type="number" name="year" placeholder="2027" required="required" />
		</label>
		<br />
		<label>
			<span>Season</span><br />
			<input type="number" name="season" placeholder="1" required="required" />
		</label>
		<br />
		<label>
			<span>Week</span><br />
			<input type="number" name="week" placeholder="1" required="required" />
		</label>
		<br />
		<br />
		<label>
			<span>Species</span><br />
			<input type="text" name="species" value="" />
		</label>
		<br />
		<br />
		<label>
			<span>Background</span><br />
			<input type="text" name="background" value="" />
		</label>
		<br />
		<br />
		<label>
			<span>God(s)</span><br />
			<input type="text" name="gods" value="" />
		</label>
		<br />
		<br />
		<label>
			<span>Reddit thread</span><br />
			<input type="text" name="reddit" placeholder="https://www.reddit.com/r/dcss/comments/..." />
		</label>
		<br />
		<br />
		<label>
			<span>Wiki URL</span><br />
			<input type="text" name="wiki" placeholder="http://crawl.chaosforge.org/Bai_Suzhen" />
		</label>
		<br />
		<br />
		<label>
			<span>Character icon image</span><br />
			<input type="text" name="icon" placeholder="https://i.imgur.com/kjsdasd.png" />
		</label>
		<br />
		<br />
		<label>
			<span>a Unique's line from crawl/crawl-ref/source/dat/database/monspeak.txt</span><br />
			<!-- the field "shortform" has been reused for this new purpose -->
			<input type="text" name="shortform" placeholder="Margery throws some purple powder towards you." />
		</label>
		<br />
		<br />
		<label>
			<span>Conduct 1 Name</span><br />
			<input type="text" name="conduct_name_1" value="" />
		</label>
		<br />
		<label>
			<span>Conduct 1 Description</span><br />
			<textarea name="conduct_1" cols="100" rows="3"></textarea>
		</label>
		<br />
		<br />
		<label>
			<span>Conduct 2 Name</span><br />
			<input type="text" name="conduct_name_2" value="" />
		</label>
		<br />
		<label>
			<span>Conduct 2 Description</span><br />
			<textarea name="conduct_2" cols="100" rows="3"></textarea>
		</label>
		<br />
		<br />
		<label>
			<span>Conduct 3 Name</span><br />
			<input type="text" name="conduct_name_3" value="" />
		</label>
		<br />
		<label>
			<span>Conduct 3 Description</span><br />
			<textarea name="conduct_3" cols="100" rows="3"></textarea>
		</label>
		<br />
		<br />
		<label>
			<span>Bonus 1 Name</span><br />
			<input type="text" name="bonus_name_1" value="" />
		</label>
		<br />
		<label>
			<span>Bonus 1 Description</span><br />
			<textarea name="bonus_1" cols="100" rows="3"></textarea>
		</label>
		<br />
		<br />
		<label>
			<span>Bonus 2 Name</span><br />
			<input type="text" name="bonus_name_2" value="" />
		</label>
		<br />
		<label>
			<span>Bonus 2 Description</span><br />
			<textarea name="bonus_2" cols="100" rows="3"></textarea>
		</label>
		<br />
		<br />
		<label>
			<span>Special rule</span><br />
			<textarea name="special_rule" cols="100" rows="3"></textarea>
		</label>
		<br />
		<br />
		<input type="submit" name="Save">
	</fieldset>
</form>
