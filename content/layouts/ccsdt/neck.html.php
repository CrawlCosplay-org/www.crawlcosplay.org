<div class="page information">
    <?php echo $this->part('neck_dropdown_menus'); ?>

    <div class="toplinks"><br></div>
    <div class="page_content">
        <div class="heading">
            <h1>Crawl Cosplay Sudden Death Tournament</h1>
            <div class="fineprint"><center>Come chat with us in our <a href="https://discord.gg/pW7nqC8Wu3" target="_blank">Crawl Cosplay 2.0 community discord server<img src="/img/discord_transparent_border.png" width="18" height="18" ></a></center></div>
        </div>
	<div class="fineprint" align="right">
	</div>
        <div class="liner">
			<img src="/img/HR-right.png" style="width:100%; height:auto;><br />
				<br />
				</div>
        <div class="content" onclick="window.location = '/dismiss';">
        <?php if ($msg = $this->request->session()->get('message')) : ?>
            <div class="message"><?=$msg?> <br /><br /><a href="/dismiss">--more--</a></div>
        <?php endif; ?>
        </div>
