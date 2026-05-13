<title><h2 style='color:rgb(69, 136, 5);'>About CCSDT#3</h2></title>
    <?php $this->layout = 'ccsdt'; ?>
    <div id="title">
    <h2 id="sdc"><center>CCSDT#3 for DCSS v0.34</center></h2>
    <h2><h2 style='color:rgb(69, 136, 5);'>About CCSDT#3</h2></h2></div><div id="content">
<h3>Rules</h3>
        <ol>
<li>Each weekly challenge consists of playing a randomly chosen 
Crawl Unqiue's starting combo (e.g. Snorg, a TrBe). 
The chosen combo for each week of the competition will streamed on 
Twitch.tv by Platt ( <a href="https://www.twitch.tv/platt_mallar" target="_blank">www.twitch.tv/platt_mallar</a> ) 30 minutes before 
the start of the new week. A spin-the-wheel format will be used to 
make the choices random.</li>
<li>Participants have one week to finish a game using that combo. 
Only milestones recorded during the week (from 00:00 UTC on the 
start date until 00:00 UTC on the end date) will count for scoring.</li>
<li>Your first DCSS v{} game started on an <a href="https://www.dungeoncrawlcentral.org/online_servers.html" target="_blank">DCSS Webtiles server</a>  
during the week will count for scoring. This is the only allowed attempt, subject to rule 4 below.
<li>One redo per week is allowed if your first game ended in death with player
XL < 5 (i.e., no redo once you hit XL 5). The redo game must be started after
the end of the first game (no startscumming!).  The highest CSDC score of the
two games is counted towards your score.</li>
<li>Each challenge has 2 optional bonus challenges in addition to the
race/class combo.</li>
<li>Each challenge includes a list of gods. A point can be earned upon
reaching ****** piety (or on worship with Gozag or Xom) with one of the listed
gods. The point is lost if you ever abandon your god or are excommunicated. If
the combo for the week is a zealot background, god points are only for sticking
with the starting god. If the combo for the week is a demigod, the god point is automatically awarded.</li>
<li>The season consists of 5 challenges total (i.e., 5 different combos). Each
race and background will be selected at most once during the competition.</li>
<li>The final rankings will be tallied at the end of week 5 and represent the
final outcome. The player with the highest CSDC score is the champion.</li>
<li>Tiebreakers are (in order): number of weekly bonus points, highest in-game
score.</li>
<li>Don't play on more than one account. That misses the point.</li>
</ol>

<h2>Scoring</h2>

<p>Scoring is divided into two parts, weekly points assigned to each game
played, and one-time points awarded once per season regardless of how many
games achieve them.</p>

<table class="info"><tr class="head"><th>15 Weekly Points (can be earned each
week)</th><th>Points</th></tr>
<tr><td class="name"> 1. Reach XL 5 ¹</td><td class="pt">1</td></tr>
<tr><td class="name"> 2. Kill a unique</td><td class="pt">1</td></tr>
<tr><td class="name"> 3. Worship one of three gods ¹</td><td class="pt">1</td></tr>
<tr><td class="name"> 4. Reach XL 10 ¹</td><td class="pt">1</td></tr>
<tr><td class="name"> 5. Enter a multi-level branch of the dungeon</td><td class="pt">1</td></tr>
<tr><td class="name"> 6. Reach the end of a multi-level branch (including D)</td><td class="pt">1</td></tr>
<tr><td class="name"> 7. Champion a listed god</td><td class="pt">1</td></tr>
<tr><td class="name"> 8. Collect 1 gem ² (does not need to stay intact) ¹</td><td class="pt">1</td></tr>
<tr><td class="name"> 9. Collect 1 rune</td><td class="pt">1</td></tr>
<tr><td class="name">10. Collect 2 runes ¹</td><td class="pt">1</td></tr>
<tr><td class="name">11. Collect 3 runes</td><td class="pt">1</td></tr>
<tr><td class="name">12. Collect the Orb of Zot</td><td class="pt">1</td></tr>
<tr><td class="name">13. Win</td><td class="pt">1</td></tr>
<tr><td class="name">14. Complete Bonus #1</td><td class="pt">1</td></tr>
<tr><td class="name">15. Complete Bonus #2</td><td class="pt">1</td></tr>
<tr class="head" id="onetime"><th>One-time points (earned once during the tournament)</th><th>Points</th></tr>
<!-- <tr><td class="name">Win a game in under 40,000 turns:</td><td class="pt">2</td></tr> -->
<tr><td class="name">1. Win a game with 15 runes</td><td class="pt">3</td></tr>
<tr><td class="name">2. Clear a Ziggurat</td><td class="pt">4</td></tr>
<tr><td class="name">3. Enter Zot at XL 20 or lower</td><td class="pt">5</td></tr>
<tr><td class="name">4. Win a game without entering lair</td><td class="pt">6</td></tr>
<tr><td class="name">5. Get a rune without using potions or scrolls (aesetic rune)</td><td class="pt">7</td></tr>
</table>
<p>¹ 5 new milestones were created for CCSDT#3 along with 
the removal of 1 one-time point: Win a game in under 40,000 turns</p>
<p>² add the following to your DCSS v0.34 RC file:</p>
<pre>always_show_gems = true
more_gem_info = true</pre>

<h2>Bonuses</h2>
<h3>Bonus 1 list</h3>
<ol>
   <li>Enter a rune branch with all base skills < 11.</li>
   <li>Enter Slime as your second multi-level branch (don't get banished).</li>
   <li>Enter the Temple in less than 4,000 turns.</li>
   <li>Reach the end of Lair at XL &leq; 12.</li>
   <li>Reach the end of Elf before entering a rune branch (excluding getting banished to the Abyss).</li>
   <li>Reach the end of the Depths before entering a rune branch (excluding getting banished to the Abyss).</li>
   <li>Collect a rune before entering Shoals, Snake, Spider, or Swamp.</li>
   <li>Collect a rune before entering Lair.</li>
   <li>Collect a rune before reaching XL17.</li>
   <li><b>NEW</b>: Enter Elf:3 in under 12,000 turns.</li>
   <li><b>NEW</b>: Reach the 10th floor of a Ziggurat.</li>
   <li><b>NEW</b>: Enter the bottom floor of the Orcish Mines before XL11.</li>
   <li><b>NEW</b>: Kill a unique in tree form (using lignification potion).</li>
   <li><b>NEW</b>: Pickup the gem in Crypt:3. (it doesn't need to stay intact)</li>
   <li>Collect two runes without dying twice (felids).</li>
</ol>

<h3>Bonus 2 list</h3>
<ol>
   <li>Collect the golden rune.</li>
   <li>Collect a rune with all base skills < 11.</li>
   <li><s>Get the slimy rune without entering any multi-level branch other than Lair, Slime, and Dungeon (don't get banished).</s><br>
       <b>REPLACED BY</b>: Pickup the rune in Slime:5.</li>
   <li>Collect a rune in less than 15,000 turns.</li>
   <li>Reach the end of the Vaults at XL &leq; 18.</li>
   <li>Kill or slimify Geryon before entering a rune branch (excluding the Abyss).</li>
   <li>Get a rune from Pan before entering any other rune branch (excluding the Abyss).</li>
   <li>Get a rune from Hell before entering any other rune branch (excluding the Abyss).</li>
   <li>Collect at least 5 runes before entering the Depths.</li>
   <li><b>NEW</b>: Collect at least 3 gems. (they don't need to stay intact)</li>
   <li><b>NEW</b>: Exit the Abyss in under 27,000 turns.</li>
   <li><b>NEW</b>: Reach the last level of the Depths without having entered the Lair.</li>
   <li><b>NEW</b>: Kill a Pan Lord unique.</li>
   <li><b>NEW</b>: Kill a Hell Lord unique. (Geryon does not count)</li>
   <li><b>NEW</b>: Pickup the gem in Slime:5. (it doesn't need to stay intact)</li>
   <li>Collect a rune without dying (felids).</li>
</ol>
   
</div>

    <div id="bottomtext"><span class="menu">CCSDT #3 for v0.34</a> : </span>
                         <span class="menu"><a href="index.html">Overview</a> - </span>
                         <span class="menu"><a href="rules.html">Details</a> - </span>
                         <span class="menu">Standings</span>
                         <span class="menuspacer"></span>
                         <span class="menu">Week 1 - </span>
                         <span class="menu">Week 2 - </span>
                         <span class="menu">Week 3 - </span>
                         <span class="menu">Week 4 - </span>
                         <span class="menu">Week 5</span></div>
