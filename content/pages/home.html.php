<?php
	$dir_path = "img/titles";
	$files = scandir($dir_path);
	$count = count($files);
	$index = rand(2, ($count-1));
	$filename = $files[$index];
	$index2 = rand(2, ($count-1));
	$filename2 = $files[$index2];

echo	"<h2>Welcome to Crawl Cosplay: Learn, Compete, Improve!</h2>";

echo "<p>" . _("We're a community built around creative challenges, learning, and improving at Dungeon Crawl Stone Soup and its many forks. 
Whether you're taking your first steps into the dungeon or looking for a fresh challenge after hundreds of wins, Crawl Cosplay is a welcoming place to learn, share your adventures, and grow alongside other players.") . "</p>";

echo "<p>" . _("Our mission is simple: help more people enjoy DCSS. Through guides, tournaments, discussions, and carefully designed challenges, we aim to be the community's 
launchpad — a place where new players build confidence, experienced players push their limits, and everyone discovers new ways to play.") . "</p>";

echo	'<img src="/img/cc_stone_soup_icon-512x512.png"  width="350"  style="float:right">';
?>
<br>
<?php
	echo "<h2>" . _("Find Your Next Adventure:") . "</h2>";
	echo "<p>" . _("Choose the path that best matches where you are in your Crawl journey - from your first victory to your next great challenge!") . "</p><br>";
#	echo '<img src="'.$dir_path."/".$filename2.'" alt="'.$filename2.'"  width="350"  style="float:right">';

	echo '<h3><a href="/cca">' . _("Crawl Cosplay Academy") . "</a> (CCA)</h3>";
	echo "<ul><li>" . _("For those new to DC or still trying to get the first few wins.") . "</li>";
	echo     "<li>" . _("Read") . ' <a href="/cca/about_cca">' . _("About CCA") . "</a></li></ul><br>";

	echo '<h3><a href="/ccc">' . _("Crawl Cosplay Challenges") . "</a> (CCC)</h3>";
	echo "<ul><li>" . _("Looking for a real challenge? Get a new one each week!") . "</li>";
   	echo     "<li>" . _("Read") . ': <a href="/ccc/about_ccc">' . _("About CCC") . "</a>.</li>";
   	echo     "<li>" . _("See") . ': <a href="/ccc/all_ccc_history">' . _("all 100+ challenges") . "</a></li></ul><br>";

	echo '<h3><a href="/ccft">' . _("Crawl Cosplay Forks Tournament") . "</a> (CCFT) </h3>";
	echo "<ul><li>" . _("A tournament lasting 5 weeks with each week highlighting a different Dungeon Crawl fork.") . "</li>";
	echo     "<li>" . _("Read ") . ' <a href="/ccft/about_ccft">' . _("About CCFT") . "</a>.</li>";
	echo     "<li>" . _("CCFT#2 will start on July 31.") . ' <img src="/img/misc/arrow-flashing-left.gif" width="40" height="10">' . "</li>";
	echo     "<li>" . _("For information about the 5 Dungeon Crawl forks taking part in CCFT#2, see")  . ': <a href="https://www.dungeoncrawlforks.org/forks_details" target="_blank">' . _("Dungeon Crawl Forks") . "</a>.</li></ul><br>";

echo '
<div style="width:80%; max-width:800px; margin:0 auto;">
    <div style="position:relative; padding-bottom:56.25%; height:0; overflow:hidden;">
        <iframe
            src="https://www.youtube.com/embed/awknvDg5gGM"
            title="Crawl Cosplay Forks Tournament"
            style="position:absolute; top:0; left:0; width:100%; height:100%; border:0;"
            allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen>
        </iframe>
    </div>
</div><br>';

	echo "<h2>" . _("Look Forward to Future Tournaments!") . "</h2>";
	echo '<h3><a href="/cctt">' . _("Crawl Cosplay Trunk Tournament") . "</a> (CCTT)</h3>";
   	echo "<ul><li>" . _("A DCSS tournament lasting about a month with each week highlighting some of the latest Trunk changes.") . "</li>";
   	echo     "<li>" . _("Read") . ': <a href="/cctt/about_cctt">' . _("About CCTT") . "</a>.</li>";
	echo     "<li>" . _("CCTT#4 for trunk v0.35 will likely start in October 2026.");
   	echo 	 '<li><a href="/cctt/past_cctt_results">' . _("Past CCTT Results") . "</a></li></ul><br>";

	echo '<h3><a href="/ccsdt">' . _("Crawl Cosplay Sudden Death Tournament") . '</a> (CCSDT)</h3>';
	echo "<ul><li>" . _("A DCSS tournament lasting 5 weeks with a different Crawl Unique combo to play each week. You only get one try!") . "</li>";
	echo     "<li>" . _("CCSDT#4 will likely start in January or February 2027.") . "</li></ul><br>";

	echo '<img src="'.$dir_path."/".$filename2.'" alt="'.$filename2.'"  width="350"  style="float:right">';

	echo "<h2>" . _("Watch a video about the Crawl Cosplay website") . "</h2>";
	echo "<p>" . _("For in depth details about the Crawl Cosplay website, see:")
		   . ' <a href="https://www.youtube.com/watch?v=x7u0PwESwPM&t=39s" target="_blank">' . _("Dumalacath's CC video") . "</a></p><br>";

	echo "<h2>" . _("Want to chat?") . "</h2>";
	echo "<p>" . _("Come chat with us on our DCSS Crawl Cosplay 2.0 community")
	           . ' <a href="https://discord.gg/pW7nqC8Wu3" target="_blank">'
		   . _("Discord server") . '<img src="/img/discord_transparent_border.png" width="18" height="18" ></a> '
		   . _("with over 300 members!") . "</p><br>";
	
	echo "<h2>" . _("Crawl Cosplay Streamers") . "</h2>";
	echo "<p>"  . _("See the") . ' <a href="/cc_streamers"><b>' . _("Crawl Cosplay Streamers") . "</b></a> "  
		    . _("webpage for info on current and past streamers as well as links to their CC playlists.") . "</p><br>";

	echo "<h2>" . _("The Latest News") . "</h2>";
	echo "<p>"  . _("See the") . ' <a href="/news"><b>' . _("Dedicated Webpage") . "</b></a> "  
		    . _("for the latest updates.") . "</p><br>";

    echo "<h2>" . _("Credits") . "</h2>";
	echo "<ul><li>" . _("This website was developed by Alkemann with assistance from RoGGA, Kitchen_Ace, and Draconius.") . "</li>";
	echo "<li>" . _("You are viewing this website ad-free thanks to funding from:")
		   . ' <a href="https://www.youtube.com/@DraconiusHC" target="_blank">' . _("Draconius' YouTube Channel.") . "</a></li>";
	echo "<li>" . _("The majority of the Crawl Cosplay Challenges were originally developed by Kitchen_Ace, founder of Crawl Cosplay, with many others contributing over the years. 
				They are currently maintained by Colgate, MrSquiggles, and Draconius.") . "</li></ul><br>";

	echo "<h3 style='color:orange'>" . _("On behalf of the Crawl Cosplay community, thank you, Rogga, for the years of dedication to Crawl Cosplay") . "!</h3><br>";
?>

<br>
<h3><b><center>Unique Visitors since June 2026</center></b></h3>
<p><center><script type="text/javascript" src="https://counter.websiteout.com/js/2/5/-1/1"></script></center></p>
<br>
