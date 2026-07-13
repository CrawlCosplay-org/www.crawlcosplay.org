<div class="page information">
        <?php echo $this->part('neck_dropdown_menus'); ?>

    	<div class="toplinks"><br></div>
    	<div class="page_content">
        	<div class="heading">
            		<h1><center><img src="/img/cc_stone_soup_icon-64x64.png" width="48" height="48" /> Crawl Cosplay <img src="/img/cc_stone_soup_icon-64x64.png" width="48" height="48" /></center></h1>
			<div class="fineprint"><center>Come chat with us in our <a href="https://discord.gg/pW7nqC8Wu3" target="_blank">Crawl Cosplay 2.0 community discord server<img src="/img/discord_transparent_border.png" width="18" height="18" ></a></center></div>
        	</div>
		<div class="fineprint" align="right">
   			To be Moderated: <?php echo app\models\Submission::getNumberOfUnscoredSubmissions() ?>
			<?php if ($this->request->session('admin')) : ?>
        			| Subs <a href="/admin/submissions/list">Official</a> <a href="/admin/submissions/moderate">Moderate</a> <a href="/admin/submissions/add">New</a>
	        		| Players <a href="/admin/players/list">List</a> <a href="/admin/players/add">New</a>
    				| Challenges <a href="/admin/challenges/list">List</a> <a href="/admin/challenges/add">New</a>
	        		| <a href="/admin/logout">Logout</a>
			<?php else : ?>
          			| <a href="/backoffice">Admin</a>
			<?php endif; ?>
		</div>
				<br />
			<img src="/img/HR-right.png" style="width:100%; height:auto;><br />
				<br />
			<div class="content" onclick="window.location = '/dismiss';">
        		<?php if ($msg = $this->request->session()->get('message')) : ?>
            			<div class="message"><?=$msg?> <br /><br /><a href="/dismiss">--more--</a></div>
        		<?php endif; ?>
        	</div>
