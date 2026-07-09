<?php
	$dir_path = "img/titles";
	$files = scandir($dir_path);
	$count = count($files);
	$index = rand(2, ($count-1));
	$filename = $files[$index];
	$index2 = rand(2, ($count-1));
	$filename2 = $files[$index2];

	echo "<h2>" . _("Welcome to Crawl Cosplay; a 5-in-1 website!") . "</h2>";
	echo '<a href="https://www.dungeoncrawlforks.org/forks/chili" target="_blank"><img src="/img/news/DungeonCrawlChiliOrg-YourLogoHere.gif"  width="256" style="float:right" title="Want your logo here? Contant RoGGa!"></a>';
	
	echo '<h3>' . _("<b>BACKGROUND:</b> Dungeon Crawl (DC) is the game with many forks to choose from.") . "</h3>";
	echo '<ul><li><a href="https://www.dungeoncrawlforks.org/" target="_blank">Dungeon Crawl Forks</a> ' . _("is the NEW website with all the details about DC forks currently being developed.") . "</li>";
 	echo	 '<li><a href="https://www.dungeoncrawlforks.org/online_servers.html" target="_blank">' . _("Play online") . "</a>" . _(" on a Webtiles server.") . "</li>";
	echo     '<li><a href="https://www.dungeoncrawlforks.org/download.html" target="_blank">' . _("Download") . "</a>" . _(" an offline version of one of the DC forks.") . "</li>";
	echo     "<li>" . _("Join our ") . '<a href="https://discord.gg/gMnE5JFcB7" target="_blank">' . _("Dungeon Crawl community") . "</a>". _(" discord server; the only place to discuss all DC forks!") . "</li>";
	echo     "<li>" . _("Post to Reddit in ") . '<a href="https://www.reddit.com/r/dungeoncrawl/" target="_blank">' . _("r/dungeoncrawl") . "</a></li></ul>";

	echo "<h2>" . _("The latest NEWS") . "</h2>";
	echo "<ul><li>" . _("A new Dungeon Crawl fork was launched in June and it is named: ") . "<a href='https://www.dungeoncrawlforks.org/forks/chili' target='_blank'><b>Dungeon Crawl Chili</b></a> (DC Chili)</li>";
	echo     "<li>" . _("DC Chili is available to play online on the new Webtiles server <a href='https://dcf.dungeoncrawlforks.org/' target='_blank'><b>Dungeon Crawl Fajita (DCF)</b></a> located in Ontario Canada</li>");
	echo     "<li>" . _("During July, the webtiles server <a href='https://cco.crawlcosplay.org' target='_blank'><b>CCO</b></a> will be moving to a European server in order to reduce monthly costs and will also be hosting Dungeon Crawl Chili.") . "</li>";
	echo     "<li>" . _("Also during July, the leadership of Crawl Cosplay will be handed over progressively from RoGGa to Colgate, with Draconius offering support to Colgate for external PR things, especially for communications with the DCSS team. FYI, the primary purpose of this leadership transfer is to try to acheive the remaining 2026 ojectives for Crawl Cosplay in regards to interactions with the DCSS team.</li>");
	echo 	 "<li>" . _("The ") . '<a href="/ccft">' . _("Crawl Cosplay Forks Tournament") . '</a> (CCFT#2) ' . _("will start in August.") . "</li>";
	echo 	 "<li>" . _("The ") . '<a href="/cctt">' . _("Crawl Cosplay Trunk Tournament") . '</a> (CCFT#2) ' . _("will likely start in October.") . "</li></ul>";

	echo "<h3 style='color:orange'>" . _("Please select your Crawl Cosplay pleasure among the following 5 options") . ":</h3>";

	echo '<img src="'.$dir_path."/".$filename.'" alt="'.$filename.'"  width="350"  style="float:right">';

	echo '<h3><a href="/cca">' . _("Crawl Cosplay Academy") . "</a> (CCA)</h3>";
	echo "<ul><li>" . _("For those new to DC or who haven't yet won a couple of times.") . "</li>";
	echo     "<li>" . _("Read") . ' <a href="/cca/about_cca">' . _("About CCA") . "</a></li></ul>";

	echo '<h3><a href="/ccc">' . _("Crawl Cosplay Challenges") . "</a> (CCC)</h3>";
	echo "<ul><li>" . _("The original weekly challenge posted on Reddit since 2019!") . "</li>";
   	echo     "<li>" . _("Read") . ': <a href="/ccc/about_ccc">' . _("About CCC") . "</a>.</li>";
   	echo     "<li>" . _("See") . ': <a href="/ccc/all_ccc_history">' . _("all 100+ challenges") . "</a></li></ul>";

	echo '<h3><a href="/ccft">' . _("Crawl Cosplay Forks Tournament") . "</a> (CCFT) </h3>";
	echo "<ul><li>" . _("A tournament lasting 4 weeks with each week highlighting a different Dungeon Crawl fork.") . "</li>";
	echo     "<li>" . _("Read ") . ' <a href="/ccft/about_ccft">' . _("About CCFT") . "</a>.</li>";
	echo     "<li>" . _("CCFT#2 is likely to start in August 2026.") . "</li></ul>";

	echo '<h3><a href="/cctt">' . _("Crawl Cosplay Trunk Tournament") . "</a> (CCTT)</h3>";
   	echo "<ul><li>" . _("A DCSS tournament lasting about a month with each week highlighting some of the latest Trunk changes.") . "</li>";
   	echo     "<li>" . _("Read") . ': <a href="/cctt/about_cctt">' . _("About CCTT") . "</a>.</li>";
	echo     "<li>" . _("CCTT#4 for trunk v0.35 will likely start in October 2026.");
   	echo 	 '<li><a href="/cctt/past_cctt_results">' . _("Past CCTT Results") . "</a></li></ul>";

	echo '<h3><a href="/ccsdt">' . _("Crawl Cosplay Sudden Death Tournament") . '</a> (CCSDT)</h3>';
	echo "<ul><li>" . _("A DCSS tournament lasting 5 weeks with a different Crawl Unique combo to play each week. You only get one try!") . "</li>";
	echo     "<li>" . _("CCSDT#4 will likely start in January or February 2027.") . "</li></ul>";

	echo '<img src="'.$dir_path."/".$filename2.'" alt="'.$filename2.'"  width="350"  style="float:right">';

	echo "<h2>" . _("Watch a video about the Crawl Cosplay website") . "</h2>";
	echo "<p>" . _("For indepth details about the Crawl Cosplay website, see:")
		   . ' <a href="https://www.youtube.com/watch?v=x7u0PwESwPM&t=39s" target="_blank">' . _("Dumalacath's CC video") . "</a></p>";

	echo "<h2>" . _("Want to chat?") . "</h2>";
	echo "<p>" . _("Come chat with us on our DCSS Crawl Cosplay 2.0 community")
	           . ' <a href="https://discord.gg/pW7nqC8Wu3" target="_blank">'
		   . _("Discord server") . '<img src="/img/discord_transparent_border.png" width="18" height="18" ></a> '
		   . _("with over 300 members!") . "</p>";
	
	echo "<h2>" . _("Crawl Cosplay Streamers") . "</h2>";
	echo "<p>"  . _("See the") . ' <a href="/cc_streamers"><b>' . _("Crawl Cosplay Streamers") . "</b></a>' "  
		    . _("webpage for info on current and past streamers as well as links to their CC playlists.") . "</p>";
	echo "<p>" . _("Happy Crawling,") . "</p>";
	echo "<p>" . _("from RoGGa your host, and the @VIPs members who all help make this site what it is.") . " :-D</p>";
?>
<h3><b><center>Unique Visitors since June 2026</center></b></h3>
<p><center><script type="text/javascript" src="https://counter.websiteout.com/js/2/5/-1/1"></script></center></p>
<br>
