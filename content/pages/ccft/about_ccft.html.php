<?php
    $this->layout = 'ccft';
    $dir_path = "img/uniques";
    $files = scandir($dir_path);
    $count = count($files);
    $index1 = rand(2, ($count-1));
    $index2 = rand(2, ($count-1));
    $index3 = rand(2, ($count-1));
    $index4 = rand(2, ($count-1));
    $filename1 = $files[$index1];
    $filename2 = $files[$index2];
    $filename3 = $files[$index3];
    $filename4 = $files[$index4];
?>

<h2>About Crawl Cosplay Fork Tournament (CCFT)</h2>
<?php    echo '<img src="/'.$dir_path."/".$filename1.'" alt="'.$filename1.'" width="72" height="72" style="float:right">'; ?>

<p>The <b>Crawl Cosplay Forks Tournament</b> is a 4-week long tournament with a different fork each week highlighting some important changes.

<p>Each week will feature a fork's Unique combo for you to play, with one or more options for starting species and background, as well as which god you must worship.
   These are all mandatory and you must worship your god before entering any multi-level branch besides the dungeon (e.g. Lair, Orc, Depths). 
   Also, you can't do anything to lose your religion, including abandoning your god. 
   The goal is not to emulate the unique's behaviour exactly, but to get a good starting point on playing games with interesting requirements.</p>
<p>In the event of a tie for top spot, the highest score for the last week will be used to determine the winner.</p>

<p>A week's challenge details will be posted on Reddit at the time of the start on Friday at 00:00 UTC.</p>

<h3>Featured Forks</h3>
<ul>
    <li><a href="/ccft/fork_bcadren.html">BCadren</a></li>
    <li><a href="/ccft/fork_bcrawl.html">BCrawl</a></li>
    <li><a href="/ccft/fork_stoatsoup.html">Stoat Soup</a></li>
    <li><a href="/ccft/fork_stonesoup.html">Stone Soup (DCSS)</a></li>
</ul>

<h3>How to Enter</h3>
<?php
    echo '<img src="/'.$dir_path."/".$filename2.'" alt="'.$filename2.'" width="72" height="72" style="float:right">'; 
?>

<p>The current challenge's details page has a "Submit a CCFT run" link.
    For those who haven't participated in CCC, CCA or CCTT before, just paste a link to your online morgue in the appropiate field,
    and enter your playername and score in the comments. 
    Submissions will be approved by a moderator, who will verify your morgue and your score, and create a username for your future submissions.</p>

<h3>Restrictions for tournament play</h3>
<ul><li><b>Submitted runs must be played on online servers. See the <a href="https://www.dungeoncrawlcentral.org/online_servers.html" target="_blank">Dungeon Crawl Central</a> website for details.</b>
    <li><b>Seeded games are NOT accepted.</b></li>
    <li>You can make as many attempts as you want.</li>
    <li>Morgues must be submitted while a challenge is active. No late submissions!</li>
    <li>Small mistakes will likely NOT be forgiven unless they are minute. (If you are unsure please ask ASAP if you are forgiven in our CC Discord linked above.)</li>
</ul>

<h3>Changing your player sprite to a Unique's doll.</h3>
<p>If you'd like to change the player doll to the Unique's tile, add the following to your RC file:</p>
<p> tile_player_tile = tile:MONS_WIGLAF</p>

<h3>Full Rules and Scoring</h3>
<?php
    echo '<img src="/'.$dir_path."/".$filename3.'" alt="'.$filename3.'" width="72" height="72" style="float:right">'; 
?>
<h4>Milestones</h4> 
<p>These remain the same from one week to the next, and are the main way of scoring points.
  Each milestone earns you 5 points, for a total of 35 if you win the game, and can be done in any order.
  You can only earn points from each milestone once per game (entering Lair and then Orc doesn't get you 10 points).</p>

<ul><li>Reach XL3.</li>
<li>Enter Lair, Orc, or Depths.</li>
<li>Reach the last floor of Lair, Orc, or the Dungeon.</li>
<li>Collect your first rune.</li>
<li>Find the entrance to Zot. (Just using magic mapping doesn't count; you must actually have the entrance in LOS.)</li>
<li>Collect three runes.</li>
<li>Win the game.</li></ul>

<h4>Conducts</h4>
<p>Each week will have three optional conducts for you to follow, that vary with every challenge; some are quite easy while others will be more difficult.
  Following or completing each conduct is worth 5 points, to a maximum of half your score from milestones, rounded down.
  So if you achieve 3 milestones (15 points), you can earn up to 7 points from conduct bonuses, for a total of 22 points.</p>

<h4>Bonus Challenges</h4>
<?php
    echo '<img src="/'.$dir_path."/".$filename4.'" alt="'.$filename4.'" width="72" height="72" style="float:right">'; 
?>
<p>Each of the 4 challenges will also have two <em>optional</em> bonus challenges, that are usually difficult or unconventional.
  Completing a bonus challenge doesn't affect your score, but gets you a <strong>star</strong>, a small trophy to recognize your skill.
  Each week has one star that requires you to win the game or is to be done on the Orb rune, and another that doesn't. 
  You don't need to follow any of the cosplay conducts to earn stars.</p>

<h3>Credits</h3>

<p>The Crawl Cosplay Fork Tournament was created and is run by <a href="https://www.reddit.com/user/RoGGa_69/" target="_blank">RoGGa</a>
  with the help from our Crawl Cosplay @VIPs in our CC Discord server.</p>
<p>The CCFT part of the website was created by copying the CCTT format.</p>
<p>See the <a href="/ccc/about_ccc">CCC about page</a> for more detailed credits about the original website live since 2019.</p>
