<?php $this->layout = 'dcf'; ?>
<?php 
echo '<div class="login_placeholder">' . 
        '<div id="login" align="right">' .
         '<br>';
echo '<span id="login_message">' . _("Logged in as:") . " xxxxx </span>" . 
     '<a id="chem_link" href="javascript:" style="">' . _("Change Email") . "</a> " .
     '<a id="chpw_link" href="javascript:" style="">' . _("Change Password") . "</a> " .
     '<a id="logout_link" href="javascript:" style="">' . _("Logout") . "</a></span>";
echo     '</div>';

echo "<h2>" . _("Welcome to the lobby for the CCO WebTiles server") . "</b>!</h2>";

echo "<p>" . _("By playing on this CCO WebTiles server you agree to our") . ' <a href="/cco/code_of_conduct">' . _("Code of Conduct") . "</a>.<br></p>";
echo "<p>" . _("Please join our") . ' <a href="https://discord.gg/pW7nqC8Wu3" target="_blank">' . _("DCSS Crawl Cosplay 2.0 community discord server") . "</a>.</p>";
echo "<p>" . _("CCO Admins (Quinn & RoGGa) may be contacted via email at:") . "<b>cco @ crawlcosplay . org</b></p>";
echo "<p>" . _("For quick account or server issues, please contact Quinn or RoGGa in the CC discord server linked above.") . "</p>";

echo "<h2>" . _("Hello") . ", <b>xxxxx</b>!</h2>";
echo "<p>"  . _("Games on this server are recorded for in-progress viewing and playback. Chat messages might be logged, depending on the player's settings.") . "</p>";

echo "<h4>" . _("Play Now") . "</h4>";
echo "<p>DCSS 0.32 (" . _("Latest Stable Version") . ") (edit rc) | Custom seed 0.32 | Sprint 0.32 | Tutorial 0.32</p>";
echo "<p>DCSS trunk (" . _("Updates Daily") . ") (edit rc) | Trunk custom seed | Sprint trunk | Tutorial trunk</p>";
?>

<details style="display:inline-block;"><summary style="cursor:pointer; -webkit-user-select:none; -moz-user-select:none; -ms-user-select:none; user-select:none;">Click to see older versions</summary>
<a href="https://crawl.dcss.io/#play-dcss-0.31">DCSS 0.31</a>
<a href="javascript:" class="edit_rc_link" data-game_id="dcss-0.31">(edit rc)</a>
<span> | <a href="https://crawl.dcss.io/#play-seeded-0.31">Custom seed 0.31</a></span>
<span> | <a href="https://crawl.dcss.io/#play-spr-0.31">Sprint 0.31</a></span>
<span> | <a href="https://crawl.dcss.io/#play-tut-0.31">Tutorial 0.31</a></span>
<span><br><a href="https://crawl.dcss.io/#play-dcss-0.30">DCSS 0.30</a>
<a href="javascript:" class="edit_rc_link" data-game_id="dcss-0.30">(edit rc)</a></span>
<span> | <a href="https://crawl.dcss.io/#play-seeded-0.30">Custom seed 0.30</a></span>
<span> | <a href="https://crawl.dcss.io/#play-spr-0.30">Sprint 0.30</a></span>
<span> | <a href="https://crawl.dcss.io/#play-tut-0.30">Tutorial 0.30</a></span>
</details>
<br><br>
<details style="display:inline-block;"><summary style="cursor:pointer; -webkit-user-select:none; -moz-user-select:none; -ms-user-select:none; user-select:none;">Click to see DCSS Forks</summary>
<a href="https://crawl.dcss.io/#play-dcss-0.31">DCSS 0.31</a>
</details></span></div>
<br>
<p>View: <a href="/cco/livegames">games currently being played LIVE on this server</a>.</p>
